<?php

namespace App\Http\Controllers\Table;

use App\Events\FreezeWallet;
use App\Events\NotifyEvent;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\EventArguments\EventArguments;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Utils\VerifyAction;
use App\IekModel\Version1_0\Constants\PersonAction;
use App\IekModel\Version1_0\Constants\ReasonType;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ManageLogs;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\PersonAvatar;
use App\IekModel\Version1_0\PersonBirthday;
use App\IekModel\Version1_0\PersonGag;
use App\IekModel\Version1_0\PersonIdentity;
use App\IekModel\Version1_0\Reason;
use App\IekModel\Version1_0\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\Gag;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Person;
use App\IekModel\Version1_0\PersonMail;
use App\IekModel\Version1_0\PersonPhone;
use App\IekModel\Version1_0\PersonNick;
use App\IekModel\Version1_0\PersonGender;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PersonController extends Controller
{
    use TraitRequestParams;

    /**
     * get one person page
     *
     * @param Request $request
     * @return view
     */
    public function person($personId){
        $err = new Error();
        $person = Person::where(IekModel::ID,$personId)
            ->with('personAccount')
            ->with(['personGag'=>
                function($query){
                    $query->where(IekModel::CONDITION)
                        ->orderBy(IekModel::UPDATED, 'desc');
                }])
            ->with(['manageLog'=>
                function($query){
                    $query->with('operator')
                        ->with('reason')
                        ->orderBy(IekModel::UPDATED,'desc');
                }])
            ->with('personFamiliar.familiar')
            ->with('personFavor.favor')
            ->first();
        if(is_null($person)){
            $err -> setError(Errors::INVALID_PARAMS);
            $err -> setMessage('invalid personId');
            return view('message.formResult',['result'=>$err]);
        }

        $person -> count = PublicationPersonController::getPersonPublicationStatistics($personId);
        $person -> mail = PersonMail::getMails($personId);
        $person -> phone = PersonPhone::getBindPhone($personId);

        $personContent = new Person();
        $personContent -> id = $personId;
        $person -> nick = $personContent->getNick();
        $person -> signature = $personContent->getSignature();
        $person -> birthday = PersonBirthday::where(IekModel::UID,$personId)
            ->with('birthday')
            ->where(IekModel::CONDITION)
            ->first();
        $person -> gender = PersonGender::getActiveGender($personId);
        $person -> avatar = PersonAvatar::getActiveAvatar($personId);
        $person -> name = Person::getActiveName($personId);
        $reason = Reason::getReason();
        $person -> reasons = $reason;
        $wealth = Person::wealth($personId);
        $wallet = Wallet::where(IekModel::CONDITION)
            ->where(IekModel::UID,$personId)
            ->first();
        $person -> wealth = $wealth;
        $person -> wallet = $wallet;
        $err -> setData($person);
        return view('admin.person.person',['person'=>$err]);
    }

    public function getPersonListByNick($nick){
        $personId = PersonNick::whereHas('nick',
            function($query) use ($nick){
                $query->where(IekModel::NICK,'like','%'.$nick.'%');
            })
            ->where(IekModel::ACTIVE,TRUE)
            ->pluck(IekModel::UID);
        return $personId;
    }

    /**
     * get person list
     *
     * @param Request $request
     * @return view
     */
    public function personList(Request $request){
        $err = new Error();
        $take = $request->input('take');
        $skip = $request->input('skip');
        $time = session('personListTime');
        if(is_null($time)){
            $time = date('Y-m-d H:i:s');
            session(['personListTime' => $time]);
        }
        $nick = $request->input('nick');
        $isForbidden = $request->input('isForbidden');
        if(!is_null($nick)){
            $pid = $this->getPersonListByNick($nick);
        }
        if($isForbidden != 'true'){
            $isForbidden = false;
        }

        if($isForbidden){
            $person = Person::with(['personNick' =>
                function($query){
                    $query->where(IekModel::ACTIVE,true)
                        ->with('nick');
                }])
                ->with(['personAvatar'=>
                    function($query){
                        $query->where(IekModel::ACTIVE,true)
                            ->with('avatar.norms');
                    }])
                ->where(IekModel::CONDITION)
                ->where('is_forbidden',true)
                ->where(IekModel::CREATED,'<',$time);
            if(!is_null($nick)){
                $person = $person->whereIn(IekModel::ID,$pid);
            }
            $person = $person->orderBy(IekModel::CREATED, 'desc')->get();
        }else{
            $person = Person::with(['personNick'=>
                function($query){
                    $query->where(IekModel::ACTIVE,true)
                        ->with('nick');
                }])
                ->with(['personAvatar'=>
                    function($query){
                        $query->where(IekModel::ACTIVE,true)
                            ->with('avatar.norms');
                    }])
                ->with(['personGag' =>
                    function($query) {
                        $query->where(IekModel::CONDITION)
                            ->orderBy(IekModel::UPDATED, 'desc');
                    }])
                ->where(IekModel::CONDITION)
                ->where(IekModel::IS_FORBIDDEN,false)
                ->where(IekModel::CREATED,'<',$time);
            if(!is_null($nick)){
                $person = $person->whereIn(IekModel::ID,$pid);
            }
            $person = $person->orderBy(IekModel::CREATED, 'desc')
                ->get()
                ->each(function($item, $key) {
                    if(!is_null($item->personGag) && !$item->personGag->isEmpty()) {
                        $gag = $item->personGag->shift();
                        $gag = !$gag->isExpired();//getGagInfo();
                        unset($item->personGag);
                        $item -> isGag = $gag;
                    } else {
                        unset($item -> personGag);
                        $item -> isGag = false;
                    }
                });
        }
        $err -> total = $person->count();
        if($take != null && $skip != null){
            $person = $person->slice($skip,$take);
        }
        $err -> setData($person);
        $err -> isForbidden = $isForbidden;
        $err -> skip = $skip;
        $err -> take = $take;
        $err -> search = $nick;
        return view('admin.person.personList',['result'=>$err]);
    }

    public function getPersonByNick($nick){
        $personId = PersonNick::whereHas('nick',
            function($query) use ($nick){
                $query->where(IekModel::NICK,'like',$nick)
                    ->where(IekModel::ACTIVE,TRUE);
            })
            ->where(IekModel::ACTIVE,TRUE)
            ->first();
        return $personId->person_id;
    }

    /**To set person gag status
     * @param integer $personId The person ID.
     * @param string $type The gag type, eg. "message", "comment".
     * @param bool $isForever To indicate whether gag a person forever
     * @param integer $reasonId The reason of gag.
     * @param integer $minutes The interval be gaged, minutes.
     * @return View
     */
    public function addPersonGag($personId, $type, $isForever, $reasonId, $minutes=0) {
        try {
            if(is_null($personId)) {
                return view('message.messageAlert', ['message' => 'Unknown person.', 'type' => 'error']);
            }
            $personGag = new PersonGag();
            if(is_null($type)) {
                $personGag -> type = Gag::ALL;
            } else {
                $personGag -> type = $type;
            }
            $personGag -> begin_at = Carbon::createFromTimestampUTC(time());
            if(!$isForever) {
                $personGag -> expired = $minutes;
            } else {
                $personGag -> expired = 0;
            }
            $personGag -> reason_id = $reasonId;
            if(is_null($reasonId)) {
                return view('message.messageAlert', ['message' => 'Unknown reason.', 'type' => 'error']);
            }
            $operator = session('login.id');
            if(!is_null($operator)) {
                $personGag -> operator_id = $operator;
            } else {
                return view('message.messageAlert', ['message' => 'Unknown operator.', 'type' => 'error']);
            }
            $personGag -> person_id = $personId;
            $result = $personGag->save();
            if(!$result) {
                return view('message.messageAlert', ['type' => 'error', 'message' => 'Save failed.']);
            }else{
                /*$record = new ManageLogs();
                $record ->reason_id = $reasonId;
                $record ->operator_id = $operator;
                $record ->table_name = $personGag->getDataTable();
                $record ->row_id = $personGag->id;
                $record ->content = json_encode($personGag::getRecords(['id'=>$personGag->id]));
                $record ->memo = '禁言';
                $record->save();*/

                $params = new \stdClass();
                $params -> action = PersonAction::FORBIDDEN;
                $params -> lang = 'person gag forbidden';
                $params -> fromId = OfficialPerson::notifier();
                $params -> toId = $personId;
                $params -> targetId = $personGag->id;
                $params -> reasonId = $reasonId;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\PersonGag::class, $params);
                event(new NotifyEvent($args));
            }
        } catch (\Exception $ex) {
            return view('message.messageAlert', ['type' => 'error', 'message' => $ex->getMessage()]);
        }
        return view('message.messageAlert', ['type' => 'success', 'message' => 'Save success.']);
    }

    //cancel gag
    public function cancelPersonGag($id){
        $err = new Error();
        $personGag = PersonGag::where(IekModel::CONDITION)
            ->where(IekModel::UID,$id)
            ->orderBy(IekModel::CREATED,'desc')
            ->first();
        if(!is_null($personGag)){
            $now = time();
            $begin = strtotime($personGag->begin_at);
            if(($now-$begin)/60 < $personGag->expired){
                PersonGag::where(IekModel::ID,$personGag->id)
                    ->update([
                        IekModel::REMOVED => true
                    ]);

                /*$record = new ManageLogs();
                $record ->reason_id = request()->input('reasonId');
                $record ->operator_id = session('login.id');
                $record ->table_name = $personGag->getDataTable();
                $record ->row_id = $id;
                $record ->content = json_encode($personGag::getRecords(['id'=>$id]));
                $record ->memo = '取消禁言';
                $record->save();*/

                $params = new \stdClass();
                $params -> action = PersonAction::UNFORBIDDEN;
                $params -> lang = 'person gag unforbidden';
                $params -> fromId = OfficialPerson::notifier();
                $params -> toId = $id;
                $params -> targetId = $personGag->id;
                $params -> reasonId = $personGag->reason_id;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\PersonGag::class, $params);
                event(new NotifyEvent($args));
            }
        }else{
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage("此用户未被禁言");
            return view('message.formResult',['result'=>$err]);
        }
        return view('message.messageAlert', ['type' => 'success', 'message' => 'Save success.']);
    }

    public function updateGag($id, $type, $isForever, $reasonId, $minutes=0, $isRemoved = false) {
        if(is_null($id)) {
            return view('message.messageAlert', ['type' => 'error', 'message' => 'Unknown gag.']);
        }
        $personGag = PersonGag::find($id);
        if(is_null($personGag)) {
            return view('message.messageAlert', ['type' => 'error', 'message' => 'Not found.']);
        }
        if(is_null($type)) {
            $personGag -> type = $type;
        }
        if(is_null($isForever)) {
            $personGag -> is_forever = $isForever;
        }
        if(is_null($reasonId)) {
            $personGag -> reason_id = $reasonId;
        }
        if(is_null($minutes)) {
            $personGag -> expired = $minutes;
        }
        if(is_null($isRemoved)) {
            $personGag -> is_removed = $isRemoved;
        }
        $result = $personGag->save();
        if($result) {
            return view('message.messageAlert', ['type' => 'success', 'message' => 'Save success.']);
        } else {
            return view('message.messageAlert', ['type' => 'error', 'message' => 'Save failed.']);
        }
    }
    public function personGag(Request $request, $personId = null) {
        $params = $this->getGagParams($request);
        if(is_null($params->personId)) {
            $params -> personId = $personId;
        }
        return $this->addPersonGag($personId, $params->type, $params->isForever, $params->reasonId, $params->interval);
    }

    public function getGagParams(Request $request) {
        $params = new \stdClass();
        $params -> id = $this->getRequestParam($request, 'id');
        $params -> type = $this->getRequestParam($request, 'type');
        $params -> isForever = $this->getRequestParam($request, 'isForever');
        $params -> interval = $this->getRequestParam($request, 'interval');
        $params -> personId = $this->getRequestParam($request, 'personId');
        $params -> is_active = $this->getRequestParam($request, 'isActive');
        $params -> is_removed = $this->getRequestParam($request, 'isRemoved');
        $reasonType = $this->getRequestParam($request, 'reasonType');
        $reasonId = null;
        if($reasonType == 'id') {
            $params -> reasonId = $this->getRequestParam($request, 'reason');
        } else if($reasonType == 'text') {
            $res = Reason::existReason($reasonType);
            if(!$res){
                $params -> reasonId = Reason::where(IekModel::REASON,$reasonType)->value(IekModel::ID);
            }else{
                $reason = new Reason();
                $reason -> reason = $this->getRequestParam($request, 'reason');
                $reason -> type = ReasonType::GAG;
                $result = $reason->save();
                if($result) {
                    $params -> reasonId = $reason->id;
                }
            }
        }
        return $params;
    }
    public function updatePersonGag(Request $request, $id = null) {
        $params = $this->getGagParams($request);
        if(is_null($params->id)) {
            $params -> id = $id;
        }
        return $this->updateGag($params->id, $params->type, $params->isForever, $params->reasonId,
            $params->interval, $params->is_removed);
    }

    /**
     * freeze wallet
     */
    public function freezeWallet($id){
        $err = new Error();
        $action = request()->input('action');
        if(is_null($action)){
            $err->setError(Errors::INVALID_PARAMS);
            return view('message.formResult',['result'=>$err]);
        }
        DB::beginTransaction();
        try{
            $res = Wallet::where(IekModel::CONDITION)
                ->where(IekModel::UID,$id)
                ->first();
            $wallet = new Wallet();
            $wallet->person_id = $id;
            if($action == 'close'){
                if(!is_null($res) && $res->is_freeze == true){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage("请不要重复操作");
                    return view('message.formResult',['result'=>$err]);
                }elseif(!is_null($res) && $res->is_freeze == false){
                    $re = $wallet->where(IekModel::UID,$res->person_id)
                        ->update([
                            IekModel::IsFreeze => true
                        ]);
                }else{
                    $wallet->is_freeze = true;
                    $re = $wallet->save();
                }
                if($re){
                    $err->setError(Errors::OK);
                    $err->setMessage("钱包冻结成功");
                }else{
                    $err->setError(Errors::FAILED);
                    $err->setMessage("钱包冻结失败");
                }

                $ckReason = Reason::existReason('冻结钱包资金','wallet','冻结钱包资金');
                if($ckReason){
                    $reason = new Reason();
                    $reason->reason = '冻结钱包资金';
                    $reason->type = 'wallet';
                    $reason->open_reason = '冻结钱包资金';
                    $reason->save();
                }else{
                    $reason = Reason::queryReason('冻结钱包资金','wallet','冻结钱包资金');
                }

                $log = new ManageLogs();
                $log -> operator_id = session('login.id');
                $log -> reason_id = $reason->id;
                $log -> table_name = $wallet->getDataTable();
                if(!is_null($res) && $res->is_freeze == false){
                    $log -> row_id = $res->id;
                    $log -> content = json_encode(\App\IekModel\Version1_0\Wallet::getRecords([IekModel::ID => $res->id]));
                }elseif(is_null($res)){
                    $log -> row_id = $wallet->id;
                    $log -> content = json_encode(\App\IekModel\Version1_0\Wallet::getRecords([IekModel::ID => $wallet->id]));
                }
                $log -> memo = "冻结钱包资金";
                $log -> save();

                $mails = PersonMail::with(['mailDomain'=>
                    function($query){
                        $query->with('mail')
                            ->with('domain');
                    }])
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::UID,$id)
                    ->first();
                if(!is_null($mails)){
                    $mail = $mails->mailDomain->mail->mail.'@'.$mails->mailDomain->domain->domain;
                    $this->sendReply('钱包资金已被冻结',$mail,$id);
                }
                $phones = PersonIdentity::with('identity.phone')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::UID,$id)
                    ->first();
                if(!is_null($phones)){
                    $phone = $phones->identity->phone->phone;
                    $sendingData = ['registerCode' => '：钱包资金已被冻结，钱包支付功能暂停使用',
                        'registerType' => 'phone',
                        'target' => $phone,
                        'type' => 'FreezeWallet',
                        'act' => VerifyAction::ACT_CHANGE_INFO,
                    ];
                    $args = new EventArguments(null, $sendingData, null);
                    event(new FreezeWallet($args));
                }
            }elseif($action == 'open'){
                if(!is_null($res) && $res->is_freeze == false){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage("请不要重复操作");
                    return view('message.formResult',['result'=>$err]);
                }
                $re = $wallet->where(IekModel::UID,$id)
                    ->update([
                        IekModel::IsFreeze => false
                    ]);
                if($re){
                    $err->setError(Errors::OK);
                    $err->setMessage("钱包解冻成功");
                }else{
                    $err->setError(Errors::FAILED);
                    $err->setMessage("钱包解冻失败");
                }

                $ckReason = Reason::existReason('解冻钱包资金','wallet','解冻钱包资金');
                if($ckReason){
                    $reason = new Reason();
                    $reason->reason = '解冻钱包资金';
                    $reason->type = 'wallet';
                    $reason->open_reason = '解冻钱包资金';
                    $reason->save();
                }else{
                    $reason = Reason::queryReason('解冻钱包资金','wallet','解冻钱包资金');
                }
                $log = new ManageLogs();
                $log -> operator_id = session('login.id');
                $log -> reason_id = $reason->id;
                $log -> table_name = $wallet->getDataTable();
                $log -> row_id = $res->id;
                $log -> memo = "解冻钱包资金";
                $log -> content = json_encode(\App\IekModel\Version1_0\Wallet::getRecords([IekModel::ID => $res->id]));
                $log -> save();

                $mails = PersonMail::with(['mailDomain'=>
                    function($query){
                        $query->with('mail')
                            ->with('domain');
                    }])
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::UID,$id)
                    ->first();
                if(!is_null($mails)){
                    $mail = $mails->mailDomain->mail->mail.'@'.$mails->mailDomain->domain->domain;
                    $this->sendReply('钱包已恢复正常使用',$mail,$id);
                }
                $phones = PersonIdentity::with('identity.phone')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::UID,$id)
                    ->first();
                if(!is_null($phones)){
                    $phone = $phones->identity->phone->phone;
                    $sendingData = ['registerCode' => '：钱包已解冻，可继续使用钱包支付功能',
                        'registerType' => 'phone',
                        'target' => $phone,
                        'type' => 'FreezeWallet',
                        'act' => VerifyAction::ACT_CHANGE_INFO,
                    ];
                    $args = new EventArguments(null, $sendingData, null);
                    event(new FreezeWallet($args));
                }
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }

    public function sendReply($content , $Email = null , $personId=null){
        if(is_null($Email)){
            return ;
        }
        if(is_null($personId)){
            $nick = '亲爱的海艺客er';
        }else{
            $nick = PersonNick::getActiveNick($personId);
            $nick = $nick->nick;
        }
        Mail::send('admin.mail.walletFreeze', ['result' => $content], function($message) use($Email , $nick)
        {
            $message->to($Email, $nick)->subject('HIYIK平台处理消息！');
        });
    }
}
