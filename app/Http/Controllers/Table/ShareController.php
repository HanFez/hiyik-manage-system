<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/10/19
 * Time: 11:20
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\PersonShare;

class ShareController extends Controller
{
    public function shareList(){
        $err = new Error();
        $data = request()->input('params');
        $params = json_decode($data);
        $take = request()->input('take');
        $skip = request()->input('skip');
        if(is_null($params)){
            $share = PersonShare::with('ip')
                ->with('platform')
                ->with(['share'=>function($query){
                    $query->with('person.personNick.nick')
                        ->with('publication.publicationTitle.title')
                        ->with('iwall.iwallTitle.title');
                }])
                ->with('person.personNick.nick')
                ->where(IekModel::CONDITION)
                ->get();
            $num = $this->statisticNum($share);
        }else{
            $share = PersonShare::with('ip')
                ->with('platform')
                ->with(['share'=>function($query){
                    $query->with('person.personNick.nick')
                        ->with('publication.publicationTitle.title')
                        ->with('iwall.iwallTitle.title');
                }])
                ->with('person.personNick.nick')
                ->where(IekModel::CONDITION);

            $type = $params->type;
            if(!count($type) == 0){
                $shareType = $share->whereHas('share',
                    function($query) use($type){
                        $query->whereIn(IekModel::CONTENT_TYPE,$type);
                    });
            }else{
                $shareType = $share;
            }
            $platform = $params->platform;
            if(!count($platform) == 0){
                $sharePlatform = $shareType->whereHas('platform',
                    function($query) use($platform){
                        $query->whereIn(IekModel::PLATFORM,$platform);
                    });
            }else{
                $sharePlatform = $share;
            }

            if(!is_null($shareType) && !is_null($sharePlatform)){
                $share1 = $sharePlatform;
            }
            if(is_null($shareType) && !is_null($sharePlatform)){
                $share1 = $sharePlatform;
            }
            if(is_null($sharePlatform) && !is_null($shareType)){
                $share1 = $shareType;
            }
            if(is_null($shareType) && is_null($sharePlatform)){
                $share1 = $share;
            }

            $time = $params->time;
            $count = count($time);
            $startTime = $params->startTime;
            $endTime = $params->endTime;
            $now = date("Y-m-d H:i:s",strtotime("+8 hour"));
            if($count == 0){
                if($startTime != '' && $endTime != ''){
                    $share2 = $share1->where(IekModel::CREATED,'>=',$startTime)
                        ->where(IekModel::CREATED,'<=',$endTime);
                }elseif($startTime == '' && $endTime != ''){
                    $share2 = $share1->where(IekModel::CREATED,'<=',$endTime);
                }elseif($startTime != '' && $endTime == '') {
                    $share2 = $share1->where(IekModel::CREATED, '>=', $startTime)
                        ->where(IekModel::CREATED, '<=', $now);
                }else{
                    $share2 = $share1;
                }
            }else{
                $share2 = $share1->where(IekModel::CREATED,'>=',$time[0])
                    ->where(IekModel::CREATED,'<=',$now);
            }
            if($type == 0 && $platform == 0 && $time == 0 && $startTime == '' && $endTime == ''){
                $err = new Error();
                $err->setMessage('请至少选择一个条件！');
                $err->setError(Errors::NOT_EMPTY);
                return view('message.formResult',['result'=>$err]);
            }
            if(isset($share2)){
                $share = $share2->get();
                $num = $this->statisticNum($share);
            }
        }
        $total = count($share);
        if(!is_null($take) && !is_null($skip)){
            $share = $share->slice($skip,$take);
        }
        $err->total = $total;
        $err->setData($share);
        $err->take = $take;
        $err->skip = $skip;
        $err->num = $num;
        $err->params = $data;
        //dd($err);
        return view('admin.share.share',['result'=>$err]);
    }

    public function statisticNum($model){
        $person = [];
        $publication = [];
        $iwall = [];
        $qq = [];
        $weChat = [];
        $sinaBlog = [];
        $tencentBlog = [];
        $weixinweb = [];
        foreach($model as $val){
            if($val->share != null){
                if($val->share->content_type == 'person'){
                    $person[] = $val->share->content_type;
                }
                if($val->share->content_type == 'publication'){
                    $publication[] = $val->share->content_type;
                }
                if($val->share->content_type == 'iwall'){
                    $iwall[] = $val->share->content_type;
                }
            }
            if($val->platform != null){
                if($val->platform->platform == 'QQ'){
                    $qq[] = $val->platform->name;
                }
                if($val->platform->platform == 'WeChat'){
                    $weChat[] = $val->platform->name;
                }
                if($val->platform->platform == 'SinaBlog'){
                    $sinaBlog[] = $val->platform->name;
                }
                if($val->platform->platform == 'TencentBlog'){
                    $tencentBlog[] = $val->platform->name;
                }
                if($val->platform->platform == 'weixinweb'){
                    $weixinweb[] = $val->platform->name;
                }
            }
        }
        $personNum = count($person);
        $publicationNum = count($publication);
        $iwallNum = count($iwall);
        $qqNum = count($qq);
        $weChatNum = count($weChat);
        $sinaBlogNum = count($sinaBlog);
        $tencentBlogNum = count($tencentBlog);
        $weixinwebNum = count($weixinweb);
        $num = [
            'personNum' => $personNum,
            'publicationNum' => $publicationNum,
            'iwallNum' => $iwallNum,
            'qqNum' => $qqNum,
            'weChatNum' => $weChatNum,
            'sinaBlogNum' => $sinaBlogNum,
            'tencentBlogNum' => $tencentBlogNum,
            'weixinwebNum' => $weixinwebNum
        ];
        return $num;
    }
}
?>