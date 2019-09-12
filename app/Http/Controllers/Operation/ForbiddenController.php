<?php

namespace App\Http\Controllers\Operation;

use App\Events\NotifyEvent;
use App\Http\Controllers\Table\FilterKeywordController;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Version1_0\Avatar;
use App\IekModel\Version1_0\Comment;
use App\IekModel\Version1_0\Constants\PersonAction;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Images;
use App\IekModel\Version1_0\Iwall;
use App\IekModel\Version1_0\IwallOfficial;
use App\IekModel\Version1_0\ManageLogs;
use App\IekModel\Version1_0\Nick;
use App\IekModel\Version1_0\Notify\FolderDescription;
use App\IekModel\Version1_0\Notify\FolderTitle;
use App\IekModel\Version1_0\Notify\IwallCover;
use App\IekModel\Version1_0\Notify\IwallDescription;
use App\IekModel\Version1_0\Notify\IwallTag;
use App\IekModel\Version1_0\Notify\IwallTitle;
use App\IekModel\Version1_0\Notify\OrderCommentContent;
use App\IekModel\Version1_0\Notify\PersonFamiliar;
use App\IekModel\Version1_0\Notify\PersonFavor;
use App\IekModel\Version1_0\Notify\PublicationCover;
use App\IekModel\Version1_0\Notify\PublicationDescription;
use App\IekModel\Version1_0\Notify\PublicationImage;
use App\IekModel\Version1_0\Notify\PublicationTag;
use App\IekModel\Version1_0\Notify\PublicationTitle;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\OrderCommentImage;
use App\IekModel\Version1_0\OrderCommentText;
use App\IekModel\Version1_0\Person;
use App\IekModel\Version1_0\PersonAvatar;
use App\IekModel\Version1_0\PersonNick;
use App\IekModel\Version1_0\PersonSignature;
use App\IekModel\Version1_0\PlainStyle;
use App\IekModel\Version1_0\Product\Product;
use App\IekModel\Version1_0\Publication;
use App\IekModel\Version1_0\PublicationOfficial;
use App\IekModel\Version1_0\Reason;
use App\IekModel\Version1_0\Signature;
use App\IekModel\Version1_0\Tag;
use Illuminate\Http\Request;
use App\IekModel\Version1_0\Constants\ReasonType;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Constants\Errors;

class ForbiddenController extends Controller
{

    public static function checkExist($model,$target_id){
        $count = $model::where(IekModel::ID,$target_id)
            ->count();
        return $count == 0 ? false : true;
    }

    public static function checkForbidden($model,$target_id){
        $count = $model::where(IekModel::ID,$target_id)
            ->where(IekModel::IS_FORBIDDEN,true)
            ->count();
        return $count == 0 ? false : true;
    }

    public static function forbiddenAction($model,$request){
        $err = new Error();
        $target_id = $request->input('targetId');

        if(is_null($target_id)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('targetId not null');
            return view('message.formResult',['result'=>$err]);
        }

        $exist = self::checkExist($model,$target_id);

        if(!$exist){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid targetId');
            return view('message.formResult',['result'=>$err]);
        }

        $forbidden = self::checkForbidden($model,$target_id);

        if($forbidden){
            $err->setError(Errors::ACTION_ALREADY_BE_DONE);
            return view('message.formResult',['result'=>$err]);
        }

        $operator_id = session('login.id');
        $reason_id = self::checkReason($request,ReasonType::FORBIDDEN);
        if($reason_id->statusCode != 0){
            return $reason_id;
        }

        DB::beginTransaction();
        try{
            $model->where(IekModel::ID,$target_id)
                ->update([
                    IekModel::IS_FORBIDDEN => true
                ]);
            //组装操作记录参数
            $record = new ManageLogs();
            $record ->reason_id = $reason_id->data;
            $record ->operator_id = $operator_id;
            $record ->table_name = $model->getDataTable();
            $record ->row_id = $target_id;
            $record ->content = json_encode($model::getRecords([IekModel::ID =>$target_id]));
//            $record ->memo = $request->input('memo');
            $record->save();
            DB::commit();
            //中间逻辑代码 DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
            //接收异常处理并回滚 DB::rollBack();
        }
        $err->setData($record->reason_id);
        return $err;
    }

    /**
     * check reason useful?
     *
     */
    public static function checkReason(Request $request,$type){
        $reasonType = $request->input('reasonType');
        $reason = $request->input('reason');

        $err = new Error();
        if($reasonType == 'id'){
            $checkReason = Reason::checkReason($reason);
            if(!$checkReason){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('invalid reasonId');
                return $err;
            }
        }else{
            if (is_null($reason)) {
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('reason not null');
                return $err;
            }
            $reason_id = Reason::insertReason($reason,$type,$reason);
            if (!$reason_id) {
                $err->setError(Errors::UNKNOWN_ERROR);
                return $err;
            }
            $err ->data = $reason_id;
            return $err;
        }
        $err ->data = $reason;
        return $err;
    }

    public function forbidden(Request $request){
        $type = $request->input('type');
        $targetId = $request->input('targetId');
        $operator = session('login.id');
        switch ($type) {
            case "person":
                $model = new Person();
                $result = self::forbiddenAction($model, $request);
                break;
            case "avatar":
                $model = new Avatar();
                $result = self::forbiddenAction($model, $request);
                //头像被禁后的通知信息
                $avatar = PersonAvatar::where(IekModel::IID,$targetId)->first();
                $params = new \stdClass();
                $params->action = PersonAction::FORBIDDEN;
                $params->lang = 'person avatar forbidden';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $avatar->person_id;
                $params->targetId = $avatar->id;
                $params->reasonId = $result->data;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\PersonAvatar::class, $params);
                event(new NotifyEvent($args));
                break;
            case "nick":
                $filterSave = FilterKeywordController::addFilterWords($request);
                if($filterSave->statusCode != 0){
                    return view('admin.forbidden',['result'=>$filterSave]);
                }
                $model = new Nick();
                $result = self::forbiddenAction($model, $request);
                //昵称被禁后的通知信息
                $nick = PersonNick::where(IekModel::NICK_ID,$targetId)->first();
                $params = new \stdClass();
                $params->action = PersonAction::FORBIDDEN;
                $params->lang = 'person nick forbidden';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $nick->person_id;
                $params->targetId = $nick->id;
                $params->reasonId = $result->data;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\PersonNick::class, $params);
                event(new NotifyEvent($args));
                break;
            case "signature":
                $filterSave = FilterKeywordController::addFilterWords($request);
                if($filterSave->statusCode != 0){
                    return view('admin.forbidden',['result'=>$filterSave]);
                }
                $model = new Signature();
                $result = self::forbiddenAction($model, $request);
                $signature = PersonSignature::where(IekModel::SIGNATURE_ID,$targetId)->first();
                $params = new \stdClass();
                $params->action = PersonAction::FORBIDDEN;
                $params->lang = 'person signature forbidden';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $signature->person_id;//PersonSignature::getPerson($targetId);
                $params->targetId = $signature->id;
                $params->reasonId = $result->data;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\PersonSignature::class, $params);
                event(new NotifyEvent($args));
                break;
            case "publication":
                $model = new Publication();
                $result = self::forbiddenAction($model,$request);
                $product_id = Product::whereHas('core.coreContent.content.corePublication' ,
                    function($query) use($targetId){
                        $query->where(IekModel::PID,$targetId);
                    })
                    ->value(IekModel::ID);
                $iwall_id = Iwall::whereHas('wall.wallProduct',
                    function($q) use($product_id){
                        $q->where(IekModel::PRODUCT_ID,$product_id);
                    })
                    ->value(IekModel::ID);
                Product::where(IekModel::ID,$product_id)
                    ->update([
                        IekModel::CHANGED => true
                    ]);
                Iwall::where(IekModel::ID,$iwall_id)
                    ->update([
                        IekModel::CHANGED=>true
                    ]);
                $publication = $model->with('publicationPerson')->find($targetId);
                if(!$publication->publicationPerson->isEmpty()){
                    foreach($publication->publicationPerson as $p){
                        $ppid = $p->person_id;
                    }
                }
                //作品被禁，取消推荐状态，保存操作记录
                $official = PublicationOfficial::isOfficial($targetId);
                if($official){
                    PublicationOfficial::where(IekModel::PID,$targetId)
                        ->update([
                            IekModel::REMOVED => true
                        ]);
                    $record = new ManageLogs();
                    $record ->reason_id = $result->data;
                    $record ->operator_id = $operator;
                    $record ->table_name = PublicationOfficial::getDataTable();
                    $record ->row_id = $targetId;
                    $record ->content = json_encode(PublicationOfficial::getRecords([IekModel::PID =>$targetId]));
                    $record ->memo = '作品被禁，取消特别推荐';
                    $record->save();
                }
                //作品被禁后的通知信息
                $params = new \stdClass();
                $params->action = PersonAction::FORBIDDEN;
                $params->lang = 'publication forbidden';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $ppid;
                $params->targetId = $publication->id;
                $params->reasonId = $result->data;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\Publication::class, $params);
                event(new NotifyEvent($args));
                break;
            case 'iwall':
                $model = new Iwall();
                $result = self::forbiddenAction($model,$request);
                $iwall = $model->with('iwallPerson')->find($targetId);
                //IWall被禁，取消推荐状态，保存操作记录
                $official = IwallOfficial::isOfficial($targetId);
                if($official){
                    IwallOfficial::where(IekModel::WID,$targetId)
                        ->update([
                            IekModel::REMOVED => true
                        ]);
                    $record = new ManageLogs();
                    $record ->reason_id = $result->data;
                    $record ->operator_id = $operator;
                    $record ->table_name = IwallOfficial::getDataTable();
                    $record ->row_id = $targetId;
                    $record ->content = json_encode(IwallOfficial::getRecords([IekModel::WID =>$targetId]));
                    $record ->memo = 'IWall被禁，取消特别推荐';
                    $record->save();
                }
                //IWall被禁后的通知信息
                $params = new \stdClass();
                $params->action = PersonAction::FORBIDDEN;
                $params->lang = 'iwall forbidden';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $iwall->iwallPerson->person_id;
                $params->targetId = $targetId;
                $params->reasonId = $result->data;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\Iwall::class, $params);
                event(new NotifyEvent($args));
                break;
            case "description":
            case "i-description":
                $filterSave = FilterKeywordController::addFilterWords($request);
                if($filterSave->statusCode != 0){
                    return view('admin.forbidden',['result'=>$filterSave]);
                }
                $model = new PlainStyle();
                $result = self::forbiddenAction($model,$request);
                //dd($result);
                //描述被禁后的通知信息
                //收藏夹的标题和描述+作品的标题和描述+iwall标题和描述
                $plainStyle = $model->with('publicationDescription.publication.publicationPerson')
                    ->with('publicationTitle.publication.publicationPerson')
                    ->with('folderTitle.personFolder')
                    ->with('folderDescription.personFolder')
                    ->with('iwallTitle.iwallPerson')
                    ->with('iwallDescription.iwallPerson')
                    //->where('id',$targetId)
                    ->find($targetId);
                //dd($plainStyle);
                if($plainStyle->publicationTitle != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'publication title forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->publicationTitle->publication->publicationPerson[0]->person_id;
                    $params->targetId = $plainStyle->publicationTitle->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PublicationTitle::class, $params);
                    event(new NotifyEvent($args));
                }
                if($plainStyle->publicationDescription != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'publication description forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->publicationDescription->publication->publicationPerson[0]->person_id;
                    $params->targetId = $plainStyle->publicationDescription->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PublicationDescription::class, $params);
                    event(new NotifyEvent($args));
                }
                if($plainStyle->folderTitle != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'folder title forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->folderTitle->personFolder->person_id;
                    $params->targetId = $plainStyle->folderTitle->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, FolderTitle::class, $params);
                    event(new NotifyEvent($args));
                }
                if($plainStyle->folderDescription != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'folder description forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->folderDescription->personFolder->person_id;
                    $params->targetId = $plainStyle->folderDescription->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, FolderDescription::class, $params);
                    event(new NotifyEvent($args));
                }
                if($plainStyle->iwallTitle != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'iwall title forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->iwallTitle->iwallPerson->person_id;
                    $params->targetId = $plainStyle->iwallTitle->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, IwallTitle::class, $params);
                    event(new NotifyEvent($args));
                }
                if($plainStyle->iwallDescription != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'iwall description forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->iwallDescription->iwallPerson->person_id;
                    $params->targetId = $plainStyle->iwallDescription->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null,IwallDescription::class, $params);
                    event(new NotifyEvent($args));
                }
                break;
            case "image":
            case "i-image":
                $model = new Images();
                $result = self::forbiddenAction($model,$request);
                $images = Images::with('publicationImage.publication.publicationPerson')
                    ->with('publicationCover.publication.publicationPerson')
                    ->with('iwallCover.iwallPerson')
                    //->where('id',$targetId)
                    ->find($targetId);
                //dd($images);
                if($images->publicationCover != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'publication cover forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $images->publicationCover->publication->publicationPerson[0]->person_id;
                    $params->targetId = $images->publicationCover->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PublicationCover::class, $params);
                    event(new NotifyEvent($args));
                }
                if($images->publicationImage != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'publication image forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $images->publicationImage->publication->publicationPerson[0]->person_id;
                    $params->targetId =$images->publicationImage->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PublicationImage::class, $params);
                    event(new NotifyEvent($args));
                }
                if(!is_null($images->iwallCover)){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'iwall cover forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $images->iwallCover->iwallPerson->person_id;
                    $params->targetId = $images->iwallCover->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, IwallCover::class, $params);
                    event(new NotifyEvent($args));
                }
                break;
            case "comment":
                $model = new Comment();
                $result = self::unForbiddenAction($model,$request);
                //评论取消禁后的通知信息
                $comment = $model->with('publicationComment')
                    ->with('commentComment')
                    ->find($targetId);
                if(!is_null($comment->publicationComment)){//评论的评论
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = PersonAction::UNFORBIDDEN;
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $comment->author;
                    $params->targetId = $result->data;
                    $params->originId = $comment->commentComment->target_comment_id;
                }else{//作品的评论
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = PersonAction::UNFORBIDDEN;
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $comment->author;
                    $params->targetId = $result->data;
                    $params->originId = $comment->publicationComment->publication_id;
                }
                $args = new NotifyEventArguments(null, PublicationCommentNotify::class, $params);
                event(new NotifyEvent($args));
                break;
            case "order-comment-text":
                $filterSave = FilterKeywordController::addFilterWords($request);
                if($filterSave->statusCode != 0){
                    return view('admin.forbidden',['result'=>$filterSave]);
                }
                $model = new OrderCommentText();
                $result = self::forbiddenAction($model,$request);
                //评论被禁后的通知信息
                $comment = $model->with('commentContent.comment')->find($targetId);
                if(!is_null($comment)){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'order comment content forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $comment->commentContent->comment->person_id;
                    $params->targetId = $comment->commentContent->id;
                    $params->reasonId = $result->data;
                }
                $args = new NotifyEventArguments(null, OrderCommentContent::class, $params);
                event(new NotifyEvent($args));
                break;
            case "order-comment-image":
                $model = new OrderCommentImage();
                $result = self::forbiddenAction($model,$request);
                //评论被禁后的通知信息
                $comment = $model->with('commentContent.comment')->find($targetId);
                if(!is_null($comment)){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'order comment content forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $comment->commentContent->comment->person_id;
                    $params->targetId = $comment->commentContent->id;
                    $params->reasonId = $result->data;
                }
                $args = new NotifyEventArguments(null, OrderCommentContent::class, $params);
                event(new NotifyEvent($args));
                break;
            case "tag":
            case "i-tag":
            $filterSave = FilterKeywordController::addFilterWords($request);
                if($filterSave->statusCode != 0){
                    return view('admin.forbidden',['result'=>$filterSave]);
                }
                $model = new Tag();
                $result = self::forbiddenAction($model,$request);
                $tag = Tag::with('publicationTag.publication.publicationPerson')
                    ->with('personFamiliar')
                    ->with('personFavor')
                    ->with('iwallTag.iwall.iwallPerson')
                    //->where('id',$targetId)
                    ->find($targetId);
                //dd($tag);
                if($tag->publicationTag != null){
                    $publication = $tag->publicationTag->publication;
                    if($publication->is_active !== false && $publication->is_publish !== false){
                        $params = new \stdClass();
                        $params->action = PersonAction::FORBIDDEN;
                        $params->lang = 'publication tag forbidden';
                        $params->fromId = OfficialPerson::notifier();
                        $params->toId = $publication->publicationPerson[0]->person_id;
                        $params->targetId = $tag->publicationTag->id;
                        $params->reasonId = $result->data;
                        $args = new NotifyEventArguments(null, PublicationTag::class, $params);
                        event(new NotifyEvent($args));
                    }
                }
                if($tag->personFamiliar != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'person familiar forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $tag->personFamiliar->person_id;
                    $params->targetId = $tag->personFamiliar->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PersonFamiliar::class, $params);
                    event(new NotifyEvent($args));
                }
                if(!is_null($tag->personFavor)){
                    $params = new \stdClass();
                    $params->action = PersonAction::FORBIDDEN;
                    $params->lang = 'person favor forbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $tag->personFavor->person_id;
                    $params->targetId = $tag->personFavor->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PersonFavor::class, $params);
                    event(new NotifyEvent($args));
                }
                if($tag->iwallTag != null){
                    $iwall = $tag->iwallTag->iwall;
                    if($iwall->is_active === true && $iwall->is_publish == true){
                        $params = new \stdClass();
                        $params->action = PersonAction::FORBIDDEN;
                        $params->lang = 'iwall tag forbidden';
                        $params->fromId = OfficialPerson::notifier();
                        $params->toId = $iwall->iwallPerson->person_id;
                        $params->targetId = $tag->iwallTag->id;
                        $params->reasonId = $result->data;
                        $args = new NotifyEventArguments(null, IwallTag::class, $params);
                        event(new NotifyEvent($args));
                    }
                }
                break;
            default :
                $result = new Error();
                $result->setError(Errors::INVALID_PARAMS);
                $result->message = 'invalid type';
        }
        $result->type = $type;
        $result->targetId = $request->input('targetId');
        return view('admin.forbidden',['result'=>$result]);
    }


    public static function unForbiddenAction($model,$request){
        $err = new Error();
        $operator_id = session('login.id');
        $target_id = $request ->input('targetId');
        if(is_null($target_id)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('target_id not null');
            return $err;
        }

        $exist = self::checkExist($model,$target_id);
        if(!$exist){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid targetId');
            return $err;
        }

        $forbidden = self::checkForbidden($model,$target_id);
        if(!$forbidden){
            $err->setError(Errors::ACTION_ALREADY_BE_DONE);
            return $err;
        }

        $reason_id = self::checkReason($request,ReasonType::UNFORBIDDEN);
        if($reason_id->statusCode != 0){
            return $reason_id;
        }

        try{
            $model::where(IekModel::ID,$target_id)
                ->update([
                    IekModel::IS_FORBIDDEN=>false
                ]);

            $record = new ManageLogs();
            $record ->reason_id = $reason_id->data;
            $record ->operator_id = $operator_id;
            $record ->table_name = $model->getDataTable();
            $record ->row_id =$target_id;
            $record ->content = json_encode($model::getRecords([IekModel::ID =>$target_id]));
            //$record ->memo = $request->input('memo');
            $record->save();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        $err->setData($record->reason_id);
        return $err;
    }

    public function unForbidden(Request $request){
        $type = $request->input('type');
        $targetId = $request->input('targetId');
        $operator = session('login.id');
        switch ($type) {
            case "person":
                $model = new Person();
                $result = self::unForbiddenAction($model, $request);
                break;
            case "avatar":
                $model = new Avatar();
                $result = self::unForbiddenAction($model, $request);
                //头像取消禁后的通知信息
                $avatar = PersonAvatar::where(IekModel::IID,$targetId)->first();
                $params = new \stdClass();
                $params->action = PersonAction::UNFORBIDDEN;
                $params->lang = 'person avatar unforbidden';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $avatar->person_id;
                $params->targetId = $avatar->id;
                $params->reasonId = $result->data;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\PersonAvatar::class, $params);
                event(new NotifyEvent($args));
                break;
            case "nick":
                $model = new Nick();
                $result = self::unForbiddenAction($model, $request);
                //昵称取消禁后的通知信息
                $nick = PersonNick::where(IekModel::NICK_ID,$targetId)->first();
                $params = new \stdClass();
                $params->action = PersonAction::UNFORBIDDEN;
                $params->lang = 'person nick unforbidden';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $nick->person_id;
                $params->targetId = $nick->id;
                $params->reasonId = $result->data;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\PersonNick::class, $params);
                event(new NotifyEvent($args));
                break;
            case "signature":
                $model = new Signature();
                $result = self::unForbiddenAction($model, $request);
                $signature = PersonSignature::where(IekModel::SIGNATURE_ID,$targetId)->first();
                $params = new \stdClass();
                $params->action = PersonAction::UNFORBIDDEN;
                $params->lang = 'person signature unforbidden';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $signature->person_id;
                $params->targetId = $signature->id;
                $params->reasonId = $result->data;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\PersonSignature::class, $params);
                event(new NotifyEvent($args));
                break;
            case "publication":
                $model = new Publication();
                $result = self::unForbiddenAction($model,$request);
                $publication = $model->with('publicationPerson')->find($targetId);
                if(!$publication->publicationPerson->isEmpty()){
                    foreach($publication->publicationPerson as $p){
                        $ppid = $p->person_id;
                    }
                }
                //作品取消禁后的通知信息
                $params = new \stdClass();
                $params->action = PersonAction::UNFORBIDDEN;
                $params->lang = 'publication unforbidden';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $ppid;
                $params->targetId = $publication->id;
                $params->reasonId = $result->data;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\Publication::class, $params);
                event(new NotifyEvent($args));
                break;
            case 'iwall':
                $model = new Iwall();
                $result = self::unForbiddenAction($model,$request);
                $iwall = $model->with('iwallPerson')->find($targetId);
                $params = new \stdClass();
                $params->action = PersonAction::UNFORBIDDEN;
                $params->lang = 'iwall unforbidden';
                $params->fromId = OfficialPerson::notifier();
                $params->toId = $iwall->iwallPerson->person_id;
                $params->targetId = $iwall->id;
                $params->reasonId = $result->data;
                $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\Iwall::class, $params);
                event(new NotifyEvent($args));
                break;
            case "description":
            case "i-description":
                $model = new PlainStyle();
                $result = self::unForbiddenAction($model,$request);
                //dd($result);
                $plainStyle = $model->with('publicationDescription.publication.publicationPerson')
                    ->with('publicationTitle.publication.publicationPerson')
                    ->with('folderTitle.personFolder')
                    ->with('folderDescription.personFolder')
                    ->with('iwallTitle.iwallPerson')
                    ->with('iwallDescription.iwallPerson')
                    ->find($targetId);
                //dd($plainStyle);
                if($plainStyle->publicationTitle != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'publication title unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->publicationTitle->publication->publicationPerson[0]->person_id;
                    $params->targetId = $plainStyle->publicationTitle->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PublicationTitle::class, $params);
                    event(new NotifyEvent($args));
                }
                if($plainStyle->publicationDescription != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'publication description unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->publicationDescription->publication->publicationPerson[0]->person_id;
                    $params->targetId = $plainStyle->publicationDescription->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PublicationDescription::class, $params);
                    event(new NotifyEvent($args));
                }
                if($plainStyle->folderTitle != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'folder title unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->folderTitle->personFolder->person_id;
                    $params->targetId = $plainStyle->folderTitle->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, FolderTitle::class, $params);
                    event(new NotifyEvent($args));
                }
                if($plainStyle->folderDescription != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'folder description unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->folderDescription->personFolder->person_id;
                    $params->targetId = $plainStyle->folderDescription->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, FolderDescription::class, $params);
                    event(new NotifyEvent($args));
                }
                if($plainStyle->iwallDescription != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'iwall description unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->iwallDescription->iwallPerson->person_id;
                    $params->targetId = $plainStyle->iwallDescription->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, IwallDescription::class, $params);
                    event(new NotifyEvent($args));
                }
                if($plainStyle->iwallTitle != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'iwall title unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $plainStyle->iwallTitle->iwallPerson->person_id;
                    $params->targetId = $plainStyle->iwallTitle->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, IwallTitle::class, $params);
                    event(new NotifyEvent($args));
                }
                break;
            case "image":
            case "i-image":
                $model = new Images();
                $result = self::unForbiddenAction($model,$request);
                $images = Images::with('publicationImage.publication.publicationPerson')
                    ->with('publicationCover.publication.publicationPerson')
                    ->with('iwallCover.iwallPerson')
                    //->where('id',$targetId)
                    ->find($targetId);
                //dd($images);
                if($images->publicationCover != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'publication cover unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $images->publicationCover->publication->publicationPerson[0]->person_id;
                    $params->targetId = $images->publicationCover->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PublicationCover::class, $params);
                    event(new NotifyEvent($args));
                }
                if($images->publicationImage != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'publication image unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $images->publicationImage->publication->publicationPerson[0]->person_id;
                    $params->targetId = $images->publicationImage->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PublicationImage::class, $params);
                    event(new NotifyEvent($args));
                }
                if($images->iwallCover != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'iwall cover unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $images->iwallCover->iwallPerson->person_id;
                    $params->targetId = $images->iwallCover->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, IwallCover::class, $params);
                    event(new NotifyEvent($args));
                }
                break;
            case "comment":
                $model = new Comment();
                $result = self::unForbiddenAction($model,$request);
                //评论取消禁后的通知信息
                $comment = $model->with('publicationComment')
                    ->with('commentComment')
                    ->find($targetId);
                if(is_null($comment->publicationComment)){//评论的评论
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = PersonAction::UNFORBIDDEN;
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $comment->author;
                    $params->targetId = $result->data;
                    $params->originId = $comment->commentComment->target_comment_id;
                }else{//作品的评论
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = PersonAction::UNFORBIDDEN;
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $comment->author;
                    $params->targetId = $result->data;
                    $params->originId = $comment->publicationComment->publication_id;
                }
                $args = new NotifyEventArguments(null, PublicationCommentNotify::class, $params);
                event(new NotifyEvent($args));
                break;
            case "order-comment-text":
                $model = new OrderCommentText();
                $result = self::unForbiddenAction($model,$request);
                //评论被禁后的通知信息
                $comment = $model->with('commentContent.comment')->find($targetId);
                if(!is_null($comment)){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'order comment content unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $comment->commentContent->comment->person_id;
                    $params->targetId = $comment->commentContent->id;
                    $params->reasonId = $result->data;
                }
                $args = new NotifyEventArguments(null, OrderCommentContent::class, $params);
                event(new NotifyEvent($args));
                break;
            case "order-comment-image":
                $model = new OrderCommentImage();
                $result = self::unForbiddenAction($model,$request);
                //评论被禁后的通知信息
                $comment = $model->with('commentContent.comment')->find($targetId);
                if(!is_null($comment)){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'order comment content unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $comment->commentContent->comment->person_id;
                    $params->targetId = $comment->commentContent->id;
                    $params->reasonId = $result->data;
                }
                $args = new NotifyEventArguments(null, OrderCommentContent::class, $params);
                event(new NotifyEvent($args));
                break;
            case "tag":
            case "i-tag":
                $model = new Tag();
                $result = self::unForbiddenAction($model,$request);
                $tag = Tag::with('publicationTag.publication.publicationPerson')
                    ->with('personFamiliar')
                    ->with('personFavor')
                    ->with('iwallTag.iwall.iwallPerson')
                    //->where('id',$targetId)
                    ->find($targetId);
                //dd($tag);
                if($tag->publicationTag != null){
                    $publication = $tag->publicationTag->publication;
                    if($publication->is_active !== false && $publication->is_publish !== false){
                        $params = new \stdClass();
                        $params->action = PersonAction::UNFORBIDDEN;
                        $params->lang = 'publication tag unforbidden';
                        $params->fromId = OfficialPerson::notifier();
                        $params->toId = $publication->publicationPerson[0]->person_id;
                        $params->targetId = $tag->publicationTag->id;
                        $params->reasonId = $result->data;
                        $args = new NotifyEventArguments(null, PublicationTag::class, $params);
                        event(new NotifyEvent($args));
                    }
                }
                if($tag->personFamiliar != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'person familiar unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $tag->personFamiliar->person_id;
                    $params->targetId = $tag->personFamiliar->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PersonFamiliar::class, $params);
                    event(new NotifyEvent($args));
                }
                if($tag->personFavor != null){
                    $params = new \stdClass();
                    $params->action = PersonAction::UNFORBIDDEN;
                    $params->lang = 'person favor unforbidden';
                    $params->fromId = OfficialPerson::notifier();
                    $params->toId = $tag->personFavor->person_id;
                    $params->targetId = $tag->personFavor->id;
                    $params->reasonId = $result->data;
                    $args = new NotifyEventArguments(null, PersonFavor::class, $params);
                    event(new NotifyEvent($args));
                }
                if($tag->iwallTag != null){
                    $iwall = $tag->iwallTag->iwall;
                    if($iwall->is_active === true && $iwall->is_publish == true){
                        $params = new \stdClass();
                        $params->action = PersonAction::UNFORBIDDEN;
                        $params->lang = 'iwall tag unforbidden';
                        $params->fromId = OfficialPerson::notifier();
                        $params->toId = $iwall->iwallPerson->person_id;
                        $params->targetId = $tag->iwallTag->id;
                        $params->reasonId = $result->data;
                        $args = new NotifyEventArguments(null, IwallTag::class, $params);
                        event(new NotifyEvent($args));
                    }
                }
                break;
            default :
                $result = new Error();
                $result->setError(Errors::INVALID_PARAMS);
                $result->message = 'invalid type';
        }
        $result->type = $type;
        $result->targetId = $request->input('targetId');
        return view('admin.unForbidden',['result'=>$result]);
    }
}