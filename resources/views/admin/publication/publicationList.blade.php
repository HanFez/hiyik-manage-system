<?php
/**
 * Created by PhpStorm.
 * User: wj
 * Date: 10/24/16
 * Time: 2:10 PM
 */
use App\IekModel\Version1_0\Constants\Path;
$path    = Path::FILE_PATH;

$transAdmin       = trans('admin');
$transPublication = trans('publication');
$transDataTable = trans('dataTable');

$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$searchText  = isset($result->search) ? $result->search : null;

$authorNick = isset($result->author->nick) ? $result->author->nick : null;

$publicationType = isset($result->type) ? $result->type : 'view';
$url = 'publications?type='.$publicationType;
if($publicationType == 'view') {
    if(isset($result->isForbidden) && $result->isForbidden == 'true') {
        $url = $url.'&isForbidden=true';
    }
}else if($publicationType == 'person') {
    $url = $url.'&personId='.$result->author->person_id;
}

?>
@extends('layout/widget')

@section('title')
    @if(!is_null($authorNick))
        <a id="p-author" href="javascript:void(0)" data="{{ $result->author->person_id }}">{{ $authorNick->nick }}</a>
    @endif
    作品{{ $transAdmin['list'] or 'list' }}
@stop

@section('content')
    @if(is_null($authorNick))
    <div class="data-btn-group">
        <div class="btn-group" id="filter-type">
            <button data-toggle="dropdown" class="btn dropdown-toggle">
                @if(isset($result->type))
                    @if($result->type == 'view' && isset($result->isForbidden) && $result->isForbidden == 'true')
                        {{ $transPublication['forbiddenPub'] or 'forbidden publication' }}
                    @elseif($result->type == 'draft')
                        {{ $transPublication['draft'] or 'draft' }}
                    @elseif($result->type == 'deleted')
                        {{ $transPublication['deleted'] or 'deleted publication' }}
                    @elseif($result->type == 'official')
                        {{ $transPublication['official'] or 'official' }}
                    @else
                        {{ $transPublication['unforbiddenPub'] or 'unforbidden publication' }}
                    @endif
                @endif
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a data-type="unforbidden">{{ $transPublication['unforbiddenPub'] or 'unforbidden publication' }}</a></li>
                <li class="divider"></li>
                <li><a data-type="forbidden">{{ $transPublication['forbiddenPub'] or 'forbidden publication' }}</a></li>
                <li class="divider"></li>
                <li><a data-type="draft">{{ $transPublication['draft'] or 'draft' }}</a></li>
                <li class="divider"></li>
                <li><a data-type="deleted">{{ $transPublication['deleted'] or 'deleted publication' }}</a></li>
                <li class="divider"></li>
                <li><a data-type="official">{{ $transPublication['official'] or 'official' }}</a></li>
            </ul>
        </div>
        <div class="search" id="list-search">
            <input placeholder="搜索作品标题" type="text">
            <button class="tip-bottom btn btn-success" data-original-title="搜索"><i class="icon-search "></i></button>
            @if(!is_null($searchText))
                <span>
                    作品标题匹配：
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
            @foreach ($result->data as $publication)
                    <li class="span2">
                    <div class="thumbnail">
                        <a href="javascript:void(0)" data="{{$publication->id}}">
                            @if(isset($publication->cover->cover->norms[2]->uri))
                                <img src="{{$path.$publication->cover->cover->norms[2]->uri}}"
                                     alt="{{$publication->publicationTitle->title->description->content or ''}}">
                            @else
                                <img src="/img/default.png" alt="">
                            @endif
                        </a>
                        @if($publication->is_active)
                            @if($publication -> is_forbidden)
                                <span class="data-label label label-danger">已禁</span>
                            @elseif(isset($result->type))
                                @if(isset($publication->is_official) && $publication->is_official)
                                    <span class="data-label label label-success">已推荐</span>
                                @elseif($result->type == 'official')
                                    <span class="data-label label label-success">已推荐</span>
                                @else
                                    {{--没推荐(>﹏<。)～呜呜呜……--}}
                                @endif
                            @endif
                        @else
                            <span class="data-label label label-danger">已删除</span>
                        @endif
                        <div class="caption">
                            <h5>
                                <a href="javascript:void(0)" data="{{$publication->id}}">
                                    @if(isset($publication->publicationTitle[0]))
                                        @if(isset($publication->publicationTitle[0]->title->description->content))
                                            {{$publication->publicationTitle[0]->title->description->content}}
                                        @else
                                            {{ '无标题' }}
                                        @endif
                                    @else
                                        {{ '无标题' }}
                                    @endif
                                </a>
                            </h5>
                            <p>发布时间: <span data-time="utc">{{ $publication->updated_at }}</span></p>
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
            'type': 'publication',
            'take': '{{ $take }}',
            'url': '{{ $url }}'
        });
        $('#p-author').on('click', bindEventToShowPublicationAuthor);
    </script>
@stop
