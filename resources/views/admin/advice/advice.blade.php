<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/23/16
 * Time: 5:00 PM
 */
$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
//$type = isset($result->type) ? $result->type : null;
$isDeal = isset($result->isDeal) ? $result->isDeal : null;
$url = is_null($isDeal) ? null : 'getAdviceList?isDeal='.$isDeal;

$transDataTable = trans('dataTable');
$reasons = isset($result->replyList) ? $result->replyList : null;
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
        @foreach($result->data as $advice)
        <li>
            <div class="article-post">
                <div class="fr">
                    @if(is_null($advice->adviceHandle))
                        <a name="advice-reply" href="javascript:void(0)" class="btn btn-primary btn-mini" data="{{ $advice->id }}" >回复</a>
                        <a name="advice-ignore" href="javascript:void(0)" class="btn btn-warning btn-mini" data="{{ $advice->id }}" >忽略</a>
                    @elseif($advice->adviceHandle->is_accept == true)
                        <a href="javascript:void(0)" class="btn btn-success btn-mini">已接受</a>
                    @elseif($advice->adviceHandle->is_accept == false)
                        <a href="javascript:void(0)" class="btn btn-danger btn-mini">已忽略</a>
                        <a name="advice-recover" href="javascript:void(0)" class="btn btn-primary btn-mini" data="{{$advice->id}}">误操恢复</a>
                    @endif
                </div>
                <p class="article-title">
                    意见建议:
                    {{ $advice->content }}
                </p>
                <p class="article-content">
                    @if(!is_null($advice))
                        <div>
                            建议人:
                            @if($advice->person != null)
                                <a name="show-person" href="javascript:void(0)" data="{{ $advice->person->id }}">
                                    @if(!$advice->person->personNick->isEmpty())
                                        {{ $advice->person->personNick[0]->nick->nick }}
                                    @endif
                                </a>
                            @else
                                {{"无"}}
                            @endif
                        </div>
                        <div>
                            邮箱：
                            <span>{{$advice->email == "" ? "无":$advice->email}}</span>
                        </div>
                    @endif
                    <div>
                        建议时间: <span data-time="utc">{{ $advice->created_at }}</span>
                    </div>
                </p>
                @if(!is_null($advice->adviceHandle))
                <p class="article-content">
                    <div>回复内容:
                        @if($advice->adviceHandle->adviceReply != null)
                            {{ $advice->adviceHandle->adviceReply->content or '无' }}
                        @endif
                    </div>
                    <div>分值: {{ $advice->adviceHandle->score or '无' }}</div>
                    <div>备注: {{ $advice->adviceHandle->memo or '无' }}</div>
                    <div>
                        处理人:
                        {{ $advice->adviceHandle->operator_id }} -
                        @if($advice->adviceHandle->adviceOperator != null)
                            {{ $advice->adviceHandle->adviceOperator->name }}
                        @endif
                    </div>
                    <div>
                        处理时间: <span data-time="utc">{{ $advice->adviceHandle->updated_at }}</span>
                    </div>
                </p>
                @endif
            </div>
        </li>
        @endforeach
    </ul>
    @include('layout/pagination')
    <div id="form-reply" class="hide">
        <form class="form-horizontal">
            <div class="control-group">
                <label class="control-label">分值(1-10) :</label>
                <div class="controls">
                    <input type="number" name="score" min="1" max="10" required="required">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">回复内容 :</label>
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
                        <input type="radio" name="reason-id" style="opacity: 0;" value="other" data-type="">
                        其他
                    </label>
                    <label class="hide"><textarea name="other"></textarea></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">备注 :</label>
                <div class="controls"><textarea name="memo"></textarea></div>
            </div>
        </form>
    </div>
    @endif
</div>
<script>
    bindEventToFilterButton({
        'type': 'advice',
        'take': '{{ $take }}'
    })
    $('a[name="show-person"]').on('click', bindEventToShowPublicationAuthor);
    $('a[name="advice-reply"]').on('click', function () {
        var $this = $(this);
        var targetId = $this.attr('data').trim();

        var params = {};
        params.data = {};
        params.data.targetId = targetId;

        var url = 'advice/' + targetId + '/deal';
        params.url = url;

        bootstrapQ.confirm({
            'id': 'myReason',
            'msg': $('#form-reply').html()
        }, postFormReply, '', dialogReasonBoxCallback, params);
    });
    function postFormReply(params) {
        if(!isUndefined(params)) {
            var replyId = null;
            var score = getVal($('#myReason form input[name="score"]'));
            if(isNull(score)) {
                messageAlert({
                    'message': '请输入分值(1-10)',
                    'type': 'error'
                })
                return false;
            } else if(parseInt(score) > 10) {
                messageAlert({
                    'message': '请输入分值(1-10)',
                    'type': 'error'
                })
                return false;
            } else {
                params.data.score = parseInt(score);
                if (!isUndefined($('#myReason form input[name="reason-id"]:checked').val())) {
                    replyId = $('#myReason form input[name="reason-id"]:checked').val().trim();
                    params.data.replyType = 'id';
                    params.data.reply = replyId;
                }
                if (replyId == 'other' && !isUndefined($('#myReason textarea[name="other"]'))) {
                    params.data.replyType = 'text';
                    params.data.reply = $('#myReason textarea[name="other"]').val().trim();
                }
                params.data.memo = $('#myReason textarea[name="memo"]').val().trim();
                ajaxData('post', params.url, handlePostReason, [], params);
            }
        }
    }
    $('a[name="advice-ignore"]').on('click',function(){
        var id = $(this).attr('data');
        ajaxData('post','ignore/'+id,function(result){
            if(result){
                $('.recent-posts').append(result);
            }
        });
    });
    $('a[name="advice-recover"]').on('click',function(){
        var id = $(this).attr('data');
        ajaxData('post','recover/'+id,function(result){
            if(result){
                $('.recent-posts').append(result);
            }
        });
    });
</script>
@stop