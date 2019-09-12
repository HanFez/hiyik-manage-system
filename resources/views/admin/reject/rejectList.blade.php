<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/10
 * Time: 14:57
 */
use App\IekModel\Version1_0\Constants\Path;
$path    = Path::FILE_PATH;
$orders = $result->data;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$isAudit = isset($result->auditing) ? $result->auditing : null;
$url = is_null($isAudit) ? null : 'reject?auditing='.$isAudit;
$transDataTable = trans('dataTable');
?>
@extends('layout.widget')

@section('title')
    退换订单列表
@stop
@section('content')
    <div class="data-list clearfix">
        <div class="widget-box">
            <div class="btn-group data-btn-group" id="filter-type">
                <button data-toggle="dropdown" class="btn dropdown-toggle">
                    {{ $result->auditing == 'true' ? '已审核' : '未审核' }}
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a data-type="false">未审核</a></li>
                    <li class="divider"></li>
                    <li><a data-type="true">已审核</a></li>
                </ul>
            </div>
            <div class="widget-content">
                @if(!$orders->isEmpty())
                @foreach($orders as $order)
                    @if(!is_null($order))
                        @if(!is_null($order->order))
                        <div class="new-update clearfix">
                            <div class="fr">
                                @if(is_null($order->result))
                                    <a name="reject-check" href="javascript:void(0)" class="btn btn-primary" data="{{ $order->id }}" >{{ "审核" }}</a>
                                @elseif($order->result === 0 && $order->confirm_reject === true)
                                    @if($order->rejectResultHandle != null && $order->rejectResultHandle->reject != null)
                                        <?php $reject = $order->rejectResultHandle->reject;?>
                                        @if($reject->send_ship_id != null)
                                            <a class="btn btn-success">{{ "平台已发货" }}</a>
                                        @else
                                            <a name="return-purchase" href="javascript:void(0)" class="btn btn-primary" data="{{ $order->id }}" >{{ "退换" }}</a>
                                        @endif

                                        @if($reject->back_ship_id != null && $reject->platform_confirm == true
                                        && $reject->ship_fee_result == true && $reject->custom_confirm == true
                                        && $reject->rejectShipFeePay != null)
                                            <a class="btn btn-success">{{ "退换成功" }}</a>
                                        @elseif($reject->back_ship_id != null && $reject->custom_confirm == false)
                                            <a class="btn btn-warning">{{ "用户已发货" }}</a>
                                        @elseif($reject->back_ship_id != null && $reject->platform_confirm == true
                                        && $reject->ship_fee_result == true && $reject->rejectShipFeePay === null)
                                            <a class="btn btn-primary" href="javascript:void(0)" id="ship-fee-return" data="{{$order->id}}">{{ "点击退邮费" }}</a>
                                        @elseif($reject->back_ship_id != null && $reject->platform_confirm == false)
                                            <a id="confirm-goods" class="btn btn-primary" href="javascript:void(0)"
                                               data="{{ $reject->id }}" >{{ "用户已发货，点击确认收货" }}</a>
                                        @elseif($reject->back_ship_id === null)
                                            <a class="btn btn-info">{{ "用户未发货" }}</a>
                                        @endif

                                    @else
                                        <a name="return-purchase" href="javascript:void(0)" class="btn btn-primary" data="{{ $order->id }}" >{{ "退换" }}</a>
                                    @endif
                                @elseif($order->result === 0 && $order->confirm_reject === false)
                                    <a class="btn btn-warning">{{ "等待用户确认" }}</a>
                                @else
                                    <a class="btn btn-danger">{{ "已拒绝退换" }}</a>
                                @endif
                            </div>
                            <div class="control-group">
                                <label class="control-label">
                                    退换订单号：
                                    <a name="detail" href="javascript:void(0)" data="{{ $order->id }}">
                                        <strong>{{ $order->order->order_no }}</strong>
                                    </a>
                                </label>
                                <label class="control-label">订单价格：
                                    原价：{{$order->order->price}}{{$order->order->currency}}<br>
                                    折扣价：{{$order->order->discount_price}}{{$order->order->currency}}
                                </label>
                                <label class="control-label">用户是否确认退换：{{$order->confirm_reject == false ? "未确认" : "已确认"}}</label>
                                <label class="control-label">退换申请状态：
                                    @if(is_null($order->result))
                                        {{ "未审核" }}
                                    @elseif($order->result == 0)
                                        {{ "同意退换" }}
                                    @elseif($order->result == 1)
                                        {{ "拒绝退换" }}
                                    @endif
                                </label>
                                @if($result->auditing == 'true')
                                    <label class="control-label">退换审核时间：{{$order->updated_at}}</label>
                                @else
                                    <label class="control-label">申请退换时间：{{$order->created_at}}</label>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endif
                @endforeach
                    @else
                    <label>还没有退换申请！！！</label>
                @endif
            </div>
            @include('layout/pagination')
        </div>
    </div>
@stop
<script>
    bindEventToButtonInListView({
        'type': 'reject',
        'take': '{{$take}}',
        'url': '{{ $url }}'
    });
    $('a[name="detail"]').on('click',function(){
        var id = $(this).attr('data');
        var audit = "{{$result->auditing}}";
        var url = 'reject/detail/'+id+'?audit='+audit;
        bootstrapQ.dialog({
            type:'get',
            url:url,
            title:'退换订单详情',
            className:'modal-lg',
            foot:false
        });
    });
    $('a[name="reject-check"]').on('click',function(){
        var id = $(this).attr('data');
        var url = 'reject/audit/'+id;
        bootstrapQ.dialog({
            type:'get',
            url:url,
            title:'审核',
            className:'modal-lg',
            foot:false
        });
    });
    $('a[name="return-purchase"]').on('click',function(){
        var id = $(this).attr('data');
        var url = 'exchange/'+id;
        bootstrapQ.dialog({
            type:'get',
            url:url,
            title:'退换',
            className:'modal-lg',
            foot:false
        });
    });
    $('#confirm-goods').on('click',function(){
        var rejectId = $(this).attr('data');
        bootstrapQ.dialog({
            type : 'get',
            url : 'receipt/'+rejectId,
            title : '确认收货',
            className : 'modal-lg',
            id : 'confirm'
        },function(){
            var receiptReason = $('#confirm-reason').val();
            if(isNull(receiptReason)){receiptReason=null;}
            var returnFeeReason = $('#return-fee-reason').val();
            if(isNull(returnFeeReason)){returnFeeReason=null;}
            var shipFeeResult = $('.ship-fee-result:checked').val();
            shipFeeResult = parseInt(shipFeeResult);
            var goodsResult = $('.goods-result:checked').val();
            goodsResult = parseInt(goodsResult);
            var param = {};
            param.data = {};
            param.data.receiptReason = receiptReason;
            param.data.returnFeeReason = returnFeeReason;
            param.data.shipFeeResult = shipFeeResult;
            param.data.goodsResult = goodsResult;
            ajaxData('post','platformConfirm/'+rejectId, function($result) {
                if($result){
                    $('#container').append($result);
                }
            },[],param);
        });
    });
    $('#ship-fee-return').on('click',function(){
        var rejectRequestId = $(this).attr('data');
        bootstrapQ.dialog({
            type : 'get',
            url : 'shipFee/'+rejectRequestId,
            title : '退邮费',
            className : 'modal-lg',
            id : 'confirm'
        },function(){
            var fee = $('.person-return-ship-fee').val();
            var personId = $('#person-id').val();
            var rejectId = $('.person-reject-id').val();
            var param = {};
            param.data = {};
            param.data.fee = fee;
            param.data.rejectId = rejectId;
            param.data.personId = personId;
            ajaxData('post','returnShipFee/'+rejectRequestId,function($result){
                if($result){
                    $('#container').append($result);
                }
            },[],param);
        });
    });
</script>