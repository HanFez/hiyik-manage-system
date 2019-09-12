<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/1/16
 * Time: 5:08 PM
 */
$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$isDeleted = isset($result->isDeleted) ? $result->isDeleted : null;
$url = is_null($isDeleted) ? null : 'getFolderList?isDeleted='.$isDeleted;
$urlType = is_null($isDeleted) ? null : 'getFolderList';

$transDataTable = trans('dataTable');
$reasons = isset($result->reasons) ? $result->reasons : null;
//dd($result);
?>
@extends('layout/widget')

@section('title')
    收藏夹列表
@stop
@section('content')
    @if($result->statusCode === 0)
    <div class="btn-group data-btn-group" id="filter-type">
        <button data-toggle="dropdown" class="btn dropdown-toggle">
            {{ $isDeleted === 'true' ? '用户已删除' : '用户未删除' }}
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a data-type="isDeleted=false">用户未删除</a></li>
            <li class="divider"></li>
            <li><a data-type="isDeleted=true">用户已删除</a></li>
        </ul>
    </div>
    <div class="data-list clearfix">
        @if($result->data->isEmpty())
            {{ $transDataTable['zeroRecords'] or 'zero records' }}
        @else
            <ul class="recent-posts">
                @foreach($result->data as $folder)
                    <li>
                        <div class="article-post">
                            {{--<div class="fr">
                                <a name="report-reply" href="javascript:void(0)" class="btn btn-primary btn-mini" data="{{ $folder->id }}" >回复</a>
                            </div>--}}
                            <?php
                                if($folder->folderTitle != null && $folder->folderTitle->title != null){
                                    $title = $folder->folderTitle->title;
                                    $titleIsForbidden = $title->is_forbidden;
                                }
                            ?>
                            <p class="article-title">
                                收藏夹标题:
                                <span name="forbidden-content">
                                    {{ isset($title) && !is_null($title->description)? $title->description->content:"无标题" }}
                                </span>
                                @if(isset($titleIsForbidden))
                                    <button name="forbidden" href="javascript:void(0)" class="btn btn-danger btn-mini" data="{{ $title->id }}"
                                            type="description" data-type="{{ $titleIsForbidden ? 'unForbidden' : 'forbidden'}}">
                                        {{ $titleIsForbidden ? '取消禁止' : '禁止'}}
                                    </button>
                                    @if($titleIsForbidden == true)
                                        <a href="javascript:void(0);" class="seeReason" data="seeTitle/{{$title->id}}">查看原因</a>
                                    @endif
                                @endif
                            </p>
                            <?php
                                if($folder->folderDescription != null && $folder->folderDescription->description != null)
                                {
                                    $des = $folder->folderDescription->description;
                                    $descriptionIsForbidden = $des->is_forbidden;
                                }
                            ?>
                            <p class="article-title" style="font-weight: normal">
                                描述:
                                <span name="forbidden-content">
                                    {{ isset($des) && !is_null($des->description) ? $des->description->content: "无描述" }}
                                </span>
                                @if(isset($descriptionIsForbidden))
                                    <button name="forbidden" href="javascript:void(0)" class="btn btn-danger btn-mini" data="{{ $des->id }}"
                                            type="description" data-type="{{ $descriptionIsForbidden ? 'unForbidden' : 'forbidden'}}">
                                        {{ $descriptionIsForbidden ? '取消禁止' : '禁止'}}
                                    </button>
                                    @if($descriptionIsForbidden == true)
                                        <a href="javascript:void(0);" class="seeReason" data="seeTitle/{{$des->id}}">查看原因</a>
                                    @endif
                                @endif
                            </p>
                            <p class="article-content">
                                <div>
                                    创建人:
                                    @if(!is_null($folder->personFolder) && !is_null($folder->personFolder->person))
                                        <?php $person = $folder->personFolder->person;?>
                                        <a name="show-person" href="javascript:void(0)" data="{{ $person->id }}">
                                            @if(!is_null($person->personNick))
                                                @foreach($person->personNick as $personNick)
                                                    @if($personNick->is_active == true)
                                                        {{ !is_null($personNick->nick) ? $personNick->nick->nick : "无"}}
                                                    @endif
                                                @endforeach
                                            @endif
                                        </a>
                                    @endif
                                </div>
                                <div>
                                    创建时间: <span data-time="utc">{{ $folder->created_at }}</span>
                                </div>
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @include('layout/pagination')
        @include('layout/reason')
        </div>
        <script>
            bindEventToFilterButton({
                'type': 'folder',
                'take': '{{ $take }}',
                'url': '{{ $urlType }}'
            })
            $('a[name="show-person"]').on('click', bindEventToShowPublicationAuthor);
            $('button[name="forbidden"]').on('click', bindEventToShowReason);
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
        @endif
    @else
        @include('errors/error', ['status'=> 500])
    @endif
@stop