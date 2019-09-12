<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/23
 * Time: 15:54
 */
$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$type = isset($result->type) ? $result->type : null;
$param = isset($result->params) ? $result->params : null;
$searchText = isset($result->search) ? $result->search : null;
if(is_null($param)){
    $url = is_null($type) ? null : 'order?type='.$type;
}else{
    $url = is_null($type) ? null : 'order?type='.$type.'&param='.$param;
}
$transDataTable = trans('dataTable');

?>

@extends('layout/widget')

@section('title')
    订单列表
@stop
@section('content')
    <div class="btn-group data-btn-group" id="filter-type">
        <button data-toggle="dropdown" class="btn dropdown-toggle">
            {{ \App\IekModel\Version1_0\IekModel::strTrans($result->type, 'order') }}
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            @foreach($result->orderType as $val)
                <li><a data-type="{{$val}}">{{ \App\IekModel\Version1_0\IekModel::strTrans($val, 'order') }}</a></li>
                <li class="divider"></li>
            @endforeach
        </ul>
        <div class="search" id="list-search">
            <input placeholder="搜索订单号" type="text">
            <button class="tip-bottom btn btn-success" data-original-title="搜索"><i class="icon-search "></i></button>
            @if(!is_null($searchText))
                <span>
                    订单号匹配：
                    <span class="badge badge-info">
                        {{ $searchText }}
                    </span>
                    <input id="search-clear" type="submit" class="btn btn-warning btn-mini" value="清空搜索">
                </span>
            @endif
        </div>
    </div>
    <div>
        @if($result->type == 'paid')
            <form class="form-horizontal">
                <div class="control-group">
                    <label for="checkboxes" class="control-label">筛选</label>
                    <div class="controls">
                        <div data-toggle="buttons-radio" name="type" class="btn-group">
                            <button class="btn" type="button" id="no-material" style="margin-right: 10px;">缺少材料</button>
                            <button class="btn" type="button" id="already-request-refund">已申请退款</button>
                            <button class="btn" type="button" id="urge">催生产</button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
        @if($result->type == 'finish')
            <form class="form-horizontal">
                <div class="control-group">
                    <label for="checkboxes" class="control-label">产品留言</label>
                    <div class="controls">
                        <div data-toggle="buttons-radio" name="type" class="btn-group">
                            <button class="btn" type="button" id="have-comment">有留言</button>
                            <button class="btn" type="button" id="no-comment">无留言</button>
                        </div>
                    </div>
                    <label for="checkboxes" class="control-label">订单评价</label>
                    <div class="controls">
                        <div data-toggle="buttons-radio" name="type" class="btn-group">
                            <button class="btn" type="button" id="have-reason">差评</button>
                            <button class="btn" type="button" id="no-reason">好评</button>
                        </div>
                    </div>
                    <label for="checkboxes" class="control-label">产品回复</label>
                    <div class="controls">
                        <div data-toggle="buttons-radio" name="type" class="btn-group">
                            <button class="btn" type="button" id="no-reply">未回复</button>
                            <button class="btn" type="button" id="already-reply">已回复</button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
    @if($result->type == 'paid')
        <div class="data-list clearfix">
            @if(!$result->data->isEmpty())
                    <div class="widget-box">
                        <div class="widget-content">
                            @foreach($result->data as $order)
                                <div class="new-update clearfix">
                                    <div class="update-done">
                                        <span>订单号：
                                            <a href="javascript:void(0)" data="{{!is_null($order->id)?$order->id:"无"}}">
                                                {{$order->order_no}}
                                            </a>
                                            {{--@if(property_exists($result,'ono'))
                                                @foreach($result->ono as $o)
                                                    {{$o == $order->id ? "（已申请退款）":""}}
                                                @endforeach
                                            @endif--}}
                                            @if(property_exists($result,'params') && $result->params == 'already_refund_request')
                                                <?php $handle = $order->refundRequest[0]->refundRequestHandle;?>
                                                @if(is_null($handle))
                                                    <code>申请退款中</code>
                                                @elseif(!is_null($handle))
                                                    @if($handle->handleResult->status == true)
                                                        <code>退款审核已通过</code>
                                                    @else
                                                        <code>拒绝退款申请,继续审核</code>
                                                    @endif
                                                @endif
                                            @elseif(!$order->refundRequest->isEmpty())
                                                <?php $handle = $order->refundRequest[0]->refundRequestHandle;?>
                                                    @if(is_null($handle))
                                                        <code>申请退款中</code>
                                                    @elseif(!is_null($handle))
                                                        @if($handle->handleResult->status == true)
                                                            <code>退款审核已通过</code>
                                                        @else
                                                            <code>拒绝退款申请,继续审核</code>
                                                        @endif
                                                    @endif
                                            @elseif(property_exists($result,'params') && $result->params == 'urge')
                                                <code>用户催生产</code>
                                            @endif
                                        </span>
                                        @if(!$order->orderProducts->isEmpty())
                                            @foreach($order->orderProducts as $k=>$product)
                                                @if(!is_null($product->products->border))
                                                    @if($product->products->border->materialDefine->is_removed == true)
                                                        <code>画框无材料</code>
                                                    @endif
                                                @endif
                                                @if(!$product->products->frame->isEmpty())
                                                    @foreach($product->products->frame as $frame)
                                                        @if($frame->materialDefine->is_removed == true)
                                                            <code>卡纸无材料</code>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @if(!is_null($product->products->core))
                                                    @if($product->products->core->materialDefine->is_removed == true)
                                                        <code>画芯无材料</code>
                                                    @endif
                                                @endif
                                                @if(!is_null($product->products->front))
                                                    @if($product->products->front->materialDefine->is_removed == true)
                                                        <code>玻璃无材料</code>
                                                    @endif
                                                @endif
                                                @if(!is_null($product->products->back))
                                                    @if($product->products->back->materialDefine->is_removed == true)
                                                        <code>背板无材料</code>
                                                    @endif
                                                @endif
                                                @if(!is_null($product->products->backFacade))
                                                    @if($product->products->backFacade->materialDefine->is_removed == true)
                                                        <code>背饰无材料</code>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                        @if(!is_null($order->urge))
                                            <code>用户催生产</code>
                                        @endif
                                        @if(!$order->orderStatus->isEmpty())
                                            @foreach($order->orderStatus as $status)
                                                @if($status->is_current == true)
                                                    <span>订单状态：{{\App\IekModel\Version1_0\IekModel::strTrans($status->status->name,'order')}}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span>订单状态：{{'无'}}</span>
                                        @endif
                                        <span>创建时间：<em data-time="utc">{{$order->updated_at}}</em></span>
                                    </div>
                                    <div class="fr">
                                        <a name="add-memo" href="javascript:void(0)" class="btn btn-success btn-mini" data-id="{{ $order->id }}" >添加备注</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @include('layout/pagination')
                    </div>
            @else
                <div class="widget-box">
                    <div class="widget-content">
                        {{'没有查询到相关订单！'}}
                    </div>
                    @include('layout/pagination')
                </div>
            @endif
        </div>
    @elseif($result->type == 'finish')
        <div class="data-list clearfix">
            <div class="widget-box">
                <div class="widget-content">
                    @if(!$result->data->isEmpty())
                        @foreach($result->data as $order)
                            <div class="new-update clearfix">
                                <div class="update-done">
                                    <span>订单号：
                                        <a href="javascript:void(0)" data="{{!is_null($order->id)?$order->id:"无"}}">
                                            {{$order->order_no}}
                                        </a>
                                        @if(!$order->orderComment->isEmpty())
                                            @foreach($order->orderComment as $comment)
                                                @if($comment->reply->isEmpty())
                                                    <code>（评价待回复）</code>
                                                    @break
                                                @endif
                                            @endforeach
                                        @endif
                                        @if(!is_null($order->score))
                                            @foreach($order->score as $re)
                                                @if($re->type == 0)
                                                    @if(!is_null($re->reason))
                                                        {{"（有服务评价）"}}
                                                    @endif
                                                @endif
                                                @if($re->type == 1)
                                                    @if(!is_null($re->reason))
                                                        {{"（有物流评价）"}}
                                                    @endif
                                                @endif
                                                @if($re->type == 2)
                                                    @if(!is_null($re->reason))
                                                        {{"（有质量评价）"}}
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    </span>
                                    @if(!$order->orderStatus->isEmpty())
                                        @foreach($order->orderStatus as $status)
                                            @if($status->is_current == true)
                                                <span>订单状态：{{\App\IekModel\Version1_0\IekModel::strTrans($status->status->name,'order')}}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span>订单状态：{{'无'}}</span>
                                    @endif
                                    <span>创建时间：<em data-time="utc">{{$order->updated_at}}</em></span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{'没有查询到相关订单！'}}
                    @endif
                </div>
            </div>
            @include('layout/pagination')
        </div>
    @else
    <div class="data-list clearfix">
        <div class="widget-box">
            <div class="widget-content">
                @if(!$result->data->isEmpty())
                    @foreach($result->data as $order)
                    <div class="new-update clearfix">
                        <div class="update-done">
                            @if($result->type == 'delivered')
                                <span>订单号：
                                    <a href="javascript:void(0)" uuid="{{!is_null($order->id)?$order->id:"无"}}" name="delivered">
                                        {{$order->order_no}}
                                    </a>
                                </span>
                            @else
                                <span>订单号：
                                    <a href="javascript:void(0)" data="{{!is_null($order->id)?$order->id:"无"}}">
                                        {{$order->order_no}}
                                    </a>
                                </span>
                            @endif
                            @if(!$order->orderStatus->isEmpty())
                                @foreach($order->orderStatus as $status)
                                    @if($status->is_current == true)
                                        <span>订单状态：{{\App\IekModel\Version1_0\IekModel::strTrans($status->status->name,'order')}}</span>
                                    @endif
                                @endforeach
                            @else
                                <span>订单状态：{{'无'}}</span>
                            @endif
                            <span>
                                创建时间：
                                <em data-time="utc">
                                    @if(!$order->orderStatus->isEmpty())
                                        @foreach($order->orderStatus as $t)
                                            @if($t->is_current == true)
                                                {{$t->created_at}}
                                            @endif
                                        @endforeach
                                    @endif
                                </em>
                            </span>
                        </div>
                    </div>
                    @endforeach
                @else
                    {{'没有查询到相关订单！'}}
                @endif
            </div>
        </div>
        @include('layout/pagination')
    </div>
    @endif
@stop
<script>
    bindEventToButtonInListView({
        'type': 'order',
        'take': '{{$take}}',
        'url': '{{ $url }}'
    });
    $('a[name="delivered"]').on('click',function(){
        var id = $(this).attr('uuid');
        bootstrapQ.dialog({
            type: 'get',
            url: 'logistics/'+id,
            title: '物流信息',
            className:'modal-lg',
            foot:false
        });
    });
    $('#no-material').on('click',function(){
        var param = 'no_material';
        ajaxData('get','order?type=paid&param='+param+'&take=6&skip=0',appendViewToContainer);
    });
    $('#already-request-refund').on('click',function(){
        var param = 'already_refund_request';
        ajaxData('get','order?type=paid&param='+param+'&take=6&skip=0',appendViewToContainer);
    });
    $('#urge').on('click',function(){
        var param = 'urge';
        ajaxData('get','order?type=paid&param='+param+'&take=6&skip=0',appendViewToContainer);
    });
    $('#have-comment').on('click',function(){
        var param = 'have_comment';
        ajaxData('get','order?type=finish&param='+param+'&take=6&skip=0',appendViewToContainer);
    });
    $('#no-comment').on('click',function(){
        var param = 'no_comment';
        ajaxData('get','order?type=finish&param='+param+'&take=6&skip=0',appendViewToContainer);
    });
    $('#have-reason').on('click',function(){
        var param = 'have_reason';
        ajaxData('get','order?type=finish&param='+param+'&take=6&skip=0',appendViewToContainer);
    });
    $('#no-reason').on('click',function(){
        var param = 'no_reason';
        ajaxData('get','order?type=finish&param='+param+'&take=6&skip=0',appendViewToContainer);
    });
    $('#already-reply').on('click',function(){
        var param = 'already_reply';
        ajaxData('get','order?type=finish&param='+param+'&take=6&skip=0',appendViewToContainer);
    });
    $('#no-reply').on('click',function(){
        var param = 'no_reply';
        ajaxData('get','order?type=finish&param='+param+'&take=6&skip=0',appendViewToContainer);
    });
    $('a[name="add-memo"]').on('click',function(){
        var orderId = $(this).attr('data-id');
        bootstrapQ.confirm({
            'id' : 'confirm',
            'msg' : '<div class="form-horizontal">' +
            '<div class="control-group">' +
            '<label class="control-label">请添加备注：</label>' +
            '<div class="controls">' +
            '<textarea name="" id="platform-memo" cols="30" rows="10"></textarea></div></div></div>'
        },function(){
            var memo = $('#platform-memo').val();
            if(isNull(memo)){
                memo = null;
            }
            var param = {};
            param.data = {};
            param.data.memo = memo;
            ajaxData('post','platformMemo/'+orderId,function(result){
                if(result){
                    $('.widget-content').append(result);
                }
            },[],param);
        });
    });
</script>