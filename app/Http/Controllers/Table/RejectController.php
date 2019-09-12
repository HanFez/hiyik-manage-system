<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/9
 * Time: 17:25
 * Reject Request
 */
namespace App\Http\Controllers\Table;

use App\Events\NotifyEvent;
use App\Http\Controllers\Controller;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Notify\OrderReject;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\Order;
use App\IekModel\Version1_0\Reason;
use App\IekModel\Version1_0\Reject;
use App\IekModel\Version1_0\RejectCartProduct;
use App\IekModel\Version1_0\RejectHandle;
use App\IekModel\Version1_0\RejectHandleResult;
use App\IekModel\Version1_0\RejectProduct;
use App\IekModel\Version1_0\RejectRequest;
use App\IekModel\Version1_0\RejectResultHandle;
use App\IekModel\Version1_0\RejectShipFeePay;
use App\IekModel\Version1_0\Ship;
use App\IekModel\Version1_0\ShipFeeReturnPay;
use Illuminate\Support\Facades\DB;

class RejectController extends Controller
{
    public function rejectList(){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $title = request()->input('title');
        $isAudit = request()->input('auditing');
        if(!is_null($title)){
            $no = $this->getOrderByName($title);
        }
        switch($isAudit){
            case 'true':
                $reject = $this->exchanged();
                break;
            case 'false':
                $reject = $this->waitExchange();
                break;
        }
        //dd($reject);
        if(!is_null($title)){
            $reject = $reject->whereIn(IekModel::NO,$no);
        }
        $total = $reject->count();
        if($take != null && $skip != null){
            $reject = $reject->slice($skip,$take);
        }
        $err->setData($reject);
        $err->take = $take;
        $err->skip = $skip;
        $err->total = $total;
        $err->search = $title;
        $err->auditing = $isAudit;
        return view('admin.reject.rejectList',['result'=>$err]);
    }
    /**
     * already check reject
     */
    public function exchanged(){
        $reject = RejectRequest::whereIn('result',[0,1])
            ->with('order')
            ->with('rejectResultHandle.reject.rejectShipFeePay.pay')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        //$reject = $reject->unique(IekModel::ORDER_ID);
        return $reject;
    }
    /**
     * wait check reject
     */
    public function waitExchange(){
        $reject = RejectRequest::whereNull('result')
            ->with('order')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        //$reject = $reject->unique(IekModel::ORDER_ID);
        return $reject;
    }
    /**
     * search
     */
    public function getOrderByName($no){
        $orderName = Order::where(IekModel::NO,'like','%'.$no.'%')
            ->where(IekModel::ACTIVE,true)
            ->pluck(IekModel::NO);
        return $orderName;
    }
    /**
     * 退换申请详情
     */
    public function returnDetail($id){
        $err = new Error();
        $isAudit = request()->input('audit');
        $data = RejectRequest::with(['order.orderReceiveInformation.receiveInformation'=>
            function($query){
                $query->with('name')
                    ->with('address.city')
                    ->with('phone');
            }])
            ->with(['rejectProducts'=>function($query){
                $query->with(['products'=>function($q){
                    $q->with('productDefine')
                        ->with('productThumb.thumb.norm');
                }])
                    ->with('reason')
                    ->with('rejectHandle.rejectHandleResult.reason')
                    ->with(['rejectRequestImages'=>function($q){
                        $q->with('images')
                            ->where(IekModel::CONDITION);
                    }]);
            }])
            ->orderBy(IekModel::CREATED,'desc')
            ->where(IekModel::CONDITION)
            ->find($id);
        $err->setData($data);
        $err->audit = $isAudit;
        return view('admin.reject.returnDetail',['result'=>$err]);
    }
    /**
     * audit view
     * param $id:reject_request_id
     */
    public function audit($id){
        $err = new Error();
        $data = RejectProduct::with('products.productThumb.thumb.norm')
            ->with(['rejectRequestImages'=>function($query){
                $query->with('images')
                    ->where(IekModel::CONDITION);
            }])
            ->where(IekModel::REJECT_RID,$id)
            ->get();
        $err->setData($data);
        return view('admin.reject.audit',['result'=>$err]);
    }
    /**
     * save audit data and notify
     * param $id : reject_request_id
     */
    public function exchangeCheck(){
        $err = new Error();
        $uid = session('login.id');
        $request = request()->all();
        $array = $request['arr'];
        $id = $request['id'];
        $checkReject = RejectRequest::where(IekModel::ID,$id)->value(IekModel::RESULT);
        if(!is_null($checkReject)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage("订单已经审核过了");
            return view('message.formResult',['result'=>$err]);
        }
        DB::beginTransaction();
        try{
            $status = false;
            foreach($array as $arr){
                if($arr['status'] == '1'){
                    $status = true;
                }
                $to = RejectRequest::with('order.personOrder')->find($id);

                $reason = new Reason();
                $reason->reason = $arr['reason'];
                $reason->type = 'reject';
                $reason->open_reason = $arr['reason'];
                $checkReason = $reason->existReason($arr['reason'],'reject',$arr['reason']);
                if($checkReason){
                    $reason->save();
                }else{
                    $reason = Reason::queryReason($arr['reason'],'reject',$arr['reason']);
                }
                $rejectHandleResult = new RejectHandleResult();
                $rejectHandleResult->reason_id = $reason->id;
                $rejectHandleResult->status = $arr['status'];
                $rejectHandleResult->is_recycling = $arr['recycle'];
                $rejectHandleResult->save();

                $rejectHandle = new RejectHandle();
                $rejectHandle->reject_handle_result_id = $rejectHandleResult->id;
                $rejectHandle->operator_id = $uid;
                $rejectHandle->reject_product_id = $arr['id'];
                $re = $rejectHandle->save();
            }
            if($status){
                RejectRequest::where(IekModel::ID,$id)
                    ->update([
                        IekModel::RESULT => 1
                    ]);
                //审核通过通知
                $params = new \stdClass();
                $params->action = 'apply';
                $params->lang = 'reject apply failed';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $to->order->personOrder->person_id;
                $params->targetId = $id;
                $args = new NotifyEventArguments(null, OrderReject::class, $params);
                event(new NotifyEvent($args));
            }else{
                RejectRequest::where(IekModel::ID,$id)
                    ->update([
                        IekModel::RESULT => 0,
                        IekModel::CONFIRM_REJECT => true
                    ]);
                //审核拒绝通知
                $params = new \stdClass();
                $params->action = 'apply';
                $params->lang = 'reject apply passed';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $to->order->personOrder->person_id;
                $params->targetId = $id;
                $args = new NotifyEventArguments(null, OrderReject::class, $params);
                event(new NotifyEvent($args));
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'保存成功','保存失败',$re);
    }
    /**
     * exchange view
     * param $id : reject_request_id
     */
    public function exchangeView($id){
        $err = new Error();
        $reject = RejectRequest::with('rejectResultHandle.reject')
            ->where(IekModel::CONDITION)
            ->find($id);
        $err->setData($reject);
        return view('admin.reject.reject',['result' => $err]);
    }
    /**
     * exchange a purchase
     */
    public function exchange(){
        $err = new Error();
        $uid = session('login.id');
        $input = request()->except('_token');
        $checkReject = RejectRequest::whereHas('rejectResultHandle.reject',
            function($query){
                $query->whereNotNull(IekModel::SEND_SHIP_ID);
            })
            ->find($input['rejectRequestId']);
        if(!is_null($checkReject)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('订单已经发过货了');
            return view('message.formResult',['result'=>$err]);
        }
        DB::beginTransaction();
        try{
            //退换
            $ship = new Ship();
            $ship->no = $input['shipNo'];
//            $ship->provider_id = $input['company'];
            $ship->fee = $input['fee'];
            $ship->net_fee = $input['netFee'];
            $ship->cost_fee = $input['costFee'];
            $ship->is_free = true;
            $ship->save();

            $reject = new Reject();
            if($input['rejectId'] != ''){
                $re = $reject->where(IekModel::ID,$input['rejectId'])
                    ->update([
                        IekModel::SEND_SHIP_ID => $ship->id,
                        IekModel::SEND_OID => $uid
                    ]);
                RejectResultHandle::where(IekModel::REJECT_ID,$input['rejectId'])
                    ->update([
                        IekModel::OID => $uid
                    ]);

                /*$reject->send_ship_id = $ship->id;
                $re = $reject->save();*/
            }else {
                $reject->send_ship_id = $ship->id;
                $reject->send_operator_id = $uid;
                $reject->save();

                $rejectResultHandle = new RejectResultHandle();
                $rejectResultHandle->reject_request_id = $input['rejectRequestId'];
                $rejectResultHandle->reject_id = $reject->id;
                $rejectResultHandle->operator_id = $uid;
                $re = $rejectResultHandle->save();
            }

            //通知
            $to = RejectRequest::with('order.personOrder')->find($input['rejectRequestId']);
            $params = new \stdClass();
            $params->action = 'sent';
            $params->lang = 'reject sent';
            $params->fromId = OfficialPerson::notifier();
            $params->toId = $to->order->personOrder->person_id;
            $params->targetId = $input['rejectRequestId'];
            $args = new NotifyEventArguments(null, OrderReject::class, $params);
            event(new NotifyEvent($args));

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
     * modify platform_confirm , ship_fee_result
     * param $id : reject_id
     */
    public function confirmPersonSendGoods($id){
        $err = new Error();
        $uid = session('login.id');
        $content = request()->input('receiptReason');
        $returnFeeReason = request()->input('returnFeeReason');
        $shipConfirm = request()->input('shipFeeResult');
        $goodsResult = request()->input('goodsResult');
        if($shipConfirm == 1){
            $returnFeeReason = '同意退邮';
        }
        if($goodsResult == 1){
            $content = '退换产品无误';
        }
        if(is_null($content) || empty($content) || !isset($content)){
            $err->setError(Errors::NOT_EMPTY);
            $err->setMessage("请输入确认收货原因");
            return view('message.formResult',['result'=>$err]);
        }
        if(is_null($returnFeeReason) || empty($returnFeeReason) || !isset($returnFeeReason)){
            $err->setError(Errors::NOT_EMPTY);
            $err->setMessage("请输入确认退邮原因");
            return view('message.formResult',['result'=>$err]);
        }
        $checkConfirm = Reject::where(IekModel::PLATFORM_CONFIRM,true)->find($id);
        if(!is_null($checkConfirm)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage("平台已确认收货了");
            return view('message.formResult',['result'=>$err]);
        }
        $reason = new Reason();
        $reason->reason = $content;
        $reason->type = 'reject';
        $reason->open_reason = $content;
        $checkReason = $reason->existReason($content,'reject',$content);
        if($checkReason){
            $reason->save();
        }else{
            $reason = Reason::queryReason($content,'reject',$content);
        }
        DB::beginTransaction();
        try{
            $re = Reject::where(IekModel::ID,$id)
                ->update([
                    'ship_fee_result' => $shipConfirm,
                    'platform_confirm' => true,
                    'reason' => $returnFeeReason,
                    'good_result' => $goodsResult,
                    'receive_operator_id' => $uid
                ]);

            $person = Reject::with('rejectResultHandle.rejectRequest.order.personOrder')->find($id);
            $to = $person->rejectResultHandle->rejectRequest->order->personOrder;
            //确认收货通知
            $params = new \stdClass();
            $params->action = 'platform confirmed';
            $params->lang = 'reject platform confirmed';
            $params->fromId = OfficialPerson::notifier();
            $params->toId = $to->person_id;
            $params->targetId = $person->rejectResultHandle->reject_request_id;
            $params->reasonId = $reason->id;
            $args = new NotifyEventArguments(null, OrderReject::class, $params);
            event(new NotifyEvent($args));

            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'成功','失败',$re);
    }
    /**
     * confirm receive and confirm fee
     * param $id : reject_id
     */
    public function confirmReceipt($id){
        $err = new Error();
        $data = Reject::where(IekModel::CONDITION)
            ->with('rejectResultHandle.rejectRequest.order.personOrder')
            ->with('backShip')
            ->find($id);
        $err->setData($data);
        return view('admin.reject.confirmReceipt',['result'=>$err]);
    }
    /**
     * return ship fee
     * param $id : reject_request_id
     */
    public function shipFee($id){
        $err = new Error();
        $data = RejectRequest::with('order.personOrder')
            ->with('rejectResultHandle.reject.backShip')
            ->find($id);
        $err->setData($data);
        return view('admin.reject.returnFee',['result'=>$err]);
    }
    /**
     * save ship fee
     * param $id : reject_request_id
     */
    public function returnShipFee($id){
        $err = new Error();
        $uid = session('login.id');
        $fee = request()->input('fee');
        $rejectId = request()->input('rejectId');
        $personId = request()->input('personId');
        $nowTime = time();
        $nowDate = date('Ymd');
        $no = $nowDate.$nowTime;
        DB::beginTransaction();
        try{
            $checkReturnFee = RejectShipFeePay::where(IekModel::REJECT_ID,$rejectId)->get();
            if(!$checkReturnFee->isEmpty()){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage("订单已经退过邮费了");
                return view('message.formResult',['result'=>$err]);
            }

            $pay = new ShipFeeReturnPay();
            $pay->fee = $fee;
            $pay->person_id = $personId;
            $pay->pay_no = $no;
            $pay->save();

            //退邮费通知
            $params = new \stdClass();
            $params->action = 'ship fee';
            $params->lang = 'reject ship fee';
            $params->fromId = OfficialPerson::notifier();
            $params->toId = $personId;
            $params->targetId = $id;
            $args = new NotifyEventArguments(null, OrderReject::class, $params);
            event(new NotifyEvent($args));

            $rejectShipFeePay = new RejectShipFeePay();
            $rejectShipFeePay->pay_id = $pay->id;
            $rejectShipFeePay->pay_method = 0;
            $rejectShipFeePay->reject_id = $rejectId;
            $rejectShipFeePay->order_id = RejectRequest::where(IekModel::ID,$id)->value('order_id');
            $re = $rejectShipFeePay->save();

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'成功','失败',$re);
    }
}