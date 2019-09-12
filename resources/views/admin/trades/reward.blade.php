<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 14:26
 */
$records = $result->data;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 10;
$type  = isset($result->type) ? $result->type : null;
$time = isset($result->time) ? $result->time : null;
$starttime = isset($result->starttime) ? $result->starttime : null;
$endtime = isset($result->endtime) ? $result->endtime : null;
$url = 'reward?type='.$type.'&time='.$time.'&startTime='.$starttime.'&endTime='.$endtime;
$transDataTable = trans('dataTable');
$statistic = json_encode($result->statistic);

//dd($records);
?>
@extends('layout/widget')

@section('title')
    打赏记录
@stop
@section('content')
    <div class="data-list clearfix">
        <form class="form-horizontal">
            <h4>请选择查询条件</h4>
            <div class="control-group">
                <label for="checkboxes" class="control-label">支付类型</label>
                <div class="controls">
                    <div data-toggle="buttons-radio" name="type" class="btn-group">
                        <button class="btn" type="button" data="wealth">钱包打赏</button>
                        <button class="btn" type="button" data="ali">支付宝打赏</button>
                    </div>
                </div>
            </div>
            @include('admin/trades/condition')
            <div style="margin: 5px;">
                <a type="button" class ="btn btn-success" title="查询" id="rewardSearch"><i class="icon-white">查询</i></a>
            </div>
        </form>
        @if($result->type == 'ali')
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>打赏流水号</th>
                    <th>打赏用户</th>
                    <th>被打赏用户</th>
                    <th>打赏金额</th>
                    <th>支付状态</th>
                    <th>打赏时间</th>
                    <th>付款账号</th>
                    <th>收款账号</th>
                    <th>第三方流水号</th>
                    <th>客户端</th>
                </tr>
                </thead>
                <tbody>
                @if(!$records->isEmpty())
                    @foreach($records as $record)
                        <tr class="odd gradeX">
                            <td class="center">{{is_null($record->pay_no)?"无":$record->pay_no}}</td>
                            <td>
                                @if(!$record->fromPerson->personNick->isEmpty())
                                    @foreach($record->fromPerson->personNick as $nick)
                                        @if($nick->is_active == true)
                                            {{$nick->nick->nick}}
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if(!$record->toPerson->personNick->isEmpty())
                                    @foreach($record->toPerson->personNick as $nick)
                                        @if($nick->is_active == true)
                                            {{$nick->nick->nick}}
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>{{is_null($record->fee)?"无":$record->fee.$record->currency}}</td>
                            <td>{{$record->status == true ? "支付成功":"支付失败"}}</td>
                            <td>{{is_null($record->created_at) ? "无": $record->created_at}}</td>
                            <td>{{is_null($record->fromAccount)?"无":$record->fromAccount->account}}</td>
                            <td class="center">{{is_null($record->toAccount)?"无":$record->toAccount->account}}</td>
                            <td>{{is_null($record->third_pay_no)?"无":$record->third_pay_no}}</td>
                            <td>{{is_null($record->client)?"无":$record->client}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="9" style="text-align: center;background-color: #cde69c;font-size: medium;">没有查到任何记录！</td></tr>
                @endif
                </tbody>
            </table>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>打赏流水号</th>
                    <th>打赏用户</th>
                    <th>被打赏用户</th>
                    <th>打赏金额</th>
                    <th>打赏方式</th>
                    <th>打赏时间</th>
                    <th>客户端</th>
                </tr>
                </thead>
                <tbody>
                @if(!$records->isEmpty())
                    @foreach($records as $record)
                        <tr>
                            <td>{{is_null($record->pay_no)?"无":$record->pay_no}}</td>
                            <td>
                                @if(!$record->fromPerson->personNick->isEmpty())
                                    @foreach($record->fromPerson->personNick as $nick)
                                        @if($nick->is_active == true)
                                            {{$nick->nick->nick}}
                                        @endif
                                    @endforeach
                                @else
                                    {{'无'}}
                                @endif
                            </td>
                            <td>
                                @if(!$record->toPerson->personNick->isEmpty())
                                    @foreach($record->toPerson->personNick as $nick)
                                        @if($nick->is_active == true)
                                            {{$nick->nick->nick}}
                                        @endif
                                    @endforeach
                                @else
                                    {{'无'}}
                                @endif
                            </td>
                            <td>{{is_null($record->fee)?"无":$record->currency.$record->fee}}</td>
                            <td>{{$record->pay_type == 'al'?"支付宝打赏":'钱包打赏'}}</td>
                            <td>{{is_null($record->created_at)?"无":$record->created_at}}</td>
                            <td>{{is_null($record->client)?"无":$record->client}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="7" style="text-align: center;background-color: #cde69c;font-size: medium;">没有查到任何记录！</td></tr>
                @endif
                </tbody>
            </table>
        @endif
        @include('layout/pagination')
        <div>总共：{{$result->total}}条 每页：{{$result->take}}条</div>
    </div>
    <div class="row-fluid">
        <div class="widget-title"> <span class="icon"> <i class="icon-bar-chart"></i> </span>
            <h5>打赏统计</h5>
        </div>
        <div class="widget-content">
            <div id="main" style="height:500px">

            </div>
        </div>
    </div>
@stop
<script src="/build/dist/echarts.js"></script>
<script src="/js/statistics.js"></script>
<script>
    bindEventToButtonInListView({
        'type': 'reward',
        'take': '{{$take}}',
        'url': '{{ $url }}'
    });
    //条件搜索
    $('#rewardSearch').on('click',function(){
        var btn1 = $('.btn-group[name="type"] .active');
        var type = btn1.attr('data');
        var btn2 = $('.btn-group[name="time"] .active');
        var time = btn2.attr('data');
        var startTime = $('#start-time').val();
        var endTime = $('#end-time').val();
        if(type == undefined && time == undefined && startTime == '' && endTime == ''){
            bootstrapQ.alert("请至少选择一个条件！");
            return false;
        }
        ajaxData('get','reward?type='+type+'&time='+time+'&startTime='+startTime+'&endTime='+endTime+'&take=6&skip=0'
        ,appendViewToContainer);
    });
    //统计数据
    var data = '<?php echo $statistic;?>';
    data = JSON.parse(data);
    statistic(data);
</script>