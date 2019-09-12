<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/1/6
 * Time: 10:47
 */
use App\IekModel\Version1_0\Constants\Path;
$path    = Path::FILE_PATH;
$transAdmin  = trans('admin');
$transIwall = trans('iwall');
$transDataTable = trans('dataTable');

$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$authorNick = isset($result->author->nick) ? $result->author->nick : null;
$searchText  = isset($result->search) ? $result->search : null;
$iwallType = isset($result->type) ? $result->type : 'normal';
$url = 'iwall?type='.$iwallType;
if($iwallType == 'normal'){
    if(isset($result->isForbidden) && $result->isForbidden == 'true') {
        $url = $url.'&isForbidden=true';
    }
}else if($iwallType == 'person') {
    $url = $url.'&personId='.$result->author->person_id;
}

?>
@extends('layout/widget')

@section('title')
    @if(!is_null($authorNick))
        <a id="i-author" href="javascript:void(0)" data="{{ $result->author->person_id }}">{{ $authorNick->nick }}</a>
    @endif
    {{$transIwall['Iwall']}} {{ $transAdmin['list'] }}
@stop

@section('content')
    @if(is_null($authorNick))
    <div class="data-btn-group">
        <div class="btn-group" id="filter-type">
            <button data-toggle="dropdown" class="btn dropdown-toggle">
                @if(isset($result->type))
                    @if($result->type == 'normal')
                        {{$transIwall['normal']}}
                    @elseif($result->type == 'forbiddenIw')
                        {{$transIwall['forbiddenIw']}}
                    @elseif($result->type == 'draft')
                        {{$transIwall['draft']}}
                    @elseif($result->type == 'deleted')
                        {{$transIwall['deleted']}}
                    @else($result->type == 'recommend')
                        {{$transIwall['recommend']}}
                    @endif
                @endif
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a data-type="normal">{{$transIwall['normal']}}</a></li>
                <li class="divider"></li>
                <li><a data-type="forbiddenIw">{{$transIwall['forbiddenIw']}}</a></li>
                <li class="divider"></li>
                <li><a data-type="draft">{{$transIwall['draft']}}</a></li>
                <li class="divider"></li>
                <li><a data-type="deleted">{{$transIwall['deleted']}}</a></li>
                <li class="divider"></li>
                <li><a data-type="recommend">{{$transIwall['recommend']}}</a></li>
            </ul>
        </div>
        <div class="search" id="list-search">
            <input placeholder="搜索Iwall标题" type="text">
            <button class="tip-bottom btn btn-success" data-original-title="搜索"><i class="icon-search "></i></button>
            @if(!is_null($searchText))
                <span>
                    标题匹配：
                    <span class="badge badge-info">
                        {{ $searchText }}
                    </span>
                    <input id="search-clear" type="submit" class="btn btn-warning btn-mini" value="清空搜索">
                </span>
            @endif
        </div>
    </div>
    @endif
    <div class="data-list clearfix">
        @if($result->statusCode == 10008)
            <script>
                window.location.href = '/login.html';
            </script>
        @endif
        @if($result->data != null && !$result->data->isEmpty())
        <ul class="thumbnails">
            @foreach ($result->data as $iwall)
            <li class="span2">
                <div class="thumbnail">
                    <a href="javascript:void(0)" data="{{$iwall->id}}">
                        @if(!is_null($iwall->iwallCover) && $iwall->iwallCover->is_active === true)
                            <img src="{{$path.$iwall->iwallCover->cover->norms[2]->uri}}" alt="图片">
                        @else
                            <img src="/img/default.png">
                        @endif
                    </a>
                    @if($iwall->is_active)
                        @if($iwall -> is_forbidden)
                            <span class="data-label label label-danger">已禁</span>
                        @elseif(isset($result->type))
                            @if(isset($iwall->is_official) && $iwall->is_official)
                                <span class="data-label label label-success">已荐</span>
                            @elseif($result->type == 'recommend')
                                <span class="data-label label label-success">已荐</span>
                            @else
                                {{--没推荐(>﹏<。)～呜呜呜……--}}
                            @endif
                        @endif
                    @else
                        <span class="data-label label label-danger">已删除</span>
                    @endif
                    <div class="caption">
                        <h5>
                            <a href="javascript:void(0)" data="{{$iwall->id}}">
                                @if(!is_null($iwall->iwallTitle))
                                    @if(!is_null($iwall->iwallTitle->title) &&
                                    $iwall->iwallTitle->title->description->is_active === true)
                                        {{$iwall->iwallTitle->title->description->content}}
                                    @else
                                        {{'无标题'}}
                                    @endif
                                @else
                                    {{'无标题'}}
                                @endif
                            </a>
                        </h5>
                        <p>发布时间: <span data-time="utc">{{$iwall->updated_at}}</span></p>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        @else
            <p>{{ $transDataTable['zeroRecords'] or 'No Data' }}</p>
        @endif
        @include('layout/pagination')
    </div>
    <script>
        bindEventToButtonInListView({
            'type': 'iwall',
            'take': '{{ $take }}',
            'url': '{{ $url }}'
        });
        $('#i-author').on('click', bindEventToShowIwallAuthor);
    </script>
@stop
