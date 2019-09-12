<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/11/27
 * Time: 17:56
 */
namespace App\Http\Controllers\Table;

use App\Events\NotifyEvent;
use App\Http\Controllers\AliPayTransfer\AliPayTransferRequest;
use App\Http\Controllers\AliPayTransfer\AopClient;
use App\Http\Controllers\Controller;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Version1_0\CashAudit;
use App\IekModel\Version1_0\CashPay;
use App\IekModel\Version1_0\CashRequest;
use App\IekModel\Version1_0\CashRequestPay;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\Reason;
use App\IekModel\Version1_0\ThirdPayAccount;
use Illuminate\Support\Facades\DB;

class CashController extends Controller
{
    public function cashList(){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $type = request()->input('type');
        $param = request()->input('param');
        switch($type){
            case 'cash':
                $cash = $this->cash();
                if($param == 'cash-wait')
                    $cash = $this->cashWait();
                if($param == 'cash-no')
                    $cash = $this->cashNo();
                if($param == 'cash-yes')
                    $cash = $this->cashYes();
                break;
            case 'pay':
                $cash = $this->pay();
                if($param == 'pay-wait')
                    $cash = $this->payWait();
                if($param == 'pay-no')
                    $cash = $this->payNo();
                if($param == 'pay-yes')
                    $cash = $this->payYes();
                if($param == 'unCash')
                    $cash = $this->unCash();
                if($param == 'cashed')
                    $cash = $this->cashed();
                break;
            default:
                break;
        }

        $total = count($cash);
        if($take != null && $skip != null){
            $cash = $cash->slice($skip,$take);
        }
        $err->setData($cash);
        $err->total = $total;
        $err->take = $take;
        $err->skip = $skip;
        $err->type = $type;
        $err->param = $param;
        return view('admin.cash.cash',['result'=>$err]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * select * from t where cash_audit =0,1,2
     * 所有提现申请审核状态
     */
    public function cash(){
        $cash = CashRequest::with('cashAudit.reason')
            ->with('thirdAccount')
            ->with(['person.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->whereIn(IekModel::CASH_AUDIT,[0,1,2])
            ->where(IekModel::PAY_AUDIT,1)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $cash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * select * from t where pay_audit =0,1,2
     * 所有提现支付审核状态
     */
    public function pay(){
        $cash = CashRequest::with('cashAudit.reason')
            ->with('thirdAccount')
            ->with(['person.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->where(IekModel::CASH_AUDIT,0)
            ->whereIn(IekModel::PAY_AUDIT,[0,1,2])
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $cash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * select * from t where cash_audit =1
     * cash_audit=1 申请未审核
     */
    public function cashWait(){
        $cash = CashRequest::with('cashAudit.reason')
            ->with('thirdAccount')
            ->with(['person.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->where(IekModel::CASH_AUDIT,1)
            //->where(IekModel::PAY_AUDIT,1)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $cash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * select * from t where cash_audit =2
     * cash_audit=2 申请审核未通过
     */
    public function cashNo(){
        $cash = CashRequest::with('cashAudit.reason')
            ->with('thirdAccount')
            ->with(['person.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->where(IekModel::CASH_AUDIT,2)
            //->where(IekModel::PAY_AUDIT,1)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $cash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * select * from t where cash_audit =0
     * cash_audit = 0 申请审核通过
     */
    public function cashYes(){
        $cash = CashRequest::with('cashAudit.reason')
            ->with('thirdAccount')
            ->with(['person.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->where(IekModel::CASH_AUDIT,0)
            //->where(IekModel::PAY_AUDIT,1)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $cash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * select * from t where cash_audit =0
     * cash_audit = 0 && pay_audit = 1 提现申请审核通过 提现支付未审核
     */
    public function payWait(){
        $cash = CashRequest::with('cashAudit.reason')
            ->with('thirdAccount')
            ->with(['person.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->where(IekModel::CASH_AUDIT,0)
            ->where(IekModel::PAY_AUDIT,1)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $cash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * select * from t where pay_audit =2
     * pay_audit =1 支付审核未通过
     */
    public function payNo(){
        $cash = CashRequest::with('cashAudit.reason')
            ->with('thirdAccount')
            ->with(['person.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->where(IekModel::CASH_AUDIT,0)
            ->where(IekModel::PAY_AUDIT,2)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $cash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * select * from t where pay_audit =0
     * pay_audit =0 支付审核通过
     */
    public function payYes(){
        $cash = CashRequest::with('cashAudit.reason')
            ->with('thirdAccount')
            ->with(['person.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->where(IekModel::CASH_AUDIT,0)
            ->where(IekModel::PAY_AUDIT,0)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $cash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * no cash
     */
    public function unCash(){
        $cash = CashRequest::doesntHave('cashRequestPay')
            ->with('thirdAccount')
            ->with(['person.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->with('cashAudit.reason')
            ->where(IekModel::CASH_AUDIT,0)
            ->where(IekModel::PAY_AUDIT,0)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $cash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * cashed
     */
    public function cashed(){
        $cash = CashRequest::has('cashRequestPay')
            ->with('thirdAccount')
            ->with(['person.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->with('cashAudit.reason')
            ->where(IekModel::CASH_AUDIT,0)
            ->where(IekModel::PAY_AUDIT,0)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $cash;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * audit request and cash-transfer
     */
    public function twoAudit(){
        $err = new Error();
        $uid = session('login.id');
        $ids = request()->input('ids');
        $status = request()->input('status');
        $type = request()->input('type');
        $param = request()->input('param');
        $auditReason = request()->input('reason');
        if(!is_array($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return view('message.formResult',['result'=>$err]);
        }
        if(is_null($type) || !isset($type) || empty($type)){
            $err->setError(Errors::NOT_EMPTY);
            return view('message.formResult',['result'=>$err]);
        }
        if(is_null($status)){
            $err->setError(Errors::NOT_EMPTY);
            return view('message.formResult',['result'=>$err]);
        }
        if(is_null($auditReason) || !isset($auditReason) || empty($auditReason)){
            $err->setError(Errors::NOT_EMPTY);
            return view('message.formResult',['result'=>$err]);
        }
        foreach($ids as $id){
            if($type == 'cash_audit'){
                $checkCash = CashRequest::whereIn(IekModel::CASH_AUDIT,[0,2])->find($id);
            }else if($type == 'pay_audit'){
                $checkCash = CashRequest::whereIn(IekModel::PAY_AUDIT,[0,2])->find($id);
            }
            if(!is_null($checkCash)){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('订单已经审核过了');
                return view('message.formResult',['result'=>$err]);
            }
        }
        DB::beginTransaction();
        try{
            $reason = new Reason();
            $reason->reason = $auditReason;
            $reason->type = 'cashAudit';
            $reason->open_reason = $auditReason;
            $checkReason = $reason->existReason($auditReason,'cashAudit',$auditReason);
            if($checkReason){
                $reason->save();
            }else{
                $reason = Reason::queryReason($auditReason,'cashAudit',$auditReason);
            }

            if($param == 'cash-wait'){
                CashRequest::whereIn(IekModel::ID,$ids)
                    ->update([
                        IekModel::CASH_AUDIT => $status
                    ]);
            }
            if($param == 'pay-wait'){
                CashRequest::whereIn(IekModel::ID,$ids)
                    ->update([
                        IekModel::PAY_AUDIT => $status
                    ]);
            }

            foreach($ids as $id){
                $cashAudit = new CashAudit();
                $cashAudit->cash_request_id = $id;
                $cashAudit->reason_id = $reason->id;
                $cashAudit->operator_id = $uid;
                $cashAudit->type = $type;
                $re = $cashAudit->save();
                if($status === 2){
                    $params = new \stdClass();
                    $params->action = 'cashed';
                    $params->lang = 'cashed fail';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = CashRequest::where(IekModel::ID,$id)->value(IekModel::UID);
                    $params->targetId = $id;
                    $params->reasonId = $reason->id;
                    $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\CashPay::class, $params);
                    event(new NotifyEvent($args));
                }
            }

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'保存成功','保存失败',$re);
    }
    /**
     * one transfer alipay transfer
     * cash transfer
     */
    public function transfer(){
        $err = new Error();
        $ids = request()->input('ids');
        $status = request()->input('status');
        $acnt = 'tianpei@hiyik.com';
        $fromId = ThirdPayAccount::isExistAccount($acnt);
        $cashs = CashRequest::with('thirdAccount')
            ->whereIn(IekModel::ID,$ids)
            ->get();
        foreach($cashs as $cash){
            $checkCash = CashRequest::whereHas('cashRequestPay')->find($cash->id);
            if(!is_null($checkCash)){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('订单已经提现成功');
                return view('message.formResult',['result'=>$err]);
            }
            $fee = $cash->fee - $cash->service_charge;
            $no = $cash->pay_no;
            $account = $cash->thirdAccount->account;
            //$realname = $cash->person->personIdentity->identity->name;

            ############请求参数###############
            $arr = [];
            $arr['out_biz_no'] = $no;
            $arr['payee_type'] = 'ALIPAY_LOGONID';
            $arr['payee_account'] = $account;
            $arr['amount'] = 0.1;
            $arr['payer_show_name'] = '';//付款方姓名 默认显示付款方的支付宝认证姓名或单位名称
            //$arr['payee_real_name'] = $realname;
            $arr['remark'] = 'HIYIK平台-用户提现';
            $arr = json_encode($arr);
            ####################################

            $aop = new AopClient();
            $request = new AliPayTransferRequest();
            $request->setBizContent($arr);

            $result = $aop->execute($request);
            if($result){
                $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
                $resultCode = $result->$responseNode->code;
                if(!empty($resultCode)&&$resultCode == 10000){
                    DB::beginTransaction();
                    try{
                        $cashPay = new CashPay();
                        $cashPay->person_id = $cash->person_id;
                        $cashPay->fee = $cash->fee;
                        $cashPay->currency = $cash->currency;
                        $cashPay->pay_no = $result->$responseNode->out_biz_no;
                        $cashPay->reply = null;
                        $cashPay->status = true;
                        $cashPay->app_id = null;
                        $cashPay->from_account_id = $fromId;
                        $cashPay->to_account_id = $cash->third_account_id;
                        $cashPay->third_pay_no = $result->$responseNode->order_id;
                        $cashPay->service_charge = $cash->service_charge;
                        $cashPay->return = json_encode($result);
                        $cashPay->client = 'browser';
                        $cashPay->save();
                        //发通知
                        $params = new \stdClass();
                        $params->action = 'cashed';
                        $params->lang = 'cashed success';
                        $params->fromId = OfficialPerson::notifier();
                        $params->toId = $cash->person_id;
                        $params->targetId = $cash->id;
                        $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\CashPay::class, $params);
                        event(new NotifyEvent($args));

                        $cashRequestPay = new CashRequestPay();
                        $cashRequestPay->request_id = $cash->id;
                        $cashRequestPay->pay_id = $cashPay->id;
                        $cashRequestPay->pay_method = 1;
                        $re = $cashRequestPay->save();

                        DB::commit();
                    }catch (\Exception $e){
                        DB::rollBack();
                        $err->setError(Errors::UNKNOWN_ERROR);
                        $err->setMessage($e->getMessage());
                        return view('message.formResult',['result'=>$err]);
                    }
                } else {
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
    }

    /**
     * cash transfer
     * batch transfer alipay transfer
     */
    /*public function transfer(){
        $err = new Error();
        $uid = session('login.id');
        if(is_null($uid)){
            $err->setError(Errors::NOT_LOGIN);
            $err->setMessage('登录信息已过期，请重新登录');
            return view('message.formResult',['result'=>$err]);
        }
        $ids = request()->input('ids');
        $num = count($ids);
        $reason = request()->input('reason');
        //获取配置信息
        $alipay_config = TransferConfig::configData();
        ######################请求参数#####################

        //付款当天日期
        $dateTime = date('Ymd',time());
        $pay_date = $dateTime;
        //必填，格式：年[4位]月[2位]日[2位]，如：20100801

        //批次号
        $no = date('YmdHis',time());
        $batch_no = $dateTime.$no;
        //必填，格式：当天日期[8位]+序列号[3至16位]，如：201008010000001

        $cash = CashPay::whereIn('id',$ids)
            ->with('toAccount')
            ->with(['person.personIdentity'=>function($query){
                $query->where(IekModel::CONDITION)
                    ->with('identity');
            }])
            ->orderBy('created_at','desc')
            ->get();
        $total_fee = 0;
        $detail_data = '';
        foreach($cash as $ch){
            if(!is_null($ch)){
                if($ch->toAccount != null){
                    $buyerAccount = $ch->toAccount->account;
                }
                if($ch->person != null && $ch->person->personIdentity != null
                    && $ch->person->personIdentity->identity != null){
                    $buyerName = $ch->person->personIdentity->identity->name;
                }
                $payNum = $ch->pay_no;
                $fee = $ch->fee;
            }
            $total_fee += $fee;
            if($detail_data !== ''){
                $detail_data = $detail_data.'|';
            }
            $detail_data = $detail_data.$payNum.'^'.$buyerAccount.'^'.$buyerName.'^'.$fee.'^'.$reason;

        }

        //付款总金额
        $batch_fee = $total_fee;
        //必填，即参数detail_data的值中所有金额的总和

        //付款笔数
        $batch_num = $num;
        //必填，即参数detail_data的值中，“|”字符出现的数量加1，最大支持1000笔（即“|”字符出现的数量999个）

        //付款详细数据
        //$detail_data = $_POST['WIDdetail_data'];
        //必填，格式：流水号1^收款方帐号1^真实姓名^付款金额1^备注说明1|流水号2^收款方帐号2^真实姓名^付款金额2^备注说明2....


        ######################################################
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "batch_trans_notify",
            "partner" => trim($alipay_config['partner']),
            "notify_url" => $alipay_config['notify_url'],
            "email" => $alipay_config['email'],
            "account_name" => $alipay_config['account_name'],
            "pay_date" => $pay_date,
            "batch_no" => $batch_no,
            "batch_fee" => $batch_fee,
            "batch_num" => $batch_num,
            "detail_data" => $detail_data,
            "_input_charset" => trim(strtolower($alipay_config['input_charset']))
        );
        //建立请求
        $alipaySubmit = new AliPaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认操作");
        return  response($html_text);
    }*/
}
?>