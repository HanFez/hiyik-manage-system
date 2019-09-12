<?php

namespace App\Http\Controllers\Table;

use App\Events\NotifyEvent;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Version1_0\AdviceHandle;
use App\IekModel\Version1_0\AdviceReply;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\FilterKeyword;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ManageReply;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\Person;
use App\IekModel\Version1_0\PersonNick;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\IekModel\Version1_0\Error;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Advice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

class AdviceController extends Controller
{
    /**
     * get advice list
     *
     * @param Request $request
     * @return mixed
     */
    public function getAdviceList(Request $request){
        $err = new Error();
        $audit = $request->input('isDeal');
        if($audit != 'false'){
            $advice = Advice::whereHas('adviceHandle')
                ->with(['person.personNick'=>function($query){
                    $query->where(IekModel::ACTIVE,true)
                        ->with('nick');
                }])
                ->with('adviceHandle.adviceReply')
                ->with('adviceHandle.adviceOperator')
                ->where(IekModel::VIEWED,true)
                ->where(IekModel::CONDITION)
                ->get();
        }else{
            $advice = Advice::doesntHave('adviceHandle')
                ->with(['person.personNick'=>function($query){
                    $query->where(IekModel::ACTIVE,true)
                        ->with('nick');
                }])
                ->where(IekModel::VIEWED,false)
                ->where(IekModel::CONDITION)
                ->get();
        }
        $count = $advice->count();
        $take = $request->input('take');
        $skip = $request->input('skip');
        if(!is_null($take) && !is_null($skip)){
            $advice = $advice->slice($skip,$take);
        }
        $replyList = ManageReply::all();
        $err->setData($advice);
        $err->replyList = $replyList;
        $err->total = $count;
        $err->take = $take;
        $err->skip = $skip;
        $err->isDeal = $audit;
        $err->setData($advice);
        return view('admin.advice.advice', ['result'=>$err]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * no mail ignore
     */
    public function ignoreAdvice($id){
        $err = new Error();
        $ck = Advice::where(IekModel::VIEWED,true)->find($id);
        if(!is_null($ck)){
            $err->setError(Errors::INVALID_DATA);
            $err->setMessage('请不要重复操作');
            return view('message.formResult', ['result' => $err]);
        }
        $data = [];
        $data['operator_id'] = session('login.id');
        $data['advice_id'] = $id;
        $data['score'] = 0;
        $data['memo'] = '垃圾意见建议';
        try{
            DB::begintransAction();
            Advice::where(IekModel::ID,$id)->update([IekModel::VIEWED=>true]);
            $adviceHandle = new AdviceHandle();
            $re = $adviceHandle->insert($data);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'已忽略','操作失败',$re);
    }
    public function recoverAdvice($id){
        $err = new Error();
        $ck = AdviceHandle::where(IekModel::ADVICE_ID,$id)->get();
        if($ck->isEmpty()){
            $err->setError(Errors::INVALID_DATA);
            $err->setMessage('请不要重复操作');
            return view('message.formResult', ['result' => $err]);
        }
        try{
            DB::begintransAction();
            Advice::where(IekModel::ID,$id)->update([IekModel::VIEWED=>false]);
            $re = AdviceHandle::where(IekModel::ADVICE_ID,$id)->delete();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'操作成功','操作失败',$re);
    }
    /**
     * deal advice
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function dealAdvice(Request $request , $id){
        $err = new Error();
        $operator_id = session('login.id');
        $ck = Advice::where(IekModel::VIEWED,true)->find($id);
        if(!is_null($ck)){
            $err->setError(Errors::INVALID_DATA);
            $err->setMessage('已处理');
            return view('message.formResult', ['result' => $err]);
        }
        $dataCheck = $this->checkAdviceReplyData($request , $id);
        if($dataCheck->statusCode != 0){
            return view('message.formResult', ['result' => $dataCheck]);
        }
        Advice::where(IekModel::ID,$id)->update([IekModel::VIEWED=>true]);
        $adviceHandle = new AdviceHandle();
        $adviceHandle->advice_id = $id;
        $adviceHandle->operator_id = $operator_id;
        $adviceHandle->score = $request->input('score');
        $adviceHandle->is_accept = true;
        $memo = $request->input('memo');
        if(!is_null($memo)){
            $adviceHandle->memo = $memo;
        }
        if(isset($dataCheck->replyContent)){
            $adviceHandle->reply_id = $dataCheck->replyContent->id;
            $result = $adviceHandle->save();

            $advice = Advice::find($id);
            if(is_null($advice->person_id) && is_null($advice->email)){
                $err->setError(Errors::FAILED);
                $err->setMessage('用户未填写邮箱或未登录，请忽略此意见建议！');
                return view('message.formResult', ['result' => $err]);
            }else if(!is_null($advice->person_id)){
                //notify
                $params = new \stdClass();
                $params->action = 'deal advice';
                $params->lang = 'deal advice';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $advice->person_id;
                $params->targetId = $adviceHandle->id;
                $args = new NotifyEventArguments(null,\App\IekModel\Version1_0\Notify\AdviceHandle::class, $params);
                event(new NotifyEvent($args));
            }else if(!is_null($advice->email)){
                //email
                $this->sendReply($dataCheck->replyContent->content , $advice->email , $advice->person_id);
            }
            if(!$result){
                $err->setError(Errors::UNKNOWN_ERROR);
                return view('message.formResult', ['result' => $err]);
            }
            $err->setData($result);
            return view('message.formResult', ['result' => $err]);
        }
    }

    /**
     * check data format
     *
     * @param Request $request
     * @param $id
     * @return Error
     */
    public function checkAdviceReplyData(Request $request , $id){
        $err = new Error();
        $adviceExist = Advice::isExists($id);
        if(!$adviceExist){
            return $this->viewReturn(Errors::INVALID_PARAMS,'无效的ID','id');
        }
        $score = $request->input('score');
        if(is_null($score)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'分值不能为空','score');
        }
        $replyType = $request->input('replyType');
        if(!is_null($replyType)){
            $reply = $request->input('reply');
            if($replyType == 'id'){
                $replyContent = ManageReply::where(IekModel::ID,$reply)->first();
                if(is_null($replyContent)){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('invalid reply id');
                    return view('message.formResult', ['result' => $err]);
                }
                $err->replyContent = $replyContent;
            }else{
                if(is_null($reply)){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('请输入回复内容');
                    return view('message.formResult', ['result' => $err]);
                }
                $replyContent = ManageReply::where(IekModel::CONTENT,$reply)
                    ->first();
                if(is_null($replyContent)){
                    $replyContent = new ManageReply();
                    $replyContent->content = $reply;
                    $replyContent->save();
                    $err->replyContent = $replyContent;
                }
            }
        }
        return $err;
    }


    /**
     * send reply email
     *
     * @param $replyContent
     * @param null $Email
     * @param null $personId
     */
    public function sendReply($replyContent , $Email = null , $personId=null){
        if(is_null($Email)){
            return ;
        }
        if(is_null($personId)){
            $nick = '亲爱的海艺客er';
        }else{
            $nick = PersonNick::getActiveNick($personId);
            $nick = $nick->nick;
        }
        Mail::send('admin.mail.adviceReply', ['result' => $replyContent], function($message) use($Email , $nick)
        {
            $message->to($Email, $nick)->subject('意见回复！');
        });
    }
}
