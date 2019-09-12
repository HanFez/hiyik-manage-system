<?php

namespace App\Http\Middleware;

use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Description;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Images;
use App\IekModel\Version1_0\IwallOfficial;
use App\IekModel\Version1_0\ManageLogs;
use App\IekModel\Version1_0\PlainStyle;
use App\IekModel\Version1_0\Publication;
use App\IekModel\Version1_0\PublicationOfficial;
use App\IekModel\Version1_0\Tag;
use Closure;
use Illuminate\Support\Facades\DB;

class checkForbiddenStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $target_id = $request->input('targetId');
        $reasonID = $request->input('reason');
        $type = $request->input('type');
        //$q = ManageLogs::where('row_id',$target_id)->with('reason')->get();
        switch($type){
            case 'description'://获取标题和描述所属作品的ID
            case 'i-description'://获取标题和描述所属iwall的ID
                $plain = PlainStyle::with('publicationDescription')
                    ->with('publicationTitle')
                    ->with('iwallTitle')
                    ->with('iwallDescription')
                    //->where('id',$target_id)
                    ->find($target_id);
                $isForbidden = $plain->is_forbidden;
                if($plain->publicationTitle != null) {
                    $publicationId = $plain->publicationTitle->publication_id;
                    $memo = '标题被禁';
                }
                if($plain->publicationDescription != null) {
                    $publicationId = $plain->publicationDescription->publication_id;
                    $memo = '描述被禁';
                }
                if($plain->iwallTitle != null){
                    $iwallId = $plain->iwallTitle->iwall_id;
                    $memo = 'Iwall标题被禁';
                }
                if($plain->iwallDescription != null){
                    $iwallId = $plain->iwallDescription->iwall_id;
                    $memo = 'Iwall描述被禁';
                }
                break;
            case 'image'://获取图所属作品的ID
            case 'i-image'://获取快照所属iwall的ID
                $images = Images::with('publicationCover')
                    ->with('publicationImage')
                    ->with('iwallCover')
                    //->where('id',$target_id)
                    ->find($target_id);
                $isForbidden = $images->is_forbidden;
                if($images->publicationCover != null){
                    $publicationId = $images->publicationCover->publication_id;
                    $memo = '封面被禁';
                }
                if($images->publicationImage != null){
                    $publicationId = $images->publicationImage->publication_id;
                    $memo = '作品图被禁';
                }
                if($images->iwallCover != null){
                    $publicationId = $images->iwallCover->iwall_id;
                    $memo = 'iwall封面被禁';
                }
                break;
            case 'tag'://获取标签所属作品的ID
            case 'i-tag'://获取标签所属iwall的ID
                $tags = Tag::with('publicationTag')
                    ->with('iwallTag')
                    //->where('id',$target_id)
                    ->find($target_id);
                $isForbidden = $tags->is_forbidden;
                if($tags->publicationTag != null){
                    $publicationId = $tags->publicationTag->publication_id;
                    $memo = '作品标签被禁';
                }
                if($tags->iwallTag != null){
                    $iwallId = $tags->iwallTag->iwall_id;
                    $memo = 'Iwall标签被禁';
                }
                break;
            default:
                return $response;
                break;
            }
        //查询推荐记录 获取推荐ID和状态is_active
        if(isset($publicationId)){
            $official = PublicationOfficial::where(IekModel::PID,$publicationId)->get();
            foreach($official as $item){
                $officialIsActive = $item->is_active;
            }
        }elseif(isset($iwallId)){
            $official = IwallOfficial::where(IekModel::WID,$iwallId)->get();
            foreach($official as $item){
                $officialIsActive = $item->is_active;
            }
        }
        if(isset($isForbidden) && isset($officialIsActive)){
            if($isForbidden === true && $officialIsActive === true){
                $err = new Error();
                $operator_id = session('login.id');
                DB::beginTransaction();
                try{
                    //置位推荐状态为true
                    if(isset($publicationId)){
                        PublicationOfficial::where(IekModel::PID,$publicationId)
                            ->update([
                                IekModel::REMOVED => true
                            ]);
                    }elseif(isset($iwallId)){
                        IwallOfficial::where(IekModel::WID,$iwallId)
                            ->update([
                                IekModel::REMOVED => true
                            ]);
                    }
                    //操作记录数据
                    $log = new ManageLogs();
                    $log -> operator_id = $operator_id;
                    $log -> reason_id = $reasonID;
                    if(isset($publicationId)){
                        $log ->row_id = $publicationId;
                        $log ->table_name = 'tblPublicationOfficials';
                        $log ->content = json_encode(\App\IekModel\Version1_0\PublicationOfficial::getRecords([IekModel::PID=>$publicationId]));
                    }elseif(isset($iwallId)){
                        $log ->row_id = $iwallId;
                        $log->table_name = 'tblIwallOfficials';
                        $log ->content = json_encode(\App\IekModel\Version1_0\IwallOfficial::getRecords([IekModel::WID=>$iwallId ]));
                    }
                    $log->memo = $memo;
                    $log->save();
                    DB::commit();
                    //中间逻辑代码 DB::commit();
                }catch (\Exception $e) {
                    DB::rollBack();
                    $err->setError(Errors::UNKNOWN_ERROR);
                    $err->setMessage($e->getMessage());
                    return view('admin.publication.unOfficial',['result'=>$err]);
                    //接收异常处理并回滚 DB::rollBack();
                }
            }
        }
    return $response;
    }
}
