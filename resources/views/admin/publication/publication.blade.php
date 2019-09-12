<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 10/29/16
 * Time: 3:33 PM
 */

use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Constants\Path;
$path    = Path::FILE_PATH;

if($result->statusCode == 0){
    $canSells = [];
    $images = json_decode(json_encode($result->data->images));

    $descriptions = json_decode(json_encode($result->data->description));
    foreach ($images as $key => $image){
        if($image->can_sell == true) {
            array_push($canSells, $image);
            unset($images[$key]);
        }
        if($image->is_active == false) {
            unset($images[$key]);
        }
    }
    foreach ($descriptions as $key => $description){
        if($description->is_active == false) {
            unset($descriptions[$key]);
        }
    }
    $contents = array_merge_recursive($images, $descriptions);
    for ($i = 0; $i < count($contents) ; $i ++) {
        for ($k = count($contents) - 1; $k > $i ; $k --) {
            if ($contents[$k]->index < $contents[$k-1]->index) {
                $temp = $contents[$k];
                $contents[$k] = $contents[$k-1];
                $contents[$k-1] = $temp;
            }
        }
    }
}

$data = isset($result->data) ? $result->data : null;
$titles = isset($data->publicationTitle) ? $data->publicationTitle : null;
$dividers = isset($data->publicationDivider) ? $data->publicationDivider : null;

$background = isset($data->publicationBackground) ? $data->publicationBackground : null;
$officials = isset($data->publicationOfficial) ? $data->publicationOfficial : null;
$officialReasons = isset($data->officialReason) ? $data->officialReason : null;
$forbiddenReasons = isset($data->publicationForbidden) ? $data->publicationForbidden : null;
$tags = isset($data->publicationTags) ? $data->publicationTags : null;

$defaultTags = [];
foreach($tags as $key => $tag) {
    if(!is_null($tag)){
        if($tag -> tag -> is_official === true) {
            array_push($defaultTags, $tag);
            unset($tags[$key]);
        }
        if($tag -> is_active === false) {
            unset($tags[$key]);
        }
    }
}

$backgroundColor = null;
if(!is_null($background)) {
    if(!is_null($background->background)) {
        $backgroundColor = $background->background->styleText->content;
    }
}

    if($data->is_forbidden){
        $isOfficial = false;
    }else{
        if(!$officials->isEmpty()){
            foreach ($officials as $key => $val) {
                if($val->is_active == true && $val->is_removed === false) {
                    $isOfficial = true;
                }else{
                    $isOfficial = false;
                }
            }
        }else{
            $isOfficial = false;
        }
    }

$reasons = $result->data->reasons;
//dd($result->data);

?>
@if($result->statusCode == 0)
    @if($data != null)
        <div class="alert alert-error alert-block alert-right">
            <a class="close" data-dismiss="alert" href="#">×</a>
            <h4 class="alert-heading">作品状态</h4>
            @if($data->is_active)
                @if($data -> is_forbidden)
                    <span class="label label-danger">已被禁止</span>
                    <span class="label label-inverse">未被推荐</span>
                @else
                    <span class="label label-inverse">未被禁止</span>
                    @if($isOfficial)
                        <span class="label label-success">已被推荐</span>
                    @else
                        <span class="label label-inverse">未被推荐</span>
                    @endif
                @endif
            @else
                <span class="label label-inverse">已删除</span>
            @endif
        </div>
        <div class="dialog-title">
            标题:
            @if(!$titles->isEmpty())
                @foreach($titles as $title)
                    <?php
                        if($title->title != null){
                            $titleIsForbidden = $title->title->is_forbidden;
                        }
                    ?>
                    @if($title->is_active == true)
                        <span name="forbidden-content">
                            {{!is_null($title->title) && !is_null($title->title->description) ? $title->title->description->content:"无"}}
                        </span>
                        @if($data->is_active !== false && $data->is_publish !== false)
                            @if(isset($titleIsForbidden))
                                <button class="btn btn-danger" type="description"
                                        data-type="{{$titleIsForbidden? 'unForbidden' : 'forbidden'}}" data="{{$title->title->id}}">
                                    {{ $titleIsForbidden ? '取消禁止' : '禁止'}}
                                </button>
                            @endif
                            @if($titleIsForbidden == true)
                                <a href="javascript:void(0);" class="seeReason group-left" data="seeTitle/{{$title->title->id}}">查看原因</a>
                            @endif
                        @endif
                    @endif
                @endforeach
            @else
                {{'无标题'}}
            @endif
        </div>
        <div class="dialog-content">
            封面图：
            @if(!is_null($data->cover))
            <?php $coverIsForbidden = $data->cover->cover->is_forbidden;?>
                <img  class="group-right" src="{{$path.$data->cover->cover->norms[4]->uri}}" alt="" style="vertical-align: top;">
                @if($data->is_active !== false && $data->is_publish !== false)
                    @if(isset($coverIsForbidden))
                        <button class="btn btn-danger group-left" type="image"
                                data-type="{{$coverIsForbidden ? 'unForbidden': 'forbidden'}}" data="{{$result->data->cover->cover->id}}">
                            {{ $coverIsForbidden ? '取消禁止' : '禁止'}}
                        </button>
                    @endif
                    @if($coverIsForbidden == true)
                        <a href="javascript:void(0);" class="seeReason group-left" data="seeImage/{{$result->data->cover->cover->id}}" >查看原因</a>
                    @endif
                @endif
            @else
                {{'未裁剪封面图'}}
            @endif
        </div>
        <div class="dialog-count">
            <span>喜欢:{{$data->like_count}}</span>
            <span>评论:{{$data->comment_count}}</span>
            <span>查看:{{$data->view_count}}</span>
        </div>
        <div class="dialog-header">
            作者:
            @foreach($result->data->owner as $owner)
                <a id="publication-author" href="javascript:void(0)" data="{{$owner->person->id}}">{{$owner->person->personNick[0]->nick->nick}}</a>
            @endforeach
        </div>
        <div class="dialog-tags">
            分类：
            @if(count($defaultTags) > 0)
                @foreach($defaultTags as $tag)
                    @if(isset($tag -> tag -> name) && $tag -> is_active === true)
                    <a>{{ IekModel::strTrans($tag -> tag -> name, 'Tag') }}</a>
                    @endif
                @endforeach
            @else
                {{'未选择分类'}}
            @endif
        </div>
        <div class="dialog-tags">
            自定义标签：
            @if(!$tags->isEmpty())
            <ul>
                @foreach($tags as $tag)
                    @if(isset($tag -> tag -> name) && $tag -> is_active === true)
                        <?php $tagIsForbidden = $tag -> tag -> is_forbidden;?>
                        <li>
                            <span name="forbidden-content" class="badge {{ $tagIsForbidden ? '' : 'badge-info'}}">{{ $tag -> tag -> name }}</span>
                            @if($result->data->is_active != false && $result->data->is_publish != false)
                            <button class="btn btn-danger btn-mini group-left" type="tag" data-type="{{ $tagIsForbidden ? 'unForbidden' : 'forbidden'}}" data="{{ $tag->tag_id  }}">
                                {{ $tagIsForbidden ? '取消禁止' : '禁止'}}</button>
                                @if($tagIsForbidden == true)
                                    <a href="javascript:void(0);" class="seeReason group-left" data="seeTag/{{$tag->tag->id}}" >查看原因</a>
                                @endif
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
            @else
                {{'未定义标签'}}
            @endif
        </div>
        <div class="dialog-content" style="background: {{ $backgroundColor or '' }}">
            @if(!is_null($canSells) && count($canSells) > 0)
                @foreach($canSells as $content)
                    <?php $imageIsForbidden = $content->image->is_forbidden;?>
                    <div class="group">
                        @if($result->data->is_active !== false && $result->data->is_publish !== false)
                        <button class="btn btn-danger group-left" type="image" data-type="{{$imageIsForbidden ? 'unForbidden' : 'forbidden'}}" data="{{ $content->image_id  }}">
                            {{ $imageIsForbidden ? '取消禁止' : '禁止'}}</button>
                            @if($imageIsForbidden == true)
                                <a href="javascript:void(0);" class="seeReason group-left" data="seeImage/{{$content->image->id}}" >查看原因</a>
                            @endif
                        @endif
                        <img class="group-right" src="{{$path.$content->image->norms[1]->uri}}" alt="">
                    </div>
                @endforeach
            @endif
            @if(!is_null($contents) && count($contents) > 0)
                <p class="dialog-notice">以下为创作过程、细节图及应用场景展示:</p>
                @foreach($contents as $index=>$content)
                    <div class="group">
                    @if(isset($content->imageTitle) && !is_null($content->imageTitle))
                        <?php $imageTitleIsForbidden = $content->imageTitle->is_forbidden; ?>
                        @if($result->data->is_active != false && $result->data->is_publish != false)
                        <button class="btn btn-danger group-left" type="description" data-type="{{ $imageTitleIsForbidden ? 'unForbidden' : 'forbidden'}}"
                                data="{{ $content->imageTitle->id  }}">{{ $imageTitleIsForbidden ? '取消禁止' : '禁止'}}</button>
                            @if($imageTitleIsForbidden == true)
                                <a href="javascript:void(0);" class="seeReason group-left" data="seeTitle/{{$content->imageTitle->id}}" >查看原因</a>
                            @endif
                        @endif
                        <span class="group-right" name="forbidden-content">
                            {!! $content->imageTitle->styleText->content !!}
                        </span>
                    @endif
                    </div>
                    <div class="group">
                    @if(isset($content->image_id))
                        <?php $imageIsForbidden = $content->image->is_forbidden;?>
                        @if($result->data->is_active != false && $result->data->is_publish != false)
                            <button class="btn btn-danger group-left" type="image"
                                    data-type="{{ $imageIsForbidden ? 'unForbidden' : 'forbidden'}}" data="{{ $content->image->id }}">
                                {{ $imageIsForbidden ? '取消禁止' : '禁止'}}
                            </button>
                            @if($imageIsForbidden == true)
                                <a href="javascript:void(0);" class="seeReason group-left" data="seeImage/{{$content->image->id}}" >查看原因</a>
                            @endif
                        @endif
                        <img class="group-right" src="{{$path.$content->image->norms[1]->uri}}" alt="">
                    @elseif(isset($content->content_id))
                        <?php $descriptionIsForbidden = $content->description->is_forbidden; ?>
                        @if($result->data->is_active != false && $result->data->is_publish != false)
                            <button class="btn btn-danger group-left" type="description"
                                    data-type="{{ $descriptionIsForbidden ? 'unForbidden' : 'forbidden'}}" data="{{ $content->content_id  }}">
                                {{ $descriptionIsForbidden? '取消禁止' : '禁止'}}
                            </button>
                            @if($descriptionIsForbidden == true )
                                <a href="javascript:void(0);" class="seeReason group-left" data="seeTitle/{{$content->description->id}}" >查看原因</a>
                            @endif
                        @endif
                        <span class="group-right" name="forbidden-content">{!! $content->description->styleText->content !!}</span>
                    @endif
                    @if(!$dividers->isEmpty() && count($dividers) > 0 && $index != count($contents) - 1)
                        @foreach($dividers as $divider)
                            @if($divider->is_active == true && $divider->is_removed == false)
                                @if(!is_null($divider->divider) && !is_null($divider->divider->styleText))
                                    {!! $divider->divider->styleText->content !!}
                                @endif
                            @endif
                        @endforeach
                    @elseif($index != count($contents) - 1)
                         <div class="module-separator"><div class="divider hidden" style="border-top-color: rgb(167, 167, 167); display: block;"></div></div>
                    @endif
                    </div>
                @endforeach
            @endif
        </div>
        @if(!$officialReasons->isEmpty())
        <div class="dialog-content dialog-border">
            <div class="group" style="margin-top: 20px">
                <div class="group-left">
                    推荐记录:
                </div>
                <div class="group-right">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>操作类型</th>
                            <th>操作原因</th>
                            <th>操作时间</th>
                            <th>操作人账号</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($officialReasons as $v)
                            <?php
                                if(!is_null($v->operator)){
                                    $operator = $v->operator;
                                }else{
                                    echo '无数据';
                                }
                                if(!is_null($v->reason)){
                                    $reason = $v->reason;
                                }else{
                                    echo '无数据';
                                }
                            ?>
                            <tr>
                                @if($reason->type == 'forbidden')
                                    <td>{{'取消推荐'}}</td>
                                    <td>{{$v->memo or '无数据'}}</td>
                                @else
                                    <td>{{ IekModel::strTrans($reason->type, 'publication') }}</td>
                                    <td>{{$reason->reason or '无数据'}}</td>
                                @endif
                                <td data-time="utc">{{ $v->created_at }}</td>
                                <td>{{ $operator->id or '无数据' }}-{{ $operator->name or '无数据' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        @if(!$forbiddenReasons->isEmpty())
            <div class="dialog-content dialog-border">
                <div class="group" style="margin-top: 20px">
                    <div class="group-left">
                        禁止记录:
                    </div>
                    <div class="group-right">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>操作类型</th>
                                <th>操作原因</th>
                                <th>操作时间</th>
                                <th>操作人账号</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($forbiddenReasons as $val)
                                <?php
                                    if(!is_null($val->operator)){
                                        $operator = $val->operator;
                                    }else{
                                        echo '无数据';
                                    }
                                    if(!is_null($val->reason)){
                                        $reason = $val->reason;
                                    }else{
                                        echo '无数据';
                                    }
                                ?>
                                <tr>
                                    <td>{{ IekModel::strTrans($reason->type, 'publication') }}</td>
                                    <td>{{ $reason->reason }}</td>
                                    <td data-time="utc">{{ $val->created_at }}</td>
                                    <td>{{ $operator->id or '空ID' }}-{{ $operator->name or '空名字' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        @if($result->data->is_active != false && $result->data->is_publish != false)
            <div class="dialog-footer">
                <button class="btn btn-danger" type="publication" data-type="{{ $data->is_forbidden ? 'unForbidden' : 'forbidden'}}" data="{{ $data->id }}">
                    {{ $data->is_forbidden ? '取消禁止' : '禁止'}}
                </button>
                <button id="publication-official" class="btn btn-warning" type="official"
                        data-type="{{ $isOfficial === false ? 'official' : 'unOfficial'}}" data="{{$data->id}}">
                    {{ $isOfficial === false ? '推荐' : '取消推荐'}}
                </button>
            </div>
        @endif
        @extends('layout/reason')
    @endif
@elseif($result->statusCode == 21001)
    @include('message.messageAlert',['type'=>'error','message'=>'无效的作品ID'])
@endif
<script>
    $('#publication-author').on('click', bindEventToShowPublicationAuthor);
    $('.seeReason').on('click',function(){
        var url = $(this).attr('data');
        bootstrapQ.dialog({
            type: 'get',
            url: url,
            title: '被禁原因',
            //foot:false
        });
    });
</script>