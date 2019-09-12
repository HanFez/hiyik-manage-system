<?php

namespace App\Http\Controllers\Table;

use App\IekModel\Version1_0\AnnounceReview;
use App\IekModel\Version1_0\Constants\AnnounceStatus;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Announce;
use Illuminate\Support\Facades\DB;

class AnnounceController extends Controller
{
    /**
     * create an announce
     *
     * @param Request $request
     * @return mixed
     */
    public function createAnnounce(Request $request){
        $err = new Error();
        $uid = session('login.id');
        $check = $this->checkAnnounceData($request);
        if($check->statusCode != 0){
            return view('admin.announce.create',['result'=>$check]);
        }
        //开启事务
        DB::beginTransaction();
        try{
            $announce = new Announce();
            $announce -> {IekModel::CONTENT} = $request->input(IekModel::CONTENT);
            $announce -> {IekModel::BEGIN_AT} = $request->input(IekModel::BEGIN_AT);
            $announce -> {IekModel::END_AT} = $request->input(IekModel::END_AT);
            $announce -> {IekModel::OID} = $uid;
            $announce -> {IekModel::MEMO} = $request->input(IekModel::MEMO);
            $announce -> {IekModel::TITLE} = $request->input(IekModel::TITLE);
            $announce -> save();

            $announceReview = new AnnounceReview();
            $announceReview ->announce_id = $announce->id;
            $announceReview ->save();
            DB::commit();
            //中间逻辑代码 DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            //接收异常处理并回滚 DB::rollBack();
        }
        return view('admin.announce.create',['result'=>$err]);
    }

    /**
     * check create an announce params
     *
     * @param Request $request
     * @return Error
     */
    public function checkAnnounceData(Request $request){
        $err = new Error();
        $title = $request->input(IekModel::TITLE);
        $content = $request->input(IekModel::CONTENT);
        $begin_at = $request->input(IekModel::BEGIN_AT);
        $end_at = $request->input(IekModel::END_AT);
        if(is_null($content) || is_null($begin_at) || is_null($end_at) || is_null($title)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('content , title , begin_at and end_at can not null');
        }
        if(date('Y-m-d',strtotime($begin_at)) > date('Y-m-d',strtotime($end_at))){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('开始时间必须早于结束时间');
        }
        return $err;
    }

    /**
     * modify an announce
     *
     * @param Request $request
     * @return mixed
     */
    public function modifyAnnounce(Request $request , $announceId){
        $err = new Error();
        $uid = session('login.id');
        $check = $this->checkAnnounceData($request);
        if($check->statusCode != 0){
            return view('admin.announce.create',['result'=>$check]);
        }
        DB::beginTransaction();
        try{
            Announce::where(IekModel::ID,$announceId)
                ->update([IekModel::CONTENT => $request->input(IekModel::CONTENT),
                    IekModel::BEGIN_AT => $request->input(IekModel::BEGIN_AT),
                    IekModel::END_AT => $request->input(IekModel::END_AT),
                    IekModel::OID => $uid,
                    IekModel::MEMO => $request->input(IekModel::MEMO),
                    IekModel::TITLE => $request->input(IekModel::TITLE),
                    IekModel::ACTIVE => true
                ]);
            AnnounceReview::where(IekModel::ANNOUNCE_ID,$announceId)
                ->update([
                    IekModel::STATUS => 0
                ]);
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('admin.announce.create',['result'=>$err]);
    }

    /**
     * delete an announce
     *
     * @param Request $request
     * @return mixed
     */
    function deleteAnnounce(Request $request,$announceId){
        $err = new Error();
        $uid = session('login.id');
        $memo = $request->input(IekModel::MEMO);
        if(is_null($announceId)){
            $err -> setError(Errors::INVALID_PARAMS);
            $err -> setMessage('announceId can not null');
            return view('admin.announce.create',['result'=>$err]);
        }
        if(!Announce::isExist($announceId)){
            $err -> setError(Errors::INVALID_PARAMS);
            $err -> setMessage('invalid announceId');
            return view('admin.announce.create',['result'=>$err]);
        }
        DB::beginTransaction();
        try{
            $update = [];
            $update[IekModel::REMOVED] = TRUE;
            $update[IekModel::OID] = $uid;
            if(!is_null($memo)){
                $update[IekModel::MEMO] = $memo;
            }

            Announce::where(IekModel::ID,$announceId)
                ->update($update);
            AnnounceReview::where(IekModel::ANNOUNCE_ID,$announceId)
                ->update([
                    IekModel::STATUS => 1
                ]);
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('admin.announce.create',['result'=>$err]);
        }
        return view('admin.announce.announce-close',['result'=>$err]);
    }

    /**
     * get announce by id
     *
     * @param $announceId
     * @return mixed
     */
    public function getAnnounce($announceId){
        $err = new Error();
        if(!Announce::isExist($announceId)){
            $err->setError(ErrorS::INVALID_PARAMS);
            $err->setMessage('invalid announceId');
            return view('message.formResult', ['result' => $err]);
        }
        $announce = Announce::with(['announceReview'=>
            function($query){
                $query->with('operator');
            }])
            ->where(IekModel::ID,$announceId)
            ->with('operator')
            ->get();
        $err->setData($announce[0]);
        $status = AnnounceStatus::getConstants();
        $err -> status = $status;
        return $err;
    }


    public function getViewAnnounce($announceId){
        $err = $this->getAnnounce($announceId);
        return view('admin.announce.announce',['announce'=>$err]);

    }

    public function getModifyAnnounce($announceId){
        $err = $this->getAnnounce($announceId);
        return view('admin.announce.createAnnounce',['result'=>$err]);
    }

    /**
     * get announce with given type
     *
     * @param Request $request
     * @return mixed
     */
    public function getAnnounceList(Request $request){
        $err = new Error();
        $type = $request->input('type');
        switch ($type) {
            case 'pass':
                $announce = $this->getPassAnnounce();
                break;
            case 'forbidden':
                $announce = $this->getForbiddenAnnounce();
                break;
            case 'audit':
                $announce = $this->getAuditAnnounce();
                break;
            case 'active':
                $announce = $this->getActiveAnnounce();
                break;
            case 'finish':
                $announce = $this->getFinishAnnounce();
                break;
            case 'delete':
                $announce = $this->getDeleteAnnounce();
                break;
            default:
                $announce = Announce::with('AnnounceReview')->get();
                break;
        }
        $take = $request->input('take');
        $skip = $request->input('skip');
        $total = $announce->count();
        if($take != null && $skip != null){
            $announce = $announce->slice($skip,$take);
        }
        $err->setData($announce);
        $typeArray = ['pass'=>'pass','forbidden'=>'forbidden','audit'=>'audit','active'=>'active','finish'=>'finish','delete'=>'delete'];
        $err -> typeArray = $typeArray;
        $err -> take = $take;
        $err -> skip = $skip;
        $err -> total = $total;
        $err -> type = $type;
        return view('admin.announce.announceList',['result'=>$err]);
    }

    /**
     * get pass audit announces
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getPassAnnounce(){
        $announce = Announce::whereHas('AnnounceReview',
            function($query){
                $query ->where(IekModel::STATUS,2);
            })
            ->get();
        return $announce;
    }

    /**
     * get forbidden announces
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getForbiddenAnnounce(){
        $announce = Announce::whereHas('AnnounceReview',
            function($query){
                $query ->where(IekModel::STATUS,1);
            })
            ->get();
        return $announce;
    }

    /**
     * get un_audit announces
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAuditAnnounce(){
        $announce = Announce::whereHas('AnnounceReview',
            function($query){
                $query ->where(IekModel::STATUS,0);
            })
            ->where(IekModel::END_AT,'>',date('Y-m-d',time()))
            ->get();
        return $announce;
    }

    /**
     * get active announces
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getActiveAnnounce(){
        $announce = Announce::whereHas('AnnounceReview',
            function($query){
                $query ->where(IekModel::STATUS,2);
            })
            ->where(IekModel::END_AT,'>',date('Y-m-d',time()))
            ->get();
        return $announce;
    }

    /**
     * get finish announces
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getFinishAnnounce(){
        $announce = Announce::whereHas('AnnounceReview',
            function($query){
                $query ->where(IekModel::STATUS,2);
            })
            ->with('AnnounceReview')
            ->where(IekModel::END_AT,'<',date('Y-m-d',time()))
            ->get();
        return $announce;
    }

    public function getDeleteAnnounce(){
        $announce = Announce::whereHas('AnnounceReview',
            function($query){
                $query ->where(IekModel::STATUS,1);
            })
            ->get();
        return $announce;
    }
    /**
     * audit announce
     *
     * @param Request $request
     * @return mixed
     */
    public function AuditAnnounce(Request $request , $announceId){
        $err = new Error();
        $uid = session('login.id');
        $status = $request->input('reason');
        $memo = $request->input(IekModel::MEMO);
        if(is_null($announceId) || is_null($status)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('announceId and status can not null');
            return view('admin.announce.create',['result'=>$err]);
        }
        $announceExist = Announce::isExist($announceId);
        if(!$announceExist){
            $err->setError(Errors::INVALID_PARAMS);
            $err ->setMessage('invalid announceId');
            return view('admin.announce.create',['result'=>$err]);
        }
        $update = [];
        $update[IekModel::STATUS] = $status;
        $update[IekModel::OID] = $uid;
        if(!is_null($memo)){
            $update[IekModel::MEMO] = $memo;
        }
        $announce = AnnounceReview::where(IekModel::ANNOUNCE_ID,$announceId)
            ->update($update);
        if(!$announce){
            $err->setError(Errors::UNKNOWN_ERROR);
        }
        return view('admin.announce.announce-close',['result'=>$err]);
    }
}
