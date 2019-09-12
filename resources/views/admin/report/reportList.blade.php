<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/22/16
 * Time: 5:35 PM
 */
$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$type = isset($result->type) ? $result->type : null;
$isDeal = isset($result->isDeal) ? $result->isDeal : null;
$url = is_null($type) ? null : 'reports?type='.$type.'&isDeal='.$isDeal;
$urlType = is_null($type) ? null : 'reports?type='.$type;

$transDataTable = trans('dataTable');
$reasons = isset($result->reasonList) ? $result->reasonList : null;
?>
@extends('layout/widget')

@section('title')
    举报列表
@stop
@section('content')
    <div class="btn-group data-btn-group" id="filter-type">
        <button data-toggle="dropdown" class="btn dropdown-toggle">
            {{ $result->isDeal=='true' ? '已处理' : '未处理' }}
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a data-type="isDeal=true">已处理</a></li>
            <li class="divider"></li>
            <li><a data-type="isDeal=false">未处理</a></li>
        </ul>
    </div>
    <div class="data-list clearfix">
        @if($result->data->isEmpty())
            {{ $transDataTable['zeroRecords'] or 'zero records' }}
        @else
        <ul class="recent-posts">
            @foreach($result->data as $report)
            <li>
                <div class="article-post">
                    <div class="fr">
                        @if(is_null($report->reportHandle))
                            <a name="report-reply" href="javascript:void(0)" class="btn btn-primary btn-mini" data="{{ $report->id }}" >回复</a>
                            <a name="ignore" href="javascript:void(0)" class="btn btn-warning btn-mini" data="{{ $report->id }}">忽略</a>
                        @endif
                    </div>
                    <p class="article-title">
                        举报内容:
                        @if($type === 'message')
                            <span name="forbidden-content">{{ $report->reportChatSession->message->content }}</span><br/>
                            <?php $isForbidden = $report->reportChatSession->message->is_removed?>
                        @elseif($type === 'comment')
                            <span name="forbidden-content">{{ $report->reportComment->content }}</span>
                            <?php $isForbidden = $report->reportComment->is_removed?>
                        @elseif($type === 'person')
                            <a name="show-person" href="javascript:void(0)" data="{{ $report->target_id }}">
                                {{ $report->reportPerson->personNick[0]->nick->nick }}
                            </a>
                        @elseif($type === 'publication')
                            <a name="show-publication" href="javascript:void(0)" data="{{ $report->reportPublicationTitle->publication_id }}">
                                {{ $report->reportPublicationTitle->title->content }}
                            </a>
                        @endif
                        @if($type === 'comment')
                            <button name="forbidden" href="javascript:void(0)" class="btn btn-danger btn-mini"
                                    data="{{ $report->target_id }}" type="{{ $type }}" data-type="{{ $isForbidden ? 'unForbidden' : 'forbidden'}}">
                                {{ $isForbidden ? '取消禁止' : '禁止'}}
                            </button>
                            @if($type === 'comment' && $isForbidden)
                                <a href="javascript:void(0);" class="seeReason" data="seeComment/{{$report->target_id}}">查看原因</a>
                            @endif
                        @endif
                        @if($type === 'message')
                           {{-- <button name="forbidden" href="javascript:void(0)" class="btn btn-danger btn-mini" data="{{ $report->reportChatSession->message_id}}" type="{{ $type }}" data-type="{{ $isForbidden ? 'unForbidden' : 'forbidden'}}">
                                {{ $isForbidden ? '取消禁止' : '禁止'}}</button>
                            @if($type === 'message' && $isForbidden)
                                <a href="javascript:void(0);" class="seeReason" data="seeMessage/{{$report->reportChatSession->message->id}}">查看原因</a>
                            @endif--}}
                            <?php $person = $report->reportChatSession->to->personNick;?>
                            @foreach($person as $p)
                                @endforeach
                            被举报人：<a name="show-person" href="javascript:void(0)" data="{{ $p->person_id }}">{{$p->nick->nick}}</a>
                        @endif
                    </p>
                    <p class="article-content">
                        <div>举报原因: {{ $report->reason }}</div>
                        <div>
                            举报人:
                            <a name="show-person" href="javascript:void(0)" data="{{ $report->reportInformer->id }}">
                                {{ $report->reportInformer->personNick[0]->nick->nick }}
                            </a>
                        </div>
                        <div>
                            举报时间: <span data-time="utc">{{ $report->created_at }}</span>
                        </div>
                    </p>
                    @if(!is_null($report->reportHandle))
                        <p class="article-content">
                            <div>回复内容: {{ $report->reportHandle->reportReply->content or '无' }}</div>
                            <div>备注: {{ $report->reportHandle->memo or '无' }}</div>
                            <div>
                                处理人:
                                {{ $report->reportHandle->operator_id }}-{{ $report->reportHandle->reportOperator->name }}
                            </div>
                            <div>
                                处理时间: <span data-time="utc">{{ $report->reportHandle->updated_at }}</span>
                            </div>
                        </p>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
        @endif
        @include('layout/pagination')
        @include('layout/reason')
        <div id="form-reply" class="hide">
            <form class="form-horizontal">
                <div class="control-group">
                    <label class="control-label">
                        回复内容 :
                    </label>
                    <div class="controls">
                        @foreach($result->replyList as $reply)
                            @if(!is_null($reply->content) && $reply->content != '')
                                <label >
                                    <input type="radio" name="reason-id" style="opacity: 0;" value="{{$reply->id}}" >
                                    {{$reply->content}}
                                </label>
                            @endif
                        @endforeach
                        <label>
                            <input type="radio" name="reason-id" style="opacity: 0;" value="other" data-type="" >
                            其他
                        </label>
                        <label class="hide">
                            <textarea name="other"></textarea>
                        </label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">
                        备注 :
                    </label>
                    <div class="controls">
                        <textarea name="memo"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        bindEventToFilterButton({
            'type': 'report',
            'take': '{{ $take }}',
            'url': '{{ $urlType }}'
        })
        $('a[name="show-person"]').on('click', bindEventToShowPublicationAuthor);
        $('a[name="show-publication"]').on('click', bindEventToShowAuthorPublication);
        $('button[name="forbidden"]').on('click', bindEventToShowReason);
        $('a[name="report-reply"]').on('click', function () {
            var $this = $(this);
            var targetId = $this.attr('data').trim();

            var params = {};
            params.data = {};
            params.data.targetId = targetId;
            params.data.status = 1;
            params.dataType = 'reply';

            var url = 'reports/' + targetId + '/deal';
            params.url = url;

            bootstrapQ.confirm({
                'id': 'myReason',
                'msg': $('#form-reply').html()
            }, postReason, '', dialogReasonBoxCallback, params);
        });
        $('a[name="ignore"]').on('click', function () {
            var $this = $(this);
            var targetId = $this.attr('data').trim();
            var url = 'reports/' + targetId + '/deal';
            var params = {};
            params.data = {};
            params.data.targetId = targetId;
            params.data.status = 0;
            bootstrapQ.confirm({
                'msg': '确定忽略此举报信息？'
            }, function () {
                ajaxData('post', url, function (result) {
                    $('#container').append(result);
                }, [], params);
            })
        })
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
@stop