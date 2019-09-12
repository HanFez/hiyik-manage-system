<?php

namespace App\Http\Controllers\Table;

use App\Events\NotifyEvent;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ManageLogs;
use App\IekModel\Version1_0\ManageReply;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\Person;
use App\IekModel\Version1_0\PersonMail;
use App\IekModel\Version1_0\Reason;
use App\IekModel\Version1_0\ReportHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Report;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * get report list with given condition
     *
     * @param Request $request
     * @return mixed
     */
    public function getReportList(Request $request){
        $err = new Error();
        $type = $request->input('type');
        $audit = $request->input('isDeal');
        $report = Report::where(IekModel::TARGET_TYPE, $type);
        if ($audit == 'false') {
            $report = $report->doesntHave('reportHandle');
        }else{
            $report = $report->whereHas('reportHandle');
        }
        switch ($type) {
            case 'comment':
                $report = $report->whereHas('reportComment')
                    ->with('reportComment');
                break;
            case 'message':
                $report = $report->whereHas('reportChatSession')
                    ->with('reportChatSession.message')
                    ->with('reportChatSession.to.personNick.nick');
                break;
            case 'person':
                $report = $report->whereHas('reportPerson')
                        ->with(['reportPerson.personNick'=>
                            function($query){
                                $query->where(IekModel::ACTIVE,true)
                                    ->with('nick');
                            }]);
                break;
            case 'publication':
                $report = $report->whereHas('reportPublicationTitle')
                        ->with(['reportPublicationTitle.title'=>
                            function($query){
                                $query->where(IekModel::ACTIVE,true);
                            }]);
                break;
        }

        $report = $report->with(['reportInformer.personNick'=>
            function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->with('reportHandle.reportOperator')
            ->with('reportHandle.reportReply')
            ->get();
       //dd($report);
        $count = $report->count();
        $take = $request->input('take');
        $skip = $request->input('skip');
        if(!is_null($take) && !is_null($skip)){
            $report = $report->slice($skip,$take);
        }
        $replyList = ManageReply::all();
        $reasonList = Reason::all();
        $err -> setData($report);
        $err -> replyList = $replyList;
        $err -> reasonList = $reasonList;
        $err -> total = $count;
        $err -> take = $take;
        $err -> skip = $skip;
        $err -> isDeal = $audit;
        $err -> type = $type;
        return view('admin.report.reportList', ['result' => $err]);
    }

    /**
     * deal report
     * reply or not
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function dealReport(Request $request , $id){
        $err = new Error();
        $memo = $request->input('memo');
        $operatorId = session('login.id');
        $status = $request->input('status');
        if(is_null($status)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('status can not null');
            return view('message.formResult', ['result' => $err]);
        }
        $report = Report::isExists($id);
        if(!$report){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid report id');
            return view('message.formResult', ['result' => $err]);
        }
        /*if($status == 1){
            $replyType = $request->input('replyType');
            $replyContent = $request->input('reply');
            $replyContent = $this->checkReply($replyType,$replyContent);
            if($replyContent->statusCode != 0){
                return view('message.formResult', ['result' => $replyContent]);
            }
            $personId = Report::where(IekModel::ID,$id)->value('person_id');
            $mail = PersonMail::getMails($personId);
            if(!is_null($mail)){
                $email = $mail[0]->mail->mail.'@'.$mail[0]->domain->domain;
                $personNick = new Person();
                $personNick -> id = $personId;
                $personNick = $personNick->getNick();
                Mail::send('admin.mail.reportReply', ['result' => $replyContent->reply], function($message) use($email , $personNick)
                {
                    $message->to($email, $personNick->nick->nick)->subject('举报回复!');
                });
            }
        }*/
        DB::beginTransaction();
        try{
            $reportHandle = ReportHandle::where(IekModel::REPORT_ID,$id)->first();
            if(is_null($reportHandle)){
                $reportHandle = new ReportHandle();
            }else{
                $manageLog = new ManageLogs();
                $manageLog -> operator_id = $operatorId;
                $manageLog -> table_name = ReportHandle::getDataTable();
                $manageLog -> row_id = $reportHandle ->id;
                $manageLog -> content = json_encode($reportHandle);
                $manageLog -> save();
            }
            $reportHandle -> operator_id = $operatorId;
            $reportHandle -> status = $status;
            $reportHandle -> memo = $memo;
            $reportHandle -> report_id = $id;
            if(isset($replyContent) && isset($replyContent->id)){
                $reportHandle -> reply_id = $replyContent->id;
            }
            $reportHandle -> save();
            //举报处理通知
            if($status == 1){
                //举报人id
                $personId = Report::where(IekModel::ID,$id)->value('person_id');

                $params = new \stdClass();
                $params->action = 'deal report';
                $params->lang = 'deal report';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $personId;
                $params->targetId = $reportHandle->id;
                $args = new NotifyEventArguments(null,\App\IekModel\Version1_0\Notify\ReportHandle::class, $params);
                event(new NotifyEvent($args));
            }

            DB::commit();
            //中间逻辑代码 DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
            //接收异常处理并回滚 DB::rollBack();
        }
        $err->setData($reportHandle);
        return view('message.formResult', ['result' => $err]);
    }

    /**
     * check reply content
     *
     * @param $type
     * @param $content
     * @return Error
     * @return replyContent
     */
    public function checkReply($type,$content){
        $err = new Error();
        if($type == 'id'){
            $reply = ManageReply::where(IekModel::ID,$content)->first();
            if(is_null($reply)){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('invalid reply id');
                return $err;
            }
        }else{
            if(is_null($content)){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('reply can not null');
                return $err;
            }
            $reply = ManageReply::where(IekModel::CONTENT,$content)->first();
            if(is_null($reply)){
                $reply = new ManageReply();
                $reply -> content = $content;
                $reply -> save();
            }
        }
        $err -> reply = $reply->content;
        $err -> id = $reply->id;
        return $err;
    }
}
