<?php

namespace App\Http\Controllers\Table;

use App\Events\NotifyEvent;
use App\Http\Controllers\Operation\ForbiddenController;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\PersonAction;
use App\IekModel\Version1_0\Constants\ReasonType;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Iwall;
use App\IekModel\Version1_0\IwallComment;
use App\IekModel\Version1_0\IwallLike;
use App\IekModel\Version1_0\IwallOfficial;
use App\IekModel\Version1_0\IwallOfficialNotify;
use App\IekModel\Version1_0\IwallTitle;
use App\IekModel\Version1_0\IwallViewer;
use App\IekModel\Version1_0\ManageLogs;
use App\IekModel\Version1_0\Nick;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\Person;
use App\IekModel\Version1_0\Reason;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IWallController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * get iwall list
     */
    public function iwallList(){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $title = request()->input('title');
        if(!is_null($title)){
            $iwallId = $this->getIwallByTitle($title);
        }
        $time = session('time');
        if(is_null($time) || (!is_null($skip) && $skip == 0)){
            $time = date('Y-m-d H:i:s');
            session(['time' => $time]);
        }

        $type = request()->input('type');
        switch($type){
            case 'normal':
                $iwall = $this->normalIwall();
                $iwall = $iwall->where(IekModel::CREATED,'<',$time);
                if(!is_null($title)){
                    $iwall = $iwall->whereIn(IekModel::ID,$iwallId);
                }
                $count = $iwall->count();
                if($take != null && $skip != null){
                    $iwall = $iwall->slice($skip,$take);
                }
                $err->setData($iwall);
                break;
            case 'recommend':
                $iwall = $this->recommendIwall();
                $iwall = $iwall->where(IekModel::CREATED,'<',$time);
                if(!is_null($title)){
                    $iwall = $iwall->whereIn(IekModel::ID,$iwallId);
                }
                $count = $iwall->count();
                if($take != null && $skip != null){
                    $iwall = $iwall->slice($skip,$take);
                }
                $err->setData($iwall);
                break;
            case 'draft':
                $iwall = $this->draftIwall();
                $iwall = $iwall->where(IekModel::CREATED,'<',$time);
                if(!is_null($title)){
                    $iwall = $iwall->whereIn(IekModel::ID,$iwallId);
                }
                $count = $iwall->count();
                if($take != null && $skip != null){
                    $iwall = $iwall->slice($skip,$take);
                }
                $err->setData($iwall);
                break;
            case 'forbiddenIw':
                $iwall = $this->forbiddenIwall();
                $iwall = $iwall->where(IekModel::CREATED,'<',$time);
                if(!is_null($title)){
                    $iwall = $iwall->whereIn(IekModel::ID,$iwallId);
                }
                $count = $iwall->count();
                if($take != null && $skip != null){
                    $iwall = $iwall->slice($skip,$take);
                }
                $err->setData($iwall);
                break;
            case 'deleted':
                $iwall = $this->deletedIwall();
                $iwall = $iwall->where(IekModel::CREATED,'<',$time);
                if(!is_null($title)){
                    $iwall = $iwall->whereIn(IekModel::ID,$iwallId);
                }
                $count = $iwall->count();
                if($take != null && $skip != null){
                    $iwall = $iwall->slice($skip,$take);
                }
                $err->setData($iwall);
                break;
            case 'person':
                $personId = request()->input('personId');
                $nick = new Person();
                $nick->id = $personId;
                $nick = $nick->getNick();
                if(is_null($personId)){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('personId not null');
                    break;
                }
                $iwalls = $this->getIwallsByPerson($personId);
                if(!is_null($title)){
                    $iwalls = $iwalls->whereIn(IekModel::ID,$iwallId);
                }
                $iwalls = $iwalls->where(IekModel::CREATED,'<',$time);
                $count = $iwalls->count();
                if($take != null && $skip != null){
                    $iwalls = $iwalls->slice($skip,$take);
                }
                $err->setData($iwalls);
                $err->author = $nick;
                break;
            default:
                $err ->setError(Errors::INVALID_PARAMS);
                $err ->setMessage('type not null');
        }
        $err -> type = $type;
        $err -> total = $count;
        $err -> skip = $skip;
        $err -> take = $take;
        $err -> search = $title;
        return view('admin.iwall.iwallList',['result'=>$err]);
    }
    /**
     * search
     */
    public function getIwallByTitle($title){
        $iwallId = IwallTitle::whereHas('title.description' ,
            function($query) use($title){
                $query->where(IekModel::CONTENT,'like','%'.$title.'%');
            })
            ->with('title.description')
            ->where(IekModel::CONDITION)
            ->pluck(IekModel::WID);
        return $iwallId;
    }
    /**
     * normal iwall
     */
    public function normalIwall(){
        $first = Iwall::with(['iwallCover'=>
            function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('cover.norms');
            }])
            ->with(['iwallTitle'=>function($query){
                $query->with('title.description')
                    ->where(IekModel::ACTIVE,true);
            }])
            ->where(IekModel::CONDITION)
            ->where(IekModel::PUBLISH,true)
            ->where(IekModel::IS_FORBIDDEN,false)
            ->whereHas('official',function($query){
                $query->where(IekModel::CONDITION);
            })
            ->select(DB::raw('*, \'t\'::boolean as is_official'));
        $second = Iwall::with(['iwallCover'=>
            function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('cover.norms');
            }])
            ->with(['iwallTitle'=>function($query){
                $query->with('title.description')
                    ->where(IekModel::ACTIVE,true);
            }])
            ->where(IekModel::CONDITION)
            ->where(IekModel::PUBLISH,true)
            ->where(IekModel::IS_FORBIDDEN,false)
            ->whereDoesntHave('official',function($query){
                $query->where(IekModel::CONDITION);
            })
            ->select(DB::raw('*, \'f\'::boolean as is_official'));
        $iwall = $first->union($second)
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        return $iwall;
    }
    /**
     * special iwall
     */
    public function recommendIwall(){
        $iwall = Iwall::with(['iwallCover'=>
            function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('cover.norms');
            }])
            ->with(['iwallTitle'=>function($query){
                $query->with('title.description')
                    ->where(IekModel::ACTIVE,true);
            }])
            ->whereHas('official',function($query){
                $query->where(IekModel::CONDITION);
            })
            ->where(IekModel::CONDITION)
            ->where(IekModel::PUBLISH,true)
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        return $iwall;
    }
    /**
     * draft iwall
     */
    public function draftIwall(){
        $iwall = Iwall::with(['iwallCover'=>
            function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('cover.norms');
            }])
            ->with(['iwallTitle'=>function($query){
                $query->with('title.description')
                    ->where(IekModel::ACTIVE,true);
            }])
            ->where(IekModel::CONDITION)
            ->where(IekModel::PUBLISH,false)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $iwall;
    }
    /**
     * forbidden iwall
     */
    public function forbiddenIwall(){
        $iwall = Iwall::with(['iwallCover'=>
            function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('cover.norms');
            }])
            ->with(['iwallTitle'=>function($query){
                $query->with('title.description')
                    ->where(IekModel::ACTIVE,true);
            }])
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_FORBIDDEN,true)
            ->where(IekModel::PUBLISH,true)
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        return $iwall;
    }
    /**
     * delete iwall
     */
    public function deletedIwall(){
        $iwall = Iwall::with(['iwallCover'=>
            function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('cover.norms');
            }])
            ->with(['iwallTitle'=>function($query){
                $query->with('title.description')
                    ->where(IekModel::ACTIVE,true);
            }])
            ->where(IekModel::ACTIVE,false)
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        return $iwall;
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * get iwall content
     */
    public function iwall($iid){
        $err = new Error();
        if(is_null($iid)){
            $err -> setError(Errors::INVALID_IWALL_ID);
            return view('admin.iwall.iwall',['iwall'=>$err]);
        }
        $iwall = Iwall::with(['iwallTitle'=>function($query){
            $query->with(['title.description.manageLog'=>
                function($q){
                    $q->with('reason')
                        ->with('operator');}])
                ->where(IekModel::ACTIVE,true);}])
            ->with(['iwallCover'=>function($query){
                $query->with(['cover'=>function($q){
                    $q->with(['manageLog'=>function($qq){
                        $qq->with('reason')
                            ->with('operator');
                    }])
                        ->with('norms');
                }])
                    ->where(IekModel::ACTIVE,true);
            }])
            ->with(['iwallDescriptions'=>function($query){
                $query->with('description.styleText')
                    ->where(IekModel::ACTIVE,true);
            }])
            ->with(['iwallTags'=>function($query){
                $query->with('tags')
                    ->where(IekModel::ACTIVE,true);
            }])
            ->with('iwallScene.scene')
            ->with('iwallCrowd.crowd')
            ->with('iwallSex.sex')
            ->with(['iwallPerson.person'=>function($query){
                $query->with(['personNick'=>function($q){
                    $q->where('is_active',true)
                        ->with('nick');
                }])
                    ->where(IekModel::IS_FORBIDDEN,false);
            }])
            ->with(['iwallForbidden'=>function($query){
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('operator')
                    ->with('reason');
            }])
            ->with('official')
            ->with(['officialReason'=>function($query){
                $query->with('reason')
                    ->with('operator')
                    ->orderBy(IekModel::CREATED,'desc');
            }])
            ->with(['wall.wall.wallProduct.product'=>function($query){
                $query->with('productDefine')
                    ->with(['border'=>function($query){
                        $query->with('materialDefine.facade')
                            ->with('material')
                            ->with('shape')
                            ->with('line');
                    }])
                    ->with(['core'=>function($query) {
                        $query->with('material')
                            ->with('materialDefine.facade')
                            ->with('coreHandle')
                            ->with(['coreContent.content' => function ($q) {
                                $q->with(['corePublication'=>function($q){
                                    $q->with('pubImg.image.norms')
                                        ->with('title.title.description');
                                }])
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
                    ->with('postMaker.maker')
                    ->with('person.personNick.nick');
            }])
            ->find($iid);

        if(is_null($iwall)){
            $err -> setError(Errors::INVALID_IWALL_ID);
            return view('admin.iwall.iwall',['iwall'=>$err]);
        }
        $iwall->like_count = IwallLike::getLikeCount($iid);
        $iwall->comment_count = IwallComment::getCommentCount($iid);
        $iwall->view_count = IwallViewer::getViewCount($iid);
        $reason = Reason::getReason();
        $iwall->reasons = $reason;
        $err->setData($iwall);
        return view('admin.iwall.iwall',['iwall'=>$err]);
    }

    /**
     * official iwall
     */
    public function official($iid){
        $err = new Error();
        if(is_null($iid)){
            $err->setError(Errors::INVALID_IWALL_ID);
            return view('admin.iwall.official',['result'=>$err]);
        }
        $operator_id = session('login.id');

        $iwall = Iwall::checkIwall($iid);
        if(!$iwall){
            $err->setError(Errors::INVALID_IWALL_ID);
            return view('admin.iwall.official',['result'=>$err]);
        }
        $reasonId = ForbiddenController::checkReason(request(),ReasonType::OFFICIAL);
        if($reasonId->statusCode != 0){
            return view('admin.iwall.official',['result'=>$reasonId]);
        }
        $iwallForbidden = Iwall::where(IekModel::ID,$iid)
            ->where(IekModel::IS_FORBIDDEN,true)
            ->count();
        if($iwallForbidden){
            $err -> setError(Errors::NON_COMPLIANCE);
            $err -> setMessage('iwall is banned ');
            return view('admin.iwall.official',['result'=>$err]);
        }

        DB::beginTransaction();
        try{
            $iwallOfficialStatus = IwallOfficial::isOfficial($iid);
            if($iwallOfficialStatus){
                $err -> setError(Errors::ACTION_ALREADY_BE_DONE);
                return view('admin.iwall.official',['result'=>$err]);
            }
            $iwallOfficial = new IwallOfficial();
            $iwallOfficial -> iwall_id = $iid;
            $iwallOfficial -> save();

            $log = new ManageLogs();
            $log -> operator_id = $operator_id;
            $log -> reason_id = $reasonId->data;
            $log -> table_name = $iwallOfficial->getDataTable();
            $log -> row_id =$iid;
            $log -> content = json_encode(\App\IekModel\Version1_0\IwallOfficial::getRecords([IekModel::WID => $iid]));
            $log -> save();

            $officials = $iwallOfficial->with('iwallPerson')
                ->where(IekModel::WID,$iid)
                ->orderBy(IekModel::CREATED,'desc')
                ->first();
            if($officials->is_removed == false){
                $params = new \stdClass();
                $params->action = PersonAction::OFFICIAL;
                $params->lang = 'iwall official';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $officials->iwallPerson->person_id;
                $params->targetId = $officials->id;
                $params->reasonId = $log->reason_id;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\IwallOfficial::class, $params);
                event(new NotifyEvent($args));
            }
            DB::commit();
            //中间逻辑代码 DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            //接收异常处理并回滚 DB::rollBack();
        }
        return view('admin.iwall.official',['result'=>$err]);
    }

    /**
     * unOfficial iwall
     */
    public function unOfficial($iid){
        $err = new Error();
        $operator_id = session('login.id');
        if(is_null($iid)){
            $err->setError(Errors::INVALID_IWALL_ID);
            return view('admin.iwall.unOfficial',['result'=>$err]);
        }

        $reasonId = ForbiddenController::checkReason(request(),ReasonType::UNOFFICIAL);
        if($reasonId->statusCode != 0){
            return view('admin.iwall.unOfficial',['result'=>$reasonId]);
        }

        $iwall = Iwall::checkIwall($iid);
        if(!$iwall){
            $err->setError(Errors::INVALID_IWALL_ID);
            return view('admin.iwall.unOfficial',['result'=>$err]);
        }
        DB::beginTransaction();
        try{
            IwallOfficial::where(IekModel::WID,$iid)
                ->where(IekModel::CONDITION)
                ->update([
                    IekModel::REMOVED => true
                ]);

            $log = new ManageLogs();
            $log -> operator_id = $operator_id;
            $log -> reason_id = $reasonId->data;
            $log -> memo = request()->input('memo');
            $log ->table_name = 'tblIwallOfficials';
            $log ->row_id = $iid;
            $log ->content = json_encode(\App\IekModel\Version1_0\IwallOfficial::getRecords([IekModel::WID => $iid]));
            $log->save();

            $officials = IwallOfficial::with('iwallPerson')
                ->where(IekModel::WID,$iid)
                ->orderBy(IekModel::CREATED,'desc')
                ->first();
            if($officials->is_removed == true){
                $params = new \stdClass();
                $params->action = PersonAction::UNOFFICIAL;
                $params->lang = 'iwall unofficial';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $officials->iwallPerson->person_id;
                $params->targetId = $officials->id;
                $params->reasonId = $log->reason_id;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\IwallOfficial::class, $params);
                event(new NotifyEvent($args));
            }
            DB::commit();
            //中间逻辑代码 DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            //接收异常处理并回滚 DB::rollBack();
        }
        return view('admin.iwall.unOfficial',['result'=>$err]);
    }

    /**
     * get iwall by personId
     *
     * @param $personId
     * @return mixed
     */
    public function getIwallsByPerson($personId){
        $firstQuery = Iwall::with(['iwallCover'=>
            function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('cover.norms');
            }])
            ->with(['iwallTitle'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('title.description');
            }])
            ->whereHas('iwallPerson',function($query) use($personId){
                $query->where(IekModel::UID,$personId);
            })
            ->where(IekModel::ACTIVE,true)
            ->get();
            /*->whereHas('official',function($query){
                $query->where(IekModel::CONDITION);
            })
            ->select(DB::raw('*, \'t\'::boolean as is_official'));*/
/*dd($firstQuery);
        $secondQuery = Iwall::with(['iwallCover'=>
            function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('cover.norms');
            }])
            ->with(['iwallTitle'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('title.description');
            }])
            ->with(['iwallPerson.person'=>function($query) use($personId){
                $query->where('id',$personId);
            }])
            ->where(IekModel::ACTIVE,true)
            ->select(DB::raw('*, \'f\'::boolean as is_official'))
            ->whereDoesntHave('official',function($query){
                $query->where(IekModel::CONDITION);
            });

        $iwalls = $firstQuery->union($secondQuery)
            ->orderBy(IekModel::UPDATED, 'desc')
            ->get();
        dd($iwalls);*/
        return $firstQuery;
    }

}
