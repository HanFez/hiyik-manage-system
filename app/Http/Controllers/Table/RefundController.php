<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/9
 * Time: 15:43
 * Refund
 */
namespace App\Http\Controllers\Table;

use App\Events\NotifyEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RefundAPI\AlipayTradeRefundRequest;
use App\Http\Controllers\RefundAPI\AopClient;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\HandleResult;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Notify\OrderRefund;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\Order;
use App\IekModel\Version1_0\OrderPay;
use App\IekModel\Version1_0\OrderReturnPay;
use App\IekModel\Version1_0\OrderReturnThirdPay;
use App\IekModel\Version1_0\OrderReturnWealthPay;
use App\IekModel\Version1_0\OrderStatus;
use App\IekModel\Version1_0\PersonOrder;
use App\IekModel\Version1_0\Reason;
use App\IekModel\Version1_0\RefundHandleResultHandle;
use App\IekModel\Version1_0\RefundRequest;
use App\IekModel\Version1_0\RefundRequestHandle;
use App\IekModel\Version1_0\Status;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * list
     */
    public function refundRequestList(){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $title = request()->input('title');
        $isAudit = request()->input('auditing');
        if(!is_null($title)){
            $no = $this->getOrderByName($title);
        }
        //dd($isAudit);
        switch($isAudit){
            case '1':
                $refund = $this->checked();
                break;
            case '0':
                $refund = $this->waitCheck();
                break;
            case '2':
                $refund = $this->waitRefund();
                break;
            case '3':
                $refund = $this->refunded();
                break;
            default:
                break;
        }

        if(!is_null($title)){
            $refund = $refund->whereIn(IekModel::ORDER_NO,$no);
        }
        $total = $refund->count();
        if(!is_null($take) && !is_null($skip)){
            $refund = $refund->slice($skip,$take);
        }
        $err->setData($refund);
        $err->take = $take;
        $err->skip = $skip;
        $err->total = $total;
        $err->search = $title;
        $err->auditing = $isAudit;
        return view('admin.refund.refundRequestList',['result'=>$err]);
    }

    /**
     * @return mixed
     * wait check
     */
    public function waitCheck(){
        $refund = RefundRequest::whereDoesntHave('refundRequestHandle')
            ->with('reason')
            ->with('order.orderPay')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        $refund = $refund->unique(IekModel::ORDER_ID);
        return $refund;
    }

    /**
     * @return mixed
     * already checked
     */
    public function checked(){
        $refund = RefundRequest::whereHas('refundRequestHandle',
            function($query){
                $query->with('handleResult.reason');
            })
            ->with('reason')
            ->with('order.orderPay')
            ->with('refundRequestHandle.handleResult.reason')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        $refund = $refund->unique(IekModel::ORDER_ID);
        return $refund;
    }

    /**
     * @return mixed
     * agree refund and no refund
     */
    public function waitRefund(){
        $refund = RefundRequest::whereDoesntHave('refundRequestHandle.money')
            ->whereHas('refundRequestHandle.handleResult',function($query){
                $query->where(IekModel::CONDITION)
                    ->where(IekModel::STATUS,true);
            })
            ->with('order.orderPay')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        return $refund;
    }

    /**
     * @return mixed
     * agree refund and refunded
     */
    public function refunded(){
        $refund = RefundRequest::whereHas('refundRequestHandle.money')
            ->whereHas('refundRequestHandle.handleResult',function($query){
                $query->where(IekModel::CONDITION)
                    ->where(IekModel::STATUS,true);
            })
            ->with(['refundRequestHandle.money.returnPay'=>function($query){
                $query->with('wealthPay')
                    ->with('thirdPay');
            }])
            ->with('order.orderPay')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        return $refund;
    }

    /**
     * @param $no
     * @return mixed
     * search request order
     */
    public function getOrderByName($no){
        $orderName = Order::where(IekModel::ORDER_NO,'like','%'.$no.'%')
            ->where(IekModel::ACTIVE,true)
            ->pluck(IekModel::ORDER_NO);
        return $orderName;
    }
    /**
     * refund request order detail
     */
    public function refundDetail($id){
        $err = new Error();
        $detail = RefundRequest::with('reason')
            ->with(['order'=>function($query){
                $query->with(['orderReceiveInformation.receiveInformation'=>function($query){
                    $query->with('name')
                        ->with('address.city')
                        ->with('phone');
                }])
                    ->with('orderStatus.status')
                    ->with(['orderProducts.products'=>function($q){
                        $q->with('productDefine')
                            ->with(['border'=>function($query){
                                $query->with('materialDefine.facade')
                                    ->with('material')
                                    ->with('line');
                            }])
                            ->with(['core'=>function($query) {
                                $query->with('material')
                                    ->with('materialDefine.facade')
                                    ->with('coreHandle')
                                    ->with(['coreContent.content' => function ($q) {
                                        $q->with('corePublication.title.title.description')
                                            ->with('image.norms');
                                    }]);
                            }])
                            ->with(['frame'=>function($query){
                                $query->with('material')
                                    ->with('materialDefine.facade')
                                    ->with('frameHole.shape');
                            }])
                            ->with(['front'=>function($query){
                                $query->with('material')
                                    ->with('materialDefine.facade');
                            }])
                            ->with(['back'=>function($query){
                                $query->with('material')
                                    ->with('materialDefine.facade');
                            }])
                            ->with(['backFacade'=>function($query){
                                $query->with('material')
                                    ->with('materialDefine.facade');
                            }])
                            ->with(['show'=>function($query){
                                $query->with('material')
                                    ->with('show');
                            }])
                            ->with('productThumb.thumb.norm')
                            ->with('postMaker.maker');
                    }])
                    ->with('orderProducts.orderProductVoucher.personVoucher.voucher')
                    ->with('orderShip.ship.company')
                    ->with(['orderPay'=>function($query){
                        $query->with('wealthPay')
                            ->with('thirdPay');
                    }])
                    ->with('orderPersonVoucher.personVoucher.voucher')
                    ->with('personOrder.person.personNick.nick');
            }])
            ->with('refundRequestHandle.handleResult.reason')
            ->where(IekModel::ORDER_ID,$id)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        $err->setData($detail);
        return view('admin.refund.refundDetail',['result'=>$err]);
    }
    /**
     * audit view
     */
    public function checkView(){
        return view('admin.refund.checkView');
    }
    /**
     * save audit
     */
    public function checkRefund($id){
        $err = new Error();
        $uid = session('login.id');
        $rea = request()->input('reason');
        if(is_null($rea) || !isset($rea) || empty($rea)){
            $err->setError(Errors::NOT_EMPTY);
            $err->setMessage('请输入审核理由！');
            return view('message.formResult',['result'=>$err]);
        }
        $status = request()->input('status');
        if(is_null($status)){
            $err->setError(Errors::NOT_EMPTY);
            $err->setMessage('请填写审核状态！');
            return view('message.formResult',['result'=>$err]);
        }
        $checkRefundRequest = RefundRequest::whereHas('refundRequestHandle')->find($id);
        if(!is_null($checkRefundRequest)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('该订单已经审核过了！');
            return view('message.formResult',['result'=>$err]);
        }
        DB::beginTransaction();
        try{
            $reason = new Reason();
            $reason->reason = $rea;
            $reason->type = 'refund';
            $reason->open_reason = $rea;
            $checkReason = $reason->existReason($rea,'refund',$rea);
            if($checkReason){
                $reason->save();
            }else{
                $reason = Reason::queryReason($rea,'refund',$rea);
            }
            $handleResult = new HandleResult();
            $handleResult->status = $status;
            $handleResult->reason_id = $reason->id;
            $handleResult->save();
            $refundRequestHandle = new RefundRequestHandle();
            $refundRequestHandle->refund_request_id = $id;
            $refundRequestHandle->handle_result_id = $handleResult->id;
            $refundRequestHandle->operator_id = $uid;
            $re = $refundRequestHandle->save();

            $to = RefundRequest::with('personOrder')->find($id);
            if($status == 1){
                $params = new \stdClass();
                $params->action = 'apply';
                $params->lang = 'refund apply passed ';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $to->personOrder->person_id;
                $params->targetId = $id;
                $params->reasonId = $reason->id;
                $args = new NotifyEventArguments(null, OrderRefund::class, $params);
                event(new NotifyEvent($args));
            }else if($status == 0){
                $params = new \stdClass();
                $params->action = 'apply';
                $params->lang = 'refund apply failed';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $to->personOrder->person_id;
                $params->targetId = $id;
                $params->reasonId = $reason->id;
                $args = new NotifyEventArguments(null, OrderRefund::class, $params);
                event(new NotifyEvent($args));
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'提交成功','提交失败',$re);
    }
    /**
     * refund list view
     */
    public function refundList(){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $type = request()->input('type');
        switch($type){
            case 'walletPay':
                $refund = $this->wallet();
                $total = count($refund);
                break;
            case 'aliPay':
                $refund = $this->ali();
                $total = count($refund);
                break;
            default :
                break;
        }
        if(!is_null($take) && !is_null($skip)){
            $refund = $refund->slice($skip,$take);
        }
        $err->setData($refund);
        $err->total = $total;
        $err->take = $take;
        $err->skip = $skip;
        $err->type = $type;
        return view('admin.refund.refundList',['result'=>$err]);
    }

    /**
     * wallet pay
     */
    public function wallet(){
        $refund = RefundRequest::whereHas('refundRequestHandle.handleResult',
            function($query){
                $query->where(IekModel::STATUS,true);
            })
            ->whereHas('orderPay.wealthPay')
            ->whereDoesntHave('OrderReturnPay')
            ->with('orderPay.wealthPay')
            ->with('order.personOrder')
            ->with('reason')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $refund;
    }

    /**
     * @return mixed
     * ali pay
     */
    public function ali(){
        $refund = RefundRequest::whereHas('refundRequestHandle.handleResult',
            function($query){
                $query->where(IekModel::STATUS,true);
            })
            ->whereHas('orderPay.thirdPay')
            ->whereDoesntHave('OrderReturnPay')
            ->with('orderPay.thirdPay')
            ->with('order.personOrder')
            ->with('reason')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $refund;
    }
    /**
     * refund
     * have notify
     */
    public function refund(){
        $err = new Error();
        $uid = session('login.id');
        $ids = request()->input('ids');
        $type = request()->input('type');
        switch($type){
            case 'walletPay':
                $refundRequestId = request()->input('rid');
                $checkRefund = RefundRequest::whereHas('refundRequestHandle.money')->find($refundRequestId);
                if(!is_null($checkRefund)){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('该订单已退款!');
                    return view('message.formResult',['result'=>$err]);
                }
                $refund = RefundRequest::with('personOrder')
                    ->with('refundRequestHandle')
                    ->find($refundRequestId);
                $fee = request()->input('fee');
                $return_pay_no = date('Ymd').time();
                try{
                    DB::beginTransaction();

                    $orderReturnWealthPay = new OrderReturnWealthPay();
                    $orderReturnWealthPay->person_id = $refund->personOrder->person_id;
                    $orderReturnWealthPay->fee = $fee;
                    $orderReturnWealthPay->currency = 'CNY';
                    $orderReturnWealthPay->return_pay_no = $return_pay_no;
                    $orderReturnWealthPay->save();

                    $orderReturnPay = new OrderReturnPay();
                    $orderReturnPay->order_id = $refund->order_id;
                    $orderReturnPay->pay_id = $orderReturnWealthPay->id;
                    $orderReturnPay->pay_method = 0;
                    $orderReturnPay->save();

                    $refundHandleResultHandle = new RefundHandleResultHandle();
                    $refundHandleResultHandle->refund_request_handle_id = $refund->refundRequestHandle->id;
                    $refundHandleResultHandle->return_pay_id = $orderReturnPay->id;
                    $refundHandleResultHandle->operator_id = $uid;
                    $refundHandleResultHandle->save();

                    $reason = new Reason();
                    $reason->reason = '订单已审核并退款';
                    $reason->type = 'status';
                    $reason->open_reason = '订单已审核并退款';
                    $checkReason = $reason->existReason($reason->reason,$reason->type,$reason->reason);
                    if($checkReason){
                        $reason->save();
                    }else{
                        $reason = Reason::queryReason($reason->reason,$reason->type,$reason->reason);
                    }
                    $orderStatus = new OrderStatus();
                    $orderStatus->where(IekModel::ORDER_ID,$refund->order_id)
                        ->update([
                            IekModel::CURRENT => false
                        ]);
                    $orderStatus->order_id = $refund->order_id;
                    $orderStatus->status_id = Status::where(IekModel::NAME,'close')->value(IekModel::ID);
                    $orderStatus->reason_id = $reason->id;
                    $orderStatus->operation_id = $uid;
                    $orderStatus->is_current = true;
                    $re = $orderStatus->save();

                    $params = new \stdClass();
                    $params->action = 'success';
                    $params->lang = 'refund success';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $refund->personOrder->person_id;
                    $params->targetId = $refund->id;
                    $args = new NotifyEventArguments(null, OrderRefund::class, $params);
                    event(new NotifyEvent($args));
                    DB::commit();
                }catch (\Exception $e){
                    DB::rollback();
                    $err->setError(Errors::UNKNOWN_ERROR);
                    $err->setMessage($e->getMessage());
                    return view('message.formResult',['result'=>$err]);
                }
                return $this->curd(Errors::OK,Errors::FAILED,'成功！','失败！',$re);
                break;
            case 'aliPay':
                $reason_refund = request()->input('reason');
                if(is_null($reason_refund) || !isset($reason_refund) || empty($reason_refund)){
                    $err->setError(Errors::NOT_EMPTY);
                    $err->setMessage('请填写操作原因');
                    return view('message.formResult',['result'=>$err]);
                }
                $requests = RefundRequest::whereIn(IekModel::ORDER_ID,$ids)
                    ->whereHas('refundRequestHandle.money')
                    ->with('order')
                    ->get();
                foreach($requests as $request){
                    if(!is_null($request)){
                        $order_no = $request->order->order_no;
                        $err->setError(Errors::INVALID_PARAMS);
                        $err->setMessage('订单'.$order_no.'已退款!');
                        return view('message.formResult',['result'=>$err]);
                    }
                }
                $refunds = OrderPay::whereIn(IekModel::ORDER_ID,$ids)
                    ->whereHas('thirdPay',function($query){
                        $query->where(IekModel::STATUS,true);
                    })
                    ->with('thirdPay')
                    ->where(IekModel::PAY_METHOD,1)
                    ->get();
                foreach($refunds as $refund){
                    $out_trade_no = $refund->thirdPay->pay_no;
                    $trade_no = $refund->thirdPay->third_pay_no;
                    $refund_amount = $refund->thirdPay->fee;
                    $refund_currency = $refund->thirdPay->currency;

                    //请求参数
                    #############################################
                    $params = [];
                    $params['out_trade_no'] = $out_trade_no;//订单支付时传入的商户订单号,不能和 trade_no同时为空。
                    $params['trade_no'] = $trade_no;//支付宝交易号，和商户订单号不能同时为空
                    $params['refund_amount'] = 0.01;//需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
                    $params['refund_reason'] = $reason_refund;
                    $params['refund_currency'] = $refund_currency;
                    $params['operator_id'] = session('login.id');
                    $params = json_encode($params);
                    #############################################

                    //配置参数
                    $aop = new AopClient();
                    //请求接口
                    $request = new AlipayTradeRefundRequest();
                    $request->setBizContent($params);
                    $result = $aop->execute($request);
                    if($result){
                        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
                        $resultCode = $result->$responseNode->code;
                        if(!empty($resultCode)&&$resultCode == 10000) {
                            DB::beginTransaction();
                            try{
                                $orderReturnThirdPay = new OrderReturnThirdPay();
                                $orderReturnThirdPay->person_id = $refund->thirdPay->person_id;
                                $orderReturnThirdPay->fee = $refund_amount;
                                $orderReturnThirdPay->currency = $refund->thirdPay->currency;
                                $orderReturnThirdPay->return_pay_no = $result->$responseNode->out_trade_no;
                                $orderReturnThirdPay->app_id = null;
                                $orderReturnThirdPay->reply = null;
                                $orderReturnThirdPay->status = true;
                                $orderReturnThirdPay->from_account_id = $refund->thirdPay->to_account_id;
                                $orderReturnThirdPay->to_account_id = $refund->thirdPay->from_account_id;
                                $orderReturnThirdPay->third_pay_no = $result->$responseNode->trade_no;
                                $orderReturnThirdPay->return = json_encode($result);
                                $orderReturnThirdPay->save();

                                $orderReturnPay = new OrderReturnPay();
                                $orderReturnPay->order_id = $refund->order_id;
                                $orderReturnPay->pay_id = $orderReturnThirdPay->id;
                                $orderReturnPay->pay_method = 1;
                                $orderReturnPay->save();

                                $odid = $refund->order_id;
                                $refund_request = RefundRequest::with('refundRequestHandle')
                                    ->where(IekModel::ORDER_ID,$odid)
                                    ->first();
                                $refundHandleResultHandle = new RefundHandleResultHandle();
                                $refundHandleResultHandle->refund_request_handle_id = $refund_request->refundRequestHandle->id;
                                $refundHandleResultHandle->return_pay_id = $orderReturnPay->id;
                                $refundHandleResultHandle->operator_id = $uid;
                                $refundHandleResultHandle->save();

                                $reason = new Reason();
                                $reason->reason = $reason_refund;
                                $reason->type = 'status';
                                $reason->open_reason = $reason_refund;
                                $checkReason = $reason->existReason($reason->reason,$reason->type,$reason->reason);
                                if($checkReason){
                                    $reason->save();
                                }else{
                                    $reason = Reason::queryReason($reason->reason,$reason->type,$reason->reason);
                                }
                                $orderStatus = new OrderStatus();
                                $orderStatus->where(IekModel::ORDER_ID,$refund->order_id)
                                    ->update([
                                        IekModel::CURRENT => false
                                    ]);
                                $orderStatus->order_id = $refund->order_id;
                                $orderStatus->status_id = Status::where(IekModel::NAME,'close')->value(IekModel::ID);
                                $orderStatus->reason_id = $reason->id;
                                $orderStatus->operation_id = $uid;
                                $orderStatus->is_current = true;
                                $re = $orderStatus->save();

                                $params = new \stdClass();
                                $params->action = 'success';
                                $params->lang = 'refund success';
                                $params->fromId = OfficialPerson::notifier();
                                $params->toId = $refund->personOrder->person_id;
                                $params->targetId = $refund->id;
                                $args = new NotifyEventArguments(null, OrderRefund::class, $params);
                                event(new NotifyEvent($args));

                                DB::commit();
                            } catch (\Exception $e){
                                DB::rollBack();
                                $err->setError(Errors::UNKNOWN_ERROR);
                                $err->setMessage($e->getMessage());
                                return view('message.formResult',['result'=>$err]);
                            }
                        }else {
                            $err->statusCode = $result->$responseNode->code;
                            $err->message = $result->$responseNode->sub_msg;
                            return view('message.formResult',['result'=>$err]);
                        }
                        return $this->curd(Errors::OK,Errors::FAILED,'成功','失败',$re);
                    }else{
                        $err->setError(Errors::INVALID_PARAMS);
                        $err->setMessage('请求接口失败！');
                        return view('message.formResult',['result'=>$err]);
                    }
                }
                break;
            default:
                break;
        }
    }
    /**
     * check wait product order,if material halt
     * refund money
     * haven't notify
     */
    public function refund_money(){
        $err = new Error();
        $order_id = request()->input('id');
        $pay_type = request()->input('type');
        $pay_fee = request()->input('fee');
        $to = PersonOrder::where(IekModel::ORDER_ID,$order_id)->first();
        switch($pay_type){
            case 'walletPay':
                $return_pay_no = date('Ymd').time();
                DB::beginTransaction();
                try{
                    $orderReturnWealthPay = new OrderReturnWealthPay();
                    $orderReturnWealthPay->person_id = $to->person_id;
                    $orderReturnWealthPay->fee = $pay_fee;
                    $orderReturnWealthPay->return_pay_no = $return_pay_no;
                    $orderReturnWealthPay->save();

                    $res = OrderReturnPay::isExist($order_id);
                    if($res){
                        $err->setError(Errors::EXIST);
                        $err->setMessage('该订单已退款');
                        return view('message.formResult',['result'=>$err]);
                    }
                    $orderReturnPay = new OrderReturnPay();
                    $orderReturnPay->order_id = $order_id;
                    $orderReturnPay->pay_id = $orderReturnWealthPay->id;
                    $orderReturnPay->pay_method = 0;
                    $re = $orderReturnPay->save();
                    DB::commit();
                }catch (\Exception $e){
                    DB::rollback();
                    $err->setError(Errors::UNKNOWN_ERROR);
                    $err->setMessage($e->getMessage());
                    return view('message.formResult',['result'=>$err]);
                }
                return $this->curd(Errors::OK,Errors::FAILED,'已退款！','退款失败！',$re);
                break;
            case 'aliPay':
                //获取配置信息
                $alipay_config = AliPayConfig::configData();
                /**************************请求参数**************************/

                //批次号，必填，格式：当天日期[8位]+序列号[3至24位]，如：201603081000001
                //$batch_no = request()->input('payNo');
                $date = date('Ymd',time());

                //退款笔数，必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）
                //$batch_num = request()->input('batchNum');
                $batch_num = count($ids);

                //退款详细数据，必填，格式（支付宝交易号^退款金额^备注），多笔请用#隔开
                //$fee = request()->input('fee');
                $reason = request()->input('reason');
                //$reasonId = Reason::insertReason($reason , 'refund');
                $orders = OrderPay::whereIn(IekModel::ORDER_ID,$ids)
                    ->whereHas('thirdPay',function($q){
                        $q->where(IekModel::CONDITION);
                    })
                    ->with('thirdPay')
                    ->get();
                $detail_data = '';
                $no = '';
                foreach ($orders as $order){
                    if($detail_data !== ''){
                        $detail_data = $detail_data.'#';
                    }
                    $no .= $order->thirdPay->pay_no;
                    $detail_data = $detail_data.$order->thirdPay->third_pay_no.'^0.01'.'^'.$reason;
                }

                /************************************************************/

                //构造要请求的参数数组，无需改动
                $parameter = array(
                    "service" => trim($alipay_config['service']),
                    "partner" => trim($alipay_config['partner']),
                    "notify_url" => trim($alipay_config['notify_url']),
                    "seller_user_id" => trim($alipay_config['seller_user_id']),
                    "refund_date" => trim($alipay_config['refund_date']),
                    "batch_no" => $date.substr(md5($no), 8, 16),
                    "batch_num" => $batch_num,
                    "detail_data" => $detail_data,
                    "_input_charset" => trim(strtolower($alipay_config['input_charset']))
                );

                //建立请求
                $alipaySubmit = new AlipaySubmit($alipay_config);
                $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认退款");
                return response($html_text);
                break;
            default:
                break;
        }
    }
    /**
     * save returnPay data
     */
    public function orderReturn(){
        $err = new Error();
        dd(request()->all());
        $req = request()->all();
        DB::beginTransaction();
        try{
            $orderReturnThirdPay = new OrderReturnThirdPay();
            $orderReturnThirdPay->person_id = $req['person_id'];
            $orderReturnThirdPay->fee =$req['fee'];
            $orderReturnThirdPay->currency =$req['currency'];
            $orderReturnThirdPay->return_pay_no =$req['return_pay_no'];
            $orderReturnThirdPay->reply =$req['reply'];
            $orderReturnThirdPay->status =$req['status'];
            $orderReturnThirdPay->from_account_id =$req['from_account_id'];
            $orderReturnThirdPay->to_account_id =$req['to_account_id'];
            $orderReturnThirdPay->third_pay_no =$req['third_pay_no'];
            $orderReturnThirdPay->return =$req['return'];
            $re = $orderReturnThirdPay->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'退款成功','退款失败',$re);
    }
}