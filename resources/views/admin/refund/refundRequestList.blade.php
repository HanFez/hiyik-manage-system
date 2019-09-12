<?php

/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/9
 * Time: 16:59
 */
$orders = $result->data;

$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$isAudit = isset($result->auditing) ? $result->auditing : null;
$url = is_null($isAudit) ? null : 'refundRequest?auditing='.$isAudit;
$transDataTable = trans('dataTable');
?>
@extends('layout/widget')

@section('title')
    退款申请列表
@stop
@section('content')
    <div class="data-list clearfix">
        <div class="widget-box">
            <div class="widget-content">
                <div class="btn-group data-btn-group" id="filter-type">
                    <h5 style="margin: 10px;float: left">审核状态</h5>
                    <button data-toggle="dropdown" class="btn dropdown-toggle" style="float: right">
                        @if($result->auditing === '0')
                            未审核
                        @elseif($result->auditing === '1')
                            已审核
                        @elseif($result->auditing === '2')
                            未退款
                        @elseif($result->auditing === '3')
                            已退款
                        @endif
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a data-type="0">未审核</a></li>
                        <li class="divider"></li>
                        <li><a data-type="1">已审核</a></li>
                        <li class="divider"></li>
                        <li><a data-type="2">未退款</a></li>
                        <li class="divider"></li>
                        <li><a data-type="3">已退款</a></li>

                    </ul>
                </div>
            </div>
            @if($result->auditing === '0' || $result->auditing === '1')
                <div class="widget-content">
                    @if($orders->isEmpty())
                        <strong>{{'没有退款申请订单'}}</strong>
                    @else
                        @foreach($orders as $order)
                            <div class="new-update clearfix">
                                @if($result->auditing === '0')
                                    <div class="fr">
                                        <a name="refund-check" href="javascript:void(0)" class="btn btn-primary" data="{{ $order->id }}" >审核</a>
                                    </div>
                                @endif
                                <div class="update-done">
                                    <label>
                                        申请退款订单号：
                                        @if(!is_null($order->order))
                                            <a href="javascript:void(0)" class="refund-info" data="{{ $order->order->id }}">
                                                <strong>{{ $order->order->order_no or 'null'}}</strong>
                                            </a>
                                        @else
                                            <strong>{{'无'}}</strong>
                                        @endif
                                    </label>
                                    <label>申请退款理由：{{ is_null($order->reason) ? "无数据":$order->reason->reason }}</label>
                                    <label>申请退款金额：{{ is_null($order->order) ? "无数据":$order->order->discount_price.$order->order->currency }}</label>
                                    <label for="">支付方式：
                                        @if(!is_null($order->order->orderPay))
                                            @if($order->order->orderPay->pay_method == 0)
                                                钱包支付
                                            @elseif($order->order->orderPay->pay_method == 1)
                                                支付宝支付
                                            @endif
                                        @else
                                            null
                                        @endif
                                    </label>
                                    <label>申请时间：<em data-time="utc">{{ $order->created_at }}</em></label>
                                </div>
                            </div>
                            @if($result->auditing === '1')
                                <div class="widget-content">
                                    <ul class="activity-list">
                                        <li>
                                            <?php $reason = $order->refundRequestHandle;?>
                                            <div>
                                                <label>审核结果：{{ $reason->handleResult->reason->reason }}</label>
                                                <label>审核状态：{{ $reason->handleResult->status == true ? "同意退款":"拒绝退款" }}</label>
                                                <label>审核人员：{{ $reason->operator_id }}</label>
                                                <label>审核时间：<em data-time="utc">{{ $reason->handleResult->created_at }}</em></label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            @elseif($result->auditing === '2')
                <div class="widget-content">
                    @if($orders->isEmpty())
                        <strong>没有未退款订单</strong>
                    @else
                        @foreach($orders as $order)
                            <div class="new-update clearfix">
                                @if(!is_null($order->order))
                                    @if($result->auditing === '2')
                                        <div class="fr">
                                            @if(!is_null($order->order->orderPay))
                                                @if($order->order->orderPay->pay_method == 0)
                                                    <a name="back-money" data-pay="walletPay" class="btn btn-primary">去退款</a>
                                                @else
                                                    <a name="back-money" data-pay="aliPay" class="btn btn-primary">去退款</a>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                    <div class="update-done">
                                        <label>
                                            退款订单号：
                                            <a href="javascript:void(0)" class="refund-info" data="{{$order->order->id}}">
                                                <strong>{{$order->order->order_no or 'null'}}</strong>
                                            </a>
                                        </label>
                                        <label>退款金额：{{$order->order->discount_price.$order->order->currency}}</label>
                                        <label for="">支付方式：
                                            @if(!is_null($order->order->orderPay))
                                                @if($order->order->orderPay->pay_method == 0)
                                                    钱包支付
                                                @elseif($order->order->orderPay->pay_method == 1)
                                                    支付宝支付
                                                @endif
                                            @else
                                                null
                                            @endif
                                        </label>
                                        @if(is_null($order->refundRequestHandle))
                                            <label for="">操作人员：null</label>
                                            <label for="">操作时间：null</label>
                                        @else
                                            <label>操作人员：{{$order->refundRequestHandle->operator_id}}</label>
                                            <label>操作时间：<em data-time="utc">{{$order->refundRequestHandle->created_at}}</em></label>
                                        @endif
                                    </div>
                                @else
                                    <strong>无数据</strong>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            @elseif($result->auditing === '3')
                <div class="widget-content">
                    @if($orders->isEmpty())
                        <strong>没有已退款订单</strong>
                    @else
                        @foreach($orders as $order)
                            <div class="new-update clearfix">
                                @if($result->auditing === '3')
                                    <div class="fr">
                                        <a class="btn btn-success">已退款</a>
                                    </div>
                                @endif
                                <div class="update-done">
                                    <label>
                                        退款订单号：
                                        @if(!is_null($order->order))
                                            <a href="javascript:void(0)" class="refund-info" data="{{$order->order->id}}">
                                                <strong>{{$order->order->order_no or 'null'}}</strong>
                                            </a>
                                        @else
                                            <strong>无数据</strong>
                                        @endif
                                    </label>
                                    @if(!is_null($order->refundRequestHandle))
                                        <?php $resultHandle = $order->refundRequestHandle;?>
                                        @if(!is_null($resultHandle->money))
                                            @if(!is_null($resultHandle->money->returnPay))
                                                <label>
                                                    退款金额：
                                                    @if($resultHandle->money->returnPay->pay_method == 0)
                                                        {{$resultHandle->money->returnPay->wealthPay->fee}}.
                                                        {{$resultHandle->money->returnPay->wealthPay->currency}}
                                                    @elseif($resultHandle->money->returnPay->pay_method == 1)
                                                        {{$resultHandle->money->returnPay->thirdPay->fee}}.
                                                        {{$resultHandle->money->returnPay->thirdPay->currency}}
                                                    @endif
                                                </label>
                                                <label for="">
                                                    支付方式：
                                                    @if(!is_null($order->order->orderPay))
                                                        @if($order->order->orderPay->pay_method == 0)
                                                            钱包支付
                                                        @elseif($order->order->orderPay->pay_method == 1)
                                                            支付宝支付
                                                        @endif
                                                    @else
                                                        null
                                                    @endif
                                                </label>
                                                <label>操作人员：{{$resultHandle->money->operator_id or 'null'}}</label>
                                                <label>退款时间：
                                                    <em data-time="utc">
                                                        @if($resultHandle->money->returnPay->pay_method == 0)
                                                            {{$resultHandle->money->returnPay->wealthPay->created_at}}
                                                        @elseif($resultHandle->money->returnPay->pay_method == 1)
                                                            {{$resultHandle->money->returnPay->thirdPay->created_at}}
                                                        @endif
                                                    </em>
                                                </label>
                                            @else
                                                <label for="">退款金额：null</label>
                                                <label for="">操作人员：null</label>
                                                <label for="">操作时间：null</label>
                                            @endif
                                        @else
                                            <strong>无数据</strong>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif
            @include('layout/pagination')
        </div>
        <div>总共：{{$result->total}} 条</div>
        <div class="message-error"></div>
    </div>
@stop
<script>
    bindEventToButtonInListView({
        'type': 'refundRequest',
        'take': '{{$take}}',
        'url': '{{ $url }}'
    });
    $('a[name="refund-check"]').on('click',function(){
        var id = $(this).attr('data');
        var url = 'checkView';
        bootstrapQ.dialog({
            type:'get',
            url:url,
            title:'审核',
            className:'modal-lg'
        },function(){
            var reason = $('#reason').val();
            if(isNull(reason)){reason=null;}
            var status = $('input[name="status"]:checked').val();
            status = parseInt(status);
            var param = {};
            param.data = {};
            param.data.reason = reason;
            param.data.status = status;
            ajaxData('post', 'refundOrder/check/'+id, function (result) {
                if(!isNull(result)) {
                    $('.message-error').append(result);
                }
            }, [], param);
        });
    });
    $('.refund-info').on('click',function(){
        var id = $(this).attr('data');
        bootstrapQ.dialog({
            type: "get",
            url: 'refundDetail/'+id,
            title: "退款订单详情",
            className:'modal-lg'
        });
    });
    $('a[name="back-money"]').on('click',function(){
        var type = $(this).attr('data-pay');
        ajaxData('get','refundList?type='+type+'&take=10&skip=0',appendViewToContainer);
    })
</script>
