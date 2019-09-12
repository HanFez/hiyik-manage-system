<?php

namespace App\Http\Controllers\Table;

use App\Events\NotifyEvent;
use App\Http\Controllers\Operation\ForbiddenController;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Version1_0\Constants\PersonAction;
use App\IekModel\Version1_0\Constants\ReasonType;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ManageLogs;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\PublicationComment;
use App\IekModel\Version1_0\PublicationLike;
use App\IekModel\Version1_0\PublicationOfficial;
use App\IekModel\Version1_0\PublicationTitle;
use App\IekModel\Version1_0\PublicationViewer;
use App\IekModel\Version1_0\Reason;
use App\IekModel\Version1_0\Person;
use Illuminate\Http\Request;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Constants\Errors;

use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Publication;

class PublicationController extends Controller
{
    /**
     * get one publication page
     *
     * @param  Request
     * @return view
     */
    public function publication($publicationId){
        $err = new Error();
        if(is_null($publicationId)){
            $err -> setError(Errors::INVALID_PUBLICATION_ID);
            return view('admin.publication.publication',['publication'=>$err]);
        }
        $publication = Publication::where(IekModel::ID,$publicationId)
            ->with(['images'=>function($query){
                $query->with('image.norms')
                    ->with('image')
                    ->with('imageTitle.description')
                    ->with('imageTitle.styleText')
                    ->with(['image.manageLog'=>function($q){
                        $q->with('reason')
                            ->with('operator');
                    }]);
            }])
            ->with(['publicationTitle'=>function($query){
                $query->with(['title.description.manageLog'=>function($q){
                    $q->with('reason')
                        ->with('operator');
                }]);
            }])
            ->with(['description'=>function($query){
                $query->with(['description.styleText.manageLog'=>function($q){
                    $q->with('reason')
                        ->with('operator');
                }]);
            }])
            ->with('publicationDivider.divider.styleText')
            ->with('publicationBackground.background.styleText')
            ->with(['officialReason'=>function($query){
                $query->where('table_name',PublicationOfficial::getDataTable())
                    ->with('reason')
                    ->with('operator')
                    ->orderBy(IekModel::CREATED,'desc');
            }])
            ->with('publicationTags.tag')
            ->with(['publicationForbidden'=>function($query){
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('operator')
                    ->with('reason');
            }])
            ->with(['cover'=>function($query){
                $query->with('cover.norms')
                ->with(['cover.manageLog'=>function($q){
                    $q->with('reason')
                        ->with('operator');
                }]);
            }])
            ->get();
        if($publication->isEmpty()){
            $err -> setError(Errors::INVALID_PUBLICATION_ID);
            return view('admin.publication.publication',['publication'=>$err]);
        }
        $publication[0] -> like_count = PublicationLike::getLikeCount($publicationId);
        $publication[0] -> comment_count = PublicationComment::getCommentCount($publicationId);
        $publication[0] -> view_count = PublicationViewer::getViewCount($publicationId);
        $publication[0] -> owner = Publication::getOwner($publicationId);

        $reason = Reason::getReason();
        $publication[0] ->reasons = $reason;
        $err->setData($publication[0]);
        return view('admin.publication.publication',['result'=>$err]);
    }

    /**
     * get publication with conditions be given
     *
     * @param Request $request -> type :audit   get need reView publications
     *                                  view    get publication (pass view || forbidden)
     *                                  draft   get draft
     *                                  deleted  get deleted publications
     *                                  official  get official publications
     * @return view
     */
    public function publicationList(Request $request){
        $err = new Error();
        $take = $request->input('take');
        $skip = $request->input('skip');
        $title = $request->input('title');
        if(!is_null($title)){
            $publicationId = $this->getPublicationsByTitle($title);
        }
        $time = session('personListTime');
        if(is_null($time) || (!is_null($skip) && $skip == 0)){
            $time = date('Y-m-d H:i:s');
            session(['personListTime' => $time]);
        }

        $type = $request->input('type');
        $isForbidden = $request->input('isForbidden');
        switch ($type) {
            case 'view':
                $publications = $this->publicationView($request);
                $publications = $publications->where(IekModel::CREATED,'<',$time);
                if(!is_null($title)){
                    $publications = $publications->whereIn(IekModel::ID,$publicationId);
                }
                $count = $publications->count();
                if($take != null && $skip != null){
                    $publications = $publications->slice($skip,$take);
                }
                $err->setData($publications);
                break;
            case 'draft':
                $publications = $this->publicationDraft($request);
                if(!is_null($title)){
                    $publications = $publications->whereIn(IekModel::ID,$publicationId);
                }
                $publications = $publications->where(IekModel::CREATED,'<',$time);
                $count = $publications->count();
                if($take != null && $skip != null){
                    $publications = $publications->slice($skip,$take);
                }
                $err->setData($publications);
                break;
            case 'deleted':
                $publications = $this->deletedPublication($request);
                if(!is_null($title)){
                    $publications = $publications->whereIn(IekModel::ID,$publicationId);
                }
                $publications = $publications->where(IekModel::CREATED,'<',$time);
                $count = $publications->count();
                if($take != null && $skip != null){
                    $publications = $publications->slice($skip,$take);
                }
                $err->setData($publications);
                break;
            case 'official':
                $publications = $this->officialPublication();
                if(!is_null($title)){
                    $publications = $publications->whereIn(IekModel::ID,$publicationId);
                }
                $publications = $publications->where(IekModel::CREATED,'<',$time);
                $count = $publications->count();
                if($take != null && $skip != null){
                    $publications = $publications->slice($skip,$take);
                }
                $err->setData($publications);
                break;
            case 'person':
                $personId = $request->input('personId');
                $nick = new Person();
                $nick->id = $personId;
                $nick = $nick->getNick();
                if(is_null($personId)){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('personId not null');
                    return view('message.formResult',['result'=>$err]);
                }
                $publications = $this->getPublicationsByPerson($personId);
                if(!is_null($title)){
                    $publications = $publications->whereIn(IekModel::ID,$publicationId);
                }
                $publications = $publications->where(IekModel::CREATED,'<',$time);
                $count = $publications->count();
                if($take != null && $skip != null){
                    $publications = $publications->slice($skip,$take);
                }
                $err->setData($publications);
                $err->author = $nick;
                break;
            default:
                $err ->setError(Errors::INVALID_PARAMS);
                $err ->setMessage('type not null');
        }
        $err -> type = $type;
        $err -> isForbidden = $isForbidden;
        $err -> total = $count;
        $err -> skip = $skip;
        $err -> take = $take;
        $err -> search = $title;

        return view('admin.publication.publicationList',['result' => $err]);
    }

    public function getPublicationsByTitle($title){
        $publicationId = PublicationTitle::whereHas('title' ,
            function($query) use($title){
                $query->where(IekModel::CONTENT,'like','%'.$title.'%');
            })
            ->with('title')
            ->where(IekModel::ACTIVE,true)
            ->pluck(IekModel::PID);
        return $publicationId;
    }

    /**
     * get publications to view
     *
     * @param Request $request -> isForbidden : true  (get forbidden publication list)
     *                                          false \ null (get pass view publication list)
     * @return publications
     */
    public function publicationView(Request $request){
        $isForbidden = $request->input('isForbidden');
        if($isForbidden != 'true'){
            $isForbidden = false;
        }
        if($isForbidden == 'true'){
            $publication = Publication::with(['cover'=>
                function($query){
                    $query->with('cover.norms');
                }])
                ->where(IekModel::ACTIVE,true)
                ->where(IekModel::PUBLISH,true)
                ->with(['publicationTitle'=>function($query){
                    $query->where(IekModel::ACTIVE,true)
                        ->with('title');
                }])
                ->where(IekModel::IS_FORBIDDEN,true)
                ->orderBy(IekModel::UPDATED,'desc')
                ->get();
        }else{
            $firstQuery = Publication::with(['cover'=>
                function($query){
                    $query->with('cover.norms');
                }])
                ->with(['publicationTitle'=>function($query){
                    $query->where(IekModel::ACTIVE,true)
                        ->with('title');
                }])
                ->where(IekModel::PUBLISH,true)
                ->where(IekModel::CONDITION)
                ->where(IekModel::IS_FORBIDDEN,false)
                ->whereHas('publicationOfficial',function($query){
                    $query->where(IekModel::CONDITION);
                })
                ->select(DB::raw('*, \'t\'::boolean as is_official'));

            $secondQuery = Publication::with(['cover'=>
                function($query){
                    $query->with('cover.norms');
                }])
                ->with(['title'=>function($query){
                    $query->with('title');
                }])
                ->where(IekModel::PUBLISH,true)
                ->where(IekModel::CONDITION)
                ->where(IekModel::IS_FORBIDDEN,false)
                ->whereDoesntHave('publicationOfficial',function($query){
                    $query->where(IekModel::CONDITION);
                })
                ->select(DB::raw('*, \'f\'::boolean as is_official'));
            $publication = $firstQuery->union($secondQuery)
                ->orderBy(IekModel::UPDATED, 'desc')
                ->get();
        }
        return $publication;
    }

    /**
     * get publication draft list
     *
     * @param Request $request
     * @return drafts
     */
    public function publicationDraft (Request $request){
        $author_id = $request->input('authorId');
        $publication = Publication::with(['cover'=>
            function($query){
                $query->with('cover.norms');
            }])
            ->with(['publicationTitle'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('title');
            }])
            ->where(IekModel::PUBLISH,false)
            ->where(IekModel::CONDITION);
        if(!is_null($author_id)){
            $publication = $publication->whereHas('publicationPerson',function($query) use($author_id){
                $query->where(IekModel::UID,$author_id);
            });
        }
        $publication = $publication->orderBy(IekModel::UPDATED, 'desc')->get();
        return $publication;
    }

    /**
     * get official publication
     *
     * @param Request $request
     * @return publications
     */
    public function officialPublication(){
        $publications = Publication::with(['cover'=>
            function($query){
                $query->with('cover.norms');
            }])
            ->with(['publicationTitle'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('title');
            }])
            ->whereHas('publicationOfficial',function($query){
                $query->where(IekModel::CONDITION);
            })
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_FORBIDDEN,false)
            ->orderBy(IekModel::UPDATED, 'desc')
            ->get();
        return $publications;
    }

    /**
     * get publication by personId
     *
     * @param $personId
     * @return mixed
     */
    public function getPublicationsByPerson($personId){
        $firstQuery = Publication::with(['cover'=>
            function($query){
                $query->with('cover.norms');
            }])
            ->with(['publicationTitle'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('title');
            }])
            ->whereHas('publicationPerson',function($query) use($personId){
                $query->where(IekModel::UID,$personId);
            })
            ->where(IekModel::ACTIVE,true)
            ->whereHas('publicationOfficial',function($query){
                $query->where(IekModel::CONDITION);
            })
            ->select(DB::raw('*, \'t\'::boolean as is_official'));

        $secondQuery = Publication::with(['cover'=>
            function($query){
                $query->with('cover.norms');
            }])
            ->with(['publicationTitle'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('title');
            }])
            ->whereHas('publicationPerson',function($query) use($personId){
                $query->where(IekModel::UID,$personId);
            })
            ->where(IekModel::ACTIVE,true)
            ->select(DB::raw('*, \'f\'::boolean as is_official'))
            ->whereDoesntHave('publicationOfficial',function($query){
                $query->where(IekModel::CONDITION);
            });

        $publication = $firstQuery->union($secondQuery)
            ->orderBy(IekModel::UPDATED, 'desc')
            ->get();
        return $publication;
    }

    /**
     * official publication
     *
     * @param Request $request
     * @return view with result
     */
    public function official(Request $request, $publicationId){
        $err = new Error();
        if(is_null($publicationId)){
            $err->setError(Errors::INVALID_PUBLICATION_ID);
            return view('admin.publication.official',['result'=>$err]);
        }
        $operator_id = session('login.id');

        $publication = Publication::checkPublication($publicationId);
        if(!$publication){
            $err->setError(Errors::INVALID_PUBLICATION_ID);
            return view('admin.publication.official',['result'=>$err]);
        }
        $reasonId = ForbiddenController::checkReason($request,ReasonType::OFFICIAL);
        if($reasonId->statusCode != 0){
            return view('admin.publication.official',['result'=>$reasonId]);
        }
        $publicationForbidden = Publication::where(IekModel::ID,$publicationId)
            ->where(IekModel::IS_FORBIDDEN,true)
            ->count();
        if($publicationForbidden>0){
            $err -> setError(Errors::NON_COMPLIANCE);
            $err -> setMessage('publication is banned ');
            return view('admin.publication.official',['result'=>$err]);
        }

        DB::beginTransaction();
        try{
            //检查有没有被推荐过
            $publicationOfficialStatus = PublicationOfficial::isOfficial($publicationId);
            if($publicationOfficialStatus){
                $err -> setError(Errors::ACTION_ALREADY_BE_DONE);
                return view('admin.publication.official',['result'=>$err]);
            }
            $publicationOfficial = new PublicationOfficial;
            $publicationOfficial -> publication_id = $publicationId;
            $publicationOfficial->save();
            //保存操作记录
            $log = new ManageLogs();
            $log -> operator_id = $operator_id;
            $log -> reason_id = $reasonId->data;
            $log -> memo = '推荐作品';
            $log ->table_name = $publicationOfficial->getDataTable();
            $log ->row_id = $publicationId;
            $log ->content = json_encode(\App\IekModel\Version1_0\PublicationOfficial::getRecords([IekModel::PID => $publicationId]));
            $log->save();
            //发通知
            $officials = $publicationOfficial
                ->with('publication.publicationPerson')
                ->where(IekModel::PID,$publicationId)
                ->orderBy(IekModel::CREATED,'desc')
                ->first();
            if($officials->is_removed == false){
                $params = new \stdClass();
                $params->action = PersonAction::OFFICIAL;
                $params->lang = 'publication official';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $officials->publication->publicationPerson[0]->person_id;
                $params->targetId = $officials->id;
                $params->reasonId = $log->reason_id;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\PublicationOfficial::class, $params);
                event(new NotifyEvent($args));
            }

            DB::commit();
            //中间逻辑代码 DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('admin.publication.official',['result'=>$err]);
            //接收异常处理并回滚 DB::rollBack();
        }
        return view('admin.publication.official',['result'=>$err]);
    }


    public function unOfficial(Request $request, $publicationId){
        $err = new Error();
        $operator_id = session('login.id');
        if(is_null($publicationId)){
            $err->setError(Errors::INVALID_PUBLICATION_ID);
            return view('admin.publication.unOfficial',['result'=>$err]);
        }

        $reasonId = ForbiddenController::checkReason($request,ReasonType::UNOFFICIAL);
        if($reasonId->statusCode != 0){
            return view('admin.publication.unOfficial',['result'=>$reasonId]);
        }

        $publication = Publication::checkPublication($publicationId);
        if(!$publication){
            $err->setError(Errors::INVALID_PUBLICATION_ID);
            return view('admin.publication.unOfficial',['result'=>$err]);
        }
        DB::beginTransaction();
        try{
            PublicationOfficial::where(IekModel::PID,$publicationId)
                ->where(IekModel::CONDITION)
                ->update([
                    IekModel::REMOVED => true
                ]);

            $log = new ManageLogs();
            $log -> operator_id = $operator_id;
            $log -> reason_id = $reasonId->data;
            $log -> memo = '取消推荐';
            $log ->table_name = 'tblPublicationOfficials';
            $log ->row_id = $publicationId;
            $log ->content = json_encode(\App\IekModel\Version1_0\PublicationOfficial::getRecords([IekModel::PID =>$publicationId]));
            $log->save();

            $officials = PublicationOfficial::with('publication.publicationPerson')
                ->where(IekModel::PID,$publicationId)
                ->orderby(IekModel::UPDATED,'desc')
                ->first();
            if($officials->is_removed ==true){
                $params = new \stdClass();
                $params->action = PersonAction::UNOFFICIAL;
                $params->lang = 'publication unofficial';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $officials->publication->publicationPerson[0]->person_id;
                $params->targetId = $officials->id;
                $params->reasonId = $log->reason_id;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\PublicationOfficial::class, $params);
                event(new NotifyEvent($args));
            }
            DB::commit();
            //中间逻辑代码 DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('admin.publication.unOfficial',['result'=>$err]);
            //接收异常处理并回滚 DB::rollBack();
        }
        return view('admin.publication.unOfficial',['result'=>$err]);
    }

    /**
     * user deleted publication list
     *
     * @param Request $request
     * @return publications
     */
    public function deletedPublication(Request $request){
        $author_id = $request->input('authorId');
        $publications = Publication::with(['cover'=>function($query){
            $query->with('cover.norms');
            }])
            ->with(['publicationTitle'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('title');
            }])
            ->where(IekModel::ACTIVE,false);
        if(!is_null($author_id)){
            $publications = $publications->whereHas('publicationPerson',function($query) use($author_id){
                $query->where(IekModel::UID,$author_id);
            });
        }
        $publications = $publications->orderBy(IekModel::UPDATED, 'desc')->get();
        return $publications;
    }

}
