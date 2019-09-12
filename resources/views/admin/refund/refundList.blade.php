<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/7
 * Time: 10:06
 */
$orders = $result->data;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$type = isset($result->type) ? $result->type : null;
$url = is_null($type) ? null : 'refundList?type='.$type;
$transDataTable = trans('dataTable');
$viewPay = trans('viewPay');

?>
@extends('layout/widget')

@section('title')
    退款列表
@stop
@section('content')
    <div class="btn-group data-btn-group" id="filter-type">
        <button data-toggle="dropdown" class="btn dropdown-toggle">
            @if($result->type == 'walletPay')
                {{$viewPay['walletPay'] or 'walletPay'}}
            @elseif($result->type == 'aliPay')
                {{$viewPay['aliPay'] or 'aliPay'}}
            @endif
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a data-type="walletPay">{{$viewPay['walletPay'] or 'walletPay'}}</a></li>
            <li class="divider"></li>
            <li><a data-type="aliPay">{{$viewPay['aliPay'] or 'aliPay'}}</a></li>
        </ul>
    </div>
    @if($result->type == 'aliPay')
        <a id="bulk-refund" onclick="batchRefund(this)" class="btn btn-success" type="button" data-url="refund" data-type="refund">确认退款</a>
    @else
        {{--<a id="bulk-refund" onclick="batchWallet(this)" class="btn btn-success" type="button" data-url="refund" data-type="refund">批量退款</a>--}}
    @endif
    <div class="widget-content nopadding">
        <table class="table table-bordered table-striped with-check">
            <thead>
            <tr>
                <th>
                    <div class="checker" id="uniform-title-table-checkbox">
                        <span class=""><input id="title-table-checkbox" name="title-table-checkbox" style="opacity: 0;" type="checkbox"></span>
                    </div>
                </th>
                <th>订单号</th>
                <th>支付金额</th>
                <th>支付流水号</th>
                <th>退款理由</th>
                <th>退款用户</th>
                @if($result->type == 'walletPay')
                    <th>操作</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @if($orders->isEmpty())
                <tr>
                    <td colspan="7" style="text-align: center">没有相关退款申请！！！</td>
                </tr>
            @else
                @foreach($orders as $order)
                    @if($order->orderPay != null && $order->order != null && $order->reason != null)
                        <tr>
                            <td>
                                <div class="checker">
                                    <span class=""><input style="opacity: 0;" type="checkbox" value="{{$order->order->id}}"></span>
                                </div>
                            </td>
                            <td>{{$order->order->order_no}}</td>
                            @if($order->orderPay->pay_method == 0)
                                <td>{{is_null($order->orderPay->walletPay) ? "无": $order->orderPay->walletPay->fee.$order->orderPay->walletPay->currency}}</td>
                                <td>{{is_null($order->orderPay->walletPay) ? "无": $order->orderPay->walletPay->pay_no}}</td>
                            @elseif($order->orderPay->pay_method == 1)
                                <td>{{is_null($order->orderPay->thirdPay) ? "无": $order->orderPay->thirdPay->fee.$order->orderPay->thirdPay->currency}}</td>
                                <td>{{is_null($order->orderPay->thirdPay) ? "无": $order->orderPay->thirdPay->pay_no}}</td>
                            @endif
                            <td>{{$order->reason->reason}}</td>
                            <td>{{is_null($order->order->personOrder) ? "无": $order->order->personOrder->person_id}}</td>
                            @if($result->type == 'walletPay')
                                <td class="op">
                                    <button href="javascript:void (0);" class="btn btn-primary" data="{{$order->id}}"
                                    data-fee="{{$order->orderPay->wealthPay->fee}}">退款</button>
                                </td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <div id="refund-confirm-notice"></div>
    <div class="data-list">
        @include('layout/pagination')
    </div>
@stop
<script>
    bindEventToButtonInListView({
        'type': 'refundList',
        'take': '{{$take}}',
        'url': '{{ $url }}'
    });
    //orderReturnThirdPay
    function batchRefund(self) {
        var type = $(self).attr('data-type');
        var url = $(self).attr('data-url');
        var refundType = '{{$result->type}}';
        if(isNull(url)) {
            return false;
        }
        var checkbox = $('tbody input[type="checkbox"]');
        var ids = [];
        var method = null;
        switch (type) {
            case 'refund':
                method = 'post';
                break;
        }
        checkbox.each(function () {
            var $this = $(this);
            if($this.parent().hasClass('checked')) {
                var id = getVal($this);
                ids.push(id);
            }
        });
        if(ids.length == 0) {
            bootstrapQ.alert(trans_admin.enterData);
        } else if(!isNull(method)) {
            if(refundType == 'aliPay'){
                bootstrapQ.confirm({
                    'id': 'confirm',
                    'msg': '<label class="control-label">备注：</label><div class="controls"><input id="reason" type="text"></div>'
                }, function () {
                    var reason = $('#reason').val();
                    if(isNull(reason)){reason = null;}
                    var params = {};
                    params.data = {};
                    params.data.ids = ids;
                    params.data.reason = reason;
                    params.data.type = refundType;
                    ajaxData(method, url,function(result){
                        if(result){
                            $('#refund-confirm-notice').append(result);
                        }
                    } ,[], params);
                })
            }
        }
    }
    function batchWallet(self){

    }
    //orderReturnWealthPay
    $('.op button').on('click',function(){
        var rid = $(this).attr('data');
        var fee = $(this).attr('data-fee');
        var refundType = '{{$result->type}}';
        bootstrapQ.confirm({
            'id':'confirm',
            'msg': '<label class="control-label">请确认退款金额：</label>' +
            '<div class="controls"><input id="r-fee" type="text" value="'+fee+'"></div>'
        },function(){
            var fee = $('#r-fee').val();
            var params = {};
            params.data = {};
            params.data.rid = rid;
            params.data.fee = fee;
            params.data.type = refundType;
            ajaxData('post', 'refund',function(result){
                if(result){
                    $('#refund-confirm-notice').append(result);
                }
            } ,[], params);
        });
    });
    function handleBulkOperation(result) {
        appendViewToContainer(result);
        messageAlert({
            'message': trans_admin.success,
            'type': 'success'
        });
        $('#myTable .checker span').removeClass('checked');
        $('input[name="all"][type="checkbox"]').prop('checked', false);
        oTable.ajax.reload(); //1.10之后
    }
</script>