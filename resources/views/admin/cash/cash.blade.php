<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/11/28
 * Time: 9:48
 */
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$total = isset($result->total) ? $result->total : null;
$type = isset($result->type) ? $result->type : null;
$param = isset($result->param) ? $result->param : null;
$url = is_null($type) ? null : 'withdraw?type=cash';
$transDataTable = trans('dataTable');
$cashs = $result->data;

?>
@extends('layout/widget')

@section('title')
    提现申请列表
@stop
@section('content')
    <div style="margin: 20px;">
        <h5>审核状态：</h5>
        <select class="span2" id="audit-type">
            <option value="cash" {{$type == 'cash' ? 'selected':''}}>提现申请审核</option>
            <option value="pay" {{$type == 'pay' ? 'selected':''}}>提现支付审核</option>
        </select>&nbsp;&nbsp;&nbsp;
        @if($type == 'cash')
            <div data-toggle="buttons-radio" class="btn-group pass-refuse">
                <button class="btn btn-primary {{$param == 'cash-wait'?'active':''}}" type="button" data="cash-wait">未审核</button>
                <button class="btn btn-primary {{$param == 'cash-yes'?'active':''}}" type="button" data="cash-yes">通过</button>
                <button class="btn btn-primary {{$param == 'cash-no'?'active':''}}" type="button" data="cash-no">未通过</button>
            </div>
        @elseif($type == 'pay')
            <div data-toggle="buttons-radio" class="btn-group pass-refuse">
                <button class="btn btn-primary {{$param == 'pay-wait'?'active':''}}" type="button" data="pay-wait">未审核</button>
                <button class="btn btn-primary {{$param == 'pay-yes'|| $param == 'unCash' || $param == 'cashed'?'active':''}}"
                        type="button" data="pay-yes">通过</button>
                <button class="btn btn-primary {{$param == 'pay-no'?'active':''}}" type="button" data="pay-no">未通过</button>
            </div>
        @endif
        @if($param == 'pay-yes' || $param == 'unCash' || $param == 'cashed')
            <div data-toggle="buttons-radio" class="cash-pay" style="margin-left: 280px; margin-top: 5px;">
                <button class="btn btn-primary {{$param == 'unCash'?'active':''}}" type="button" data="unCash">未提现</button>
                <button class="btn btn-primary {{$param == 'cashed'?'active':''}}" type="button" data="cashed">已提现</button>
            </div>
        @endif
    </div>
<div class="widget-content nopadding">
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>
                    <div class="checker" id="uniform-title-table-checkbox">
                        <span class="">
                            <input id="title-table-checkbox" name="title-table-checkbox" style="opacity: 0;" type="checkbox">
                        </span>
                    </div>
                </th>
                <th>流水号</th>
                <th>提现用户</th>
                <th>提现账户</th>
                <th>账户类型</th>
                <th>提现金额</th>
                <th>服务费</th>
                @if($type == 'pay')
                    <th style="color: #e64641">提现状态</th>
                @endif
                <th>申请时间</th>
                <th>申请审核状态</th>
                <th>提现审核状态</th>
            </tr>
        </thead>
        <tbody>
        @if(!$cashs->isEmpty())
            @foreach($cashs as $cash)
                <?php
                    if(!is_null($cash) && !is_null($cash->cashAudit) && !is_null($cash->cashAudit->reason)){
                        $reason = $cash->cashAudit->reason;
                    }
                ?>
                <tr>
                    <td>
                        <div class="checker">
                            <span class=""><input style="opacity: 0;" type="checkbox" value="{{$cash->id}}"></span>
                        </div>
                    </td>
                    <td>{{is_null($cash->pay_no)?"无":$cash->pay_no}}</td>
                    <td>
                        @if(!is_null($cash->person))
                            @foreach($cash->person->personNick as $nick)
                                @if($nick != null)
                                    {{is_null($nick->nick)?"无":$nick->nick->nick}}
                                @endif
                            @endforeach
                        @else
                            {{"无"}}
                        @endif
                    </td>
                    <td>{{!is_null($cash->thirdAccount)?$cash->thirdAccount->account:"无"}}</td>
                    <td>{{!is_null($cash->thirdAccount)?$cash->thirdAccount->platform:"无"}}</td>
                    <td>{{is_null($cash->fee)?"无":$cash->fee.$cash->currency}}</td>
                    <td>{{is_null($cash->service_charge)?"无":$cash->service_charge.$cash->currency}}</td>
                    @if($type == 'pay')
                            @if(!is_null($cash->cashRequestPay))
                                <td style="color: #4ae642">{{"已提现"}}</td>
                            @else
                                <td style="color: #6464e6">{{"未提现"}}</td>
                            @endif
                    @endif
                    <td>{{$cash->created_at}}</td>
                    <td>
                        @if($cash->cash_audit == '1')
                            {{"未审核"}}
                        @elseif($cash->cash_audit == '0')
                            {{"审核通过"}}
                            <p><a href="javascript:void(0);" class="check-reason" data-reason="{{ isset($reason)?$reason->reason:"无" }}">查看审核结果</a></p>
                        @else
                            {{"未通过审核"}}
                            <p><a href="javascript:void(0);" class="check-reason" data-reason="{{ isset($reason)?$reason->reason:"无" }}">查看审核结果</a></p>
                        @endif
                    </td>
                    <td>
                        @if($cash->pay_audit == '1')
                            {{"未审核"}}
                        @elseif($cash->pay_audit == '0')
                            {{"审核通过"}}
                            <p><a href="javascript:void(0);" class="check-reason" data-reason="{{ isset($reason)?$reason->reason:"无" }}">查看审核结果</a></p>
                        @else
                            {{"未通过审核"}}
                            <p><a href="javascript:void(0);" class="check-reason" data-reason="{{ isset($reason)?$reason->reason:"无" }}">查看审核结果</a></p>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="11" style="text-align: center;background-color: #cde69c">(。・＿・。)ﾉI’m sorry~ 还没有数据！</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
    <div id="notify"></div>
    <div class="data-list">
        <div>
            @if($param == 'unCash')
                <a href="javascript:void (0);" id="aliTransfer" class="btn btn-success" type="button" >提现</a>
            @elseif($param == 'cash-wait' || $param == 'pay-wait')
                <a id="bulk-audit" onclick="batchAudit(this)" class="btn btn-success" type="button" data-url="batchAudit">批量审核</a>
            @endif
        </div>
        @include('layout/pagination')
    </div>

@stop
<script>
    //huoqutixianzhuangtailiebiao
    $('#audit-type').on('click',function(){
        var type = $(this).find('option:selected').val();
        var url = 'withdraw?take=6&skip=0&type='+type;
        ajaxData('get', url, appendViewToContainer);
    });
    //piliangshenheanniufangfa
    function batchAudit(self) {
        var url = $(self).attr('data-url');
        if(isNull(url)) {
            return false;
        }
        var checkbox = $('tbody input[type="checkbox"]');
        var ids = [];
        var method = 'post';

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
            bootstrapQ.confirm({
                'id' : 'confirm',
                'msg' : '<div class="form-horizontal">' +
                '<div class="control-group">' +
                '<label class="control-label">是否通过审核：</label>' +
                '<div class="controls">' +
                '<label><input type="radio" name="status" value="0"> 同意 </label> ' +
                '<label><input type="radio" name="status" value="2"> 拒绝 </label></div></div>' +
                '<div class="control-group">' +
                '<label class="control-label">审核类型：</label>' +
                '<div class="controls">' +
                '<label><input type="radio" name="type" value="cash_audit"> 提现申请审核 </label> ' +
                '<label><input type="radio" name="type" value="pay_audit"> 提现支付审核 </label></div></div>' +
                '<div class="control-group">' +
                '<label class="control-label">请输入审核理由：</label>' +
                '<div class="controls">' +
                '<input type="text" name="reason" id="audit-reason" value=""></div></div></div>'
            },function(){
                var reason = $('#audit-reason').val();
                if(isNull(reason)){
                    reason = null;
                }
                var status = $('input[name="status"]:checked').val();
                status = parseInt(status);
                var type = $('input[name="type"]:checked').val();
                if(isNull(type)){type=null;}
                var params = {};
                params.data = {};
                params.data.ids = ids;
                params.data.status = status;
                params.data.type = type;
                params.data.reason = reason;
                params.data.param = "{{$param}}";
                ajaxData(method,url,function(result){
                    if(result){
                        $('#notify').append(result);
                    }
                },[],params);
            });
        }
    }
    //xuanzeshifoutongguoanniu
    $('.pass-refuse :button').on('click',function(){
        var type = "{{$type}}";
        var param = $(this).attr('data');
        var url = 'withdraw?take=6&skip=0&type='+type+'&param='+param;
        ajaxData('get',url,appendViewToContainer);
    });
    //xuanzeshifoutixiananniu
    $('.cash-pay :button').on('click',function(){
        var type = "{{$type}}";
        var param = $(this).attr('data');
        var url = 'withdraw?take=6&skip=0&type='+type+'&param='+param;
        ajaxData('get',url,appendViewToContainer);
    });
    //diaoyongtixiansanfangjiekou
    $('#aliTransfer').on('click',function(){
        var checkbox = $('tbody input[type="checkbox"]');
        var ids = [];
        var method = 'post';

        checkbox.each(function () {
            var $this = $(this);
            if($this.parent().hasClass('checked')) {
                var id = getVal($this);
                ids.push(id);
            }
        });
        if(ids.length == 0) {
            bootstrapQ.alert(trans_admin.enterData);
        }else{
            bootstrapQ.confirm({
                'id':'confirm',
                'msg': "请问是否确认提现操作？"
            },function(){
                var params = {};
                params.data = {};
                params.data.ids = ids;
                ajaxData(method,'transfer',function(result){
                    if(result){
                        $('#notify').append(result);
                    }
                },[],params);
            });
        }
    });
    //chakanshenhejieguo
    $('.check-reason').on('click',function(){
        var reason = $(this).attr('data-reason');
        bootstrapQ.alert({
            'id': "reason",
            'title': "审核结果",
            'msg': "内容："+reason
        });
    });
</script>