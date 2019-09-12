<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/8
 * Time: 15:15
 * order status audit
 */
namespace App\Http\Controllers\Table;

use App\Events\NotifyEvent;
use App\Http\Controllers\AliPayTransfer\AliPaySubmit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ReturnPay\AliPayConfig;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Version1_0\Company;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\Order;
use App\IekModel\Version1_0\OrderPay;
use App\IekModel\Version1_0\OrderReturnPay;
use App\IekModel\Version1_0\OrderReturnWealthPay;
use App\IekModel\Version1_0\OrderShip;
use App\IekModel\Version1_0\OrderStatus;
use App\IekModel\Version1_0\OrderStatusHandle;
use App\IekModel\Version1_0\PersonOrder;
use App\IekModel\Version1_0\Reason;
use App\IekModel\Version1_0\Ship;
use App\IekModel\Version1_0\Status;
use Illuminate\Support\Facades\DB;

class OrderAuditController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * check material view
     */
    public function waitProduct($id){
        $order = Order::where(IekModel::CONDITION)
            ->with(['orderProducts.products'=>function($query){
                $query->with('border.materialDefine')
                    ->with('front.materialDefine')
                    ->with('frame.materialDefine')
                    ->with('back.materialDefine')
                    ->with('backFacade.materialDefine')
                    ->with('core.materialDefine');
            }])
            ->find($id);
        $status = Status::where(IekModel::CONDITION)->get();
        return view('admin.order.waitProduct',compact('order','status'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * check and notify
     */
    public function checkMaterial($id){
        $err = new Error();
        $uid = session('login.id');
        $req = request()->all();

        $deal = OrderStatus::where(IekModel::ORDER_ID,$id)
            ->whereHas('status',function($query) use($req){
                $query->where(IekModel::NAME,'waitProduct');
            })
            ->orderBy(IekModel::CREATED,'desc')
            ->first();
        if(!is_null($deal) && $deal->is_current == false){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage("订单已经处理过了!");
            return view('message.formResult',['result'=>$err]);
        }
        if(is_null($req['reason']) || !isset($req['reason']) || empty($req['reason'])){
            $err->setError(Errors::NOT_EMPTY);
            $err->setMessage("请输入审核结果理由!");
            return view('message.formResult',['result'=>$err]);
        }

        DB::beginTransaction();
        try{
            OrderStatus::where(IekModel::ORDER_ID,$id)
                ->whereHas('status',function($query){
                    $query->where(IekModel::NAME,'waitProduct');
                })
                ->where(IekModel::CURRENT,true)
                ->update([
                    IekModel::CURRENT=>false
                ]);
            $reason = new Reason();
            $reason->reason = $req['reason'];
            $reason->type = 'check';
            $reason->open_reason = $req['reason'];
            $checkReason = $reason->existReason($req['reason'],$reason->type,$req['reason']);
            if($checkReason){
                $reason->save();
            }else{
                $reason = Reason::queryReason($req['reason'],$reason->type,$req['reason']);
            }
            $orderStatus = new OrderStatus();
            $orderStatus->order_id = $id;
            $orderStatus->status_id = Status::where(IekModel::NAME,$req['statusName'])->value(IekModel::ID);
            $orderStatus->reason_id = $reason->id;
            $orderStatus->operation_id = $uid;
            $orderStatus->is_current = true;
            $re = $orderStatus->save();
            switch($req['statusName']) {
                case 'waitProduct':
                    //材料不足
                    $params = new \stdClass();
                    $params->action = 'material lack';
                    $params->lang = 'material lack';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = PersonOrder::where(IekModel::ORDER_ID, $id)->value(IekModel::UID);
                    $params->targetId = $orderStatus->id;
                    $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\OrderStatus::class, $params);
                    event(new NotifyEvent($args));
                    break;
                case 'producing':

                    break;
                case 'close':
                    //材料停产
                    $params = new \stdClass();
                    $params->action = 'material halt';
                    $params->lang = 'material halt';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = PersonOrder::where(IekModel::ORDER_ID, $id)->value(IekModel::UID);
                    $params->targetId = $orderStatus->id;
                    $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\OrderStatus::class, $params);
                    event(new NotifyEvent($args));
                    //订单关闭
                    $params = new \stdClass();
                    $params->action = 'closed';
                    $params->lang = 'order closed';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = PersonOrder::where(IekModel::ORDER_ID, $id)->value(IekModel::UID);
                    $params->targetId = $orderStatus->id;
                    $params->reasonId = $reason->id;
                    $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\OrderStatus::class, $params);
                    event(new NotifyEvent($args));
                    //待生产订单审核 关闭订单后退款
                    $order = Order::with(['orderPay'=>function($query){
                        $query->with(['thirdPay'=>function($q){
                            $q->where(IekModel::STATUS,true);
                        }])
                            ->with('wealthPay');
                    }])
                        ->where(IekModel::ID, $id)
                        ->first();
                    if (!is_null($order) && !is_null($order->orderPay)) {
                        $pay = $order->orderPay;
                        if (is_null($pay->thirdPay) && !is_null($pay->wealthPay)) {
                            $return_pay_no = date('Ymd') . time();
                            DB::beginTransaction();
                            try {
                                $orderReturnWealthPay = new OrderReturnWealthPay();
                                $orderReturnWealthPay->person_id = $pay->wealthPay->person_id;
                                $orderReturnWealthPay->fee = $pay->wealthPay->fee;
                                $orderReturnWealthPay->return_pay_no = $return_pay_no;
                                $orderReturnWealthPay->save();

                                $res = OrderReturnPay::isExist($id);
                                if ($res) {
                                    $err->setError(Errors::EXIST);
                                    $err->setMessage('订单已关闭并退款');
                                    return view('message.formResult', ['result' => $err]);
                                }
                                $orderReturnPay = new OrderReturnPay();
                                $orderReturnPay->order_id = $id;
                                $orderReturnPay->pay_id = $orderReturnWealthPay->id;
                                $orderReturnPay->pay_method = 0;
                                $re = $orderReturnPay->save();
                                DB::commit();
                            } catch (\Exception $e) {
                                DB::rollback();
                                $err->setError(Errors::UNKNOWN_ERROR);
                                $err->setMessage($e->getMessage());
                                return view('message.formResult', ['result' => $err]);
                            }
                            //return $this->curd(Errors::OK, Errors::FAILED, '订单关闭并退款！', '退款失败！', $re);
                        }
                        if (!is_null($pay->thirdPay) && is_null($pay->wealthPay)) {
                            //获取配置信息
                            $alipay_config = AliPayConfig::configData();
                            /**************************请求参数**************************/

                            //批次号，必填，格式：当天日期[8位]+序列号[3至24位]，如：201603081000001
                            $date = date('Ymd', time());

                            //退款笔数，必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）
                            $batch_num = 1;

                            //退款详细数据，必填，格式（支付宝交易号^退款金额^备注），多笔请用#隔开
                            $reason = '材料停产';
                            $detail_data = $pay->thirdPay->third_pay_no . '^' . $pay->thirdPay->fee . '^' . $reason;
                            $no = $pay->thirdPay->pay_no;

                            /************************************************************/

                            //构造要请求的参数数组，无需改动
                            $parameter = array(
                                "service" => trim($alipay_config['service']),
                                "partner" => trim($alipay_config['partner']),
                                "notify_url" => trim($alipay_config['notify_url']),
                                "seller_user_id" => trim($alipay_config['seller_user_id']),
                                "refund_date" => trim($alipay_config['refund_date']),
                                "batch_no" => $date . substr(md5($no), 8, 16),
                                "batch_num" => $batch_num,
                                "detail_data" => $detail_data,
                                "_input_charset" => trim(strtolower($alipay_config['input_charset']))
                            );

                            //建立请求
                            $alipaySubmit = new AliPaySubmit($alipay_config);
                            $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认退款");
                            return response($html_text);
                        }
                        if(is_null($pay->thirdPay) && is_null($pay->wealthPay)){
                            $err->setError(Errors::NOT_FOUND);
                            $err->setMessage('订单未支付');
                            return view('message.formResult', ['result' => $err]);
                        }
                    }
                    break;
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'提交成功','提交失败',$re);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * submit accept
     */
    public function checkProduce($id){
        $err = new Error();
        $uid = session('login.id');
        $rea = '生产完毕';
        $deal = OrderStatus::where(IekModel::ORDER_ID,$id)
            ->whereHas('status',function($query){
                $query->where(IekModel::NAME,'producing');
            })
            ->orderBy(IekModel::CREATED,'desc')
            ->first();
        if(!is_null($deal) && $deal->is_current == false){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage("订单已经处理过了!");
            return view('message.formResult',['result'=>$err]);
        }
        DB::beginTransaction();
        try{
            OrderStatus::where(IekModel::ORDER_ID,$id)
                ->whereHas('status',function($query){
                    $query->where(IekModel::NAME,'producing');
                })
                ->where(IekModel::CURRENT,true)
                ->update([
                    IekModel::CURRENT=>false
                ]);
            $reason = new Reason();
            $reason->reason = $rea;
            $reason->type = 'check';
            $reason->open_reason = $rea;
            $checkReason = $reason->existReason($rea,$reason->type,$rea);
            if($checkReason){
                $reason->save();
            }else{
                $reason = Reason::queryReason($rea,$reason->type,$rea);
            }
            $orderStatus = new OrderStatus();
            $orderStatus->order_id = $id;
            $orderStatus->status_id = Status::where(IekModel::NAME,'waitAccept')->value(IekModel::ID);
            $orderStatus->reason_id = $reason->id;
            $orderStatus->operation_id = $uid;
            $orderStatus->is_current = true;
            $re = $orderStatus->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'提交成功','提交失败',$re);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * accept product
     */
    public function acceptProduct(){
        $status = Status::where(IekModel::CONDITION)->get();
        return view('admin.order.accept',compact('status'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * check and accept
     */
    public function accept($id){
        $err = new Error();
        $uid = session('login.id');
        $req = request()->all();
        $deal = OrderStatus::where(IekModel::ORDER_ID,$id)
            ->whereHas('status',function($query){
                $query->where(IekModel::NAME,'waitAccept');
            })
            ->orderBy(IekModel::CREATED,'desc')
            ->first();
        if(!is_null($deal) && $deal->is_current == false){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage("订单已经处理过了!");
            return view('message.formResult',['result'=>$err]);
        }
        if(is_null($req['reason']) || !isset($req['reason']) || empty($req['reason'])){
            $err->setError(Errors::NOT_EMPTY);
            $err->setMessage("请输入审核结果理由!");
            return view('message.formResult',['result'=>$err]);
        }

        DB::beginTransaction();
        try{
            OrderStatus::where(IekModel::ORDER_ID,$id)
                ->whereHas('status',function($query){
                    $query->where(IekModel::NAME,'waitAccept');
                })
                ->where(IekModel::CURRENT,true)
                ->update([
                    IekModel::CURRENT=>false
                ]);
            $reason = new Reason();
            $reason->reason = $req['reason'];
            $reason->type = 'check';
            $reason->open_reason = $req['reason'];
            $checkReason = $reason->existReason($req['reason'],$reason->type,$req['reason']);
            if($checkReason){
                $reason->save();
            }else{
                $reason = Reason::queryReason($req['reason'],$reason->type,$req['reason']);
            }
            switch($req['statusName']){
                case 'waitProduct':
                    $orderStatus = new OrderStatus();
                    $orderStatus->order_id = $id;
                    $orderStatus->status_id = Status::where(IekModel::NAME,'waitProduct')->value(IekModel::ID);
                    $orderStatus->reason_id = $reason->id;
                    $orderStatus->operation_id = $uid;
                    $orderStatus->is_current = true;
                    $re = $orderStatus->save();
                    break;
                case 'waitSend':
                    $orderStatus = new OrderStatus();
                    $orderStatus->order_id = $id;
                    $orderStatus->status_id = Status::where(IekModel::NAME,'waitSend')->value(IekModel::ID);
                    $orderStatus->reason_id = $reason->id;
                    $orderStatus->operation_id = $uid;
                    $orderStatus->is_current = true;
                    $re = $orderStatus->save();
                    break;
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'提交成功','提交失败',$re);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * send goods view
     */
    public function toSend(){
        $company = Company::where(IekModel::CONDITION)->get();
        return view('admin.order.send',compact('company'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * send goods and notify
     */
    public function send($id){
        $err = new Error();
        $uid = session('login.id');
        $req = request()->all();
        $deal = OrderStatus::where(IekModel::ORDER_ID,$id)
            ->whereHas('status',function($query){
                $query->where(IekModel::NAME,'waitSend');
            })
            ->orderBy(IekModel::CREATED,'desc')
            ->first();
        if(!is_null($deal) && $deal->is_current == false){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage("订单已经处理过了!");
            return view('message.formResult',['result'=>$err]);
        }
        DB::beginTransaction();
        try{
            OrderStatus::where(IekModel::ORDER_ID,$id)
                ->whereHas('status',function($query){
                    $query->where(IekModel::NAME,'waitSend');
                })
                ->where(IekModel::CURRENT,true)
                ->update([
                    IekModel::CURRENT=>false
                ]);
            $orderStatus = new OrderStatus();
            $orderStatus->order_id = $id;
            $orderStatus->status_id = Status::where(IekModel::NAME,'waitConfirm')->value(IekModel::ID);
            $orderStatus->operation_id = $uid;
            $orderStatus->is_current = true;
            $re = $orderStatus->save();

            $shipId = OrderShip::where(IekModel::ORDER_ID,$id)->value(IekModel::SHIP_ID);
            Ship::where(IekModel::ID,$shipId)
                ->update([
                    IekModel::NO => $req['no'],
                    IekModel::COST_FEE => $req['costFee'],
                    IekModel::PROVIDER_ID => $req['providerId']
                ]);
            //发货通知
            $params = new \stdClass();
            $params->action = 'sent';
            $params->lang = 'sent';
            $params->fromId = OfficialPerson::notifier();
            $params->toId = PersonOrder::where(IekModel::ORDER_ID,$id)->value(IekModel::UID);
            $params->targetId = $orderStatus->id;
            $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\OrderStatus::class, $params);
            event(new NotifyEvent($args));
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'提交成功','提交失败',$re);
    }

    /**
     * product complete status
     */
    public function complete($id){
        $status = Status::where(IekModel::CONDITION)->get();
        return view('admin.order.complete',compact('status','id'));
    }
}