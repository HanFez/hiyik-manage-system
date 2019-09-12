<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 10/31/16
 * Time: 5:17 PM
 */
use App\IekModel\Version1_0\Constants\Path;
$path    = Path::FILE_PATH;

$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$searchText  = isset($result->search) ? $result->search : null;
$isForbidden = isset($result->isForbidden) ? $result->isForbidden : false;
$url = $isForbidden == 'true' ? '/persons?isForbidden=true' : '/persons?isForbidden=false';
//dd(json_decode(json_encode($result)));
$transDataTable = trans('dataTable');
?>
@extends('layout/widget')

@section('title')
    用户列表
@stop

@section('content')
    <div class="data-btn-group">
        <div class="btn-group" id="filter-type">
            <button data-toggle="dropdown" class="btn dropdown-toggle">
                @if($isForbidden == 'true')
                    已禁用户
                @else
                    未禁用户
                @endif
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a data-type="unforbidden">未禁用户</a></li>
                <li class="divider"></li>
                <li><a data-type="forbidden">已禁用户</a></li>
            </ul>
        </div>
        <div class="search" id="list-search">
            <input placeholder="搜索用户昵称" type="text">
            <button class="tip-bottom btn btn-success" data-original-title="搜索"><i class="icon-search "></i></button>
            @if(!is_null($searchText))
                <span>
                    昵称匹配：
                    <span class="badge badge-info">
                        {{ $searchText }}
                    </span>
                    <input id="search-clear" type="submit" class="btn btn-warning btn-mini" value="清空搜索">
                </span>
            @endif
        </div>
    </div>
    <div class="data-list clearfix">
    @if($result->statusCode == 0)
        @if(!$result->data->isEmpty())
            <ul class="thumbnails">
            @foreach($result->data as $person)
                <li class="span2">
                    <div class="thumbnail">
                        <a href="javascript:void(0)" data="{{$person->id}}">
                            @if($person->personAvatar != null && count($person->personAvatar) !== 0)
                                <img src="{{$path.$person->personAvatar[0]->avatar->norms[4]->uri}}" alt="">
                            @else
                                <img src="/img/default.png" alt="">
                            @endif
                        </a>
                        @if($person -> is_forbidden)
                            <span class="data-label label label-danger">已封号</span>
                        @elseif($person -> isGag == true)
                            <span class="data-label label label-warning">已禁言</span>
                        @endif
                        <div class="caption">
                            <h5>
                                <a href="javascript:void(0)" data="{{$person->id}}">
                                    @if(!$person->personNick->isEmpty())
                                        {{ !is_null($person->personNick[0]->nick) ? $person->personNick[0]->nick->nick : "无昵称" }}
                                    @else
                                        {{ "无昵称" }}
                                    @endif
                                </a>
                                <p style="margin-top: 10px">
                                    <button name="author-publications" class="btn btn-info btn-mini" data="{{$person->id}}">查看所有作品</button>
                                </p>
                                <p style="margin-top: 10px">
                                    <button name="author-iwalls" class="btn btn-info btn-mini" data="{{$person->id}}">查看所有Iwall</button>
                                </p>
                            </h5>
                        </div>
                    </div>
                </li>
            @endforeach
            </ul>
        @else
            <p>{{ $transDataTable['zeroRecords'] or 'No Data' }}</p>
        @endif
    @endif
    @include('layout/pagination')
    </div>
    <script>
        bindEventToButtonInListView({
            'type': 'person',
            'take': '{{ $take }}',
            'url': '{{ $url }}'
        });
        $('#container button[name="author-publications"]').unbind('click').on('click', bindEventToShowAuthorPublications);
        $('#container button[name="author-iwalls"]').unbind('click').on('click', bindEventToShowAuthorIwalls);
    </script>
@stop