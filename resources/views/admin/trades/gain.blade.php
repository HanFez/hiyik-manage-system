<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 15:02
 */

$records = $result->data;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 10;
$total  = isset($result->total) ? $result->total : null;
$url = 'gain?total='.$total;
$transDataTable = trans('dataTable');
$statistic = json_encode($result->statistic);
?>
@extends('layout/widget')

@section('title')
    版权收益记录
@stop
@section('content')
    <div class="data-list clearfix">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>版权费流水号</th>
                <th>收益用户</th>
                <th>收益金额</th>
                <th>收益时间</th>
            </tr>
            </thead>
            <tbody>
            @if(!$records->isEmpty())
                @foreach($records as $record)
                    <tr class="odd gradeX">
                        <td class="center">{{is_null($record->pay_no)?"无":$record->pay_no}}</td>
                        <td>
                            @if(!$record->person->personNick->isEmpty())
                                @foreach($record->person->personNick as $nick)
                                    @if($nick->is_active == true)
                                        {{$nick->nick->nick}}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>{{is_null($record->fee)?"无":$record->fee.$record->currency}}</td>
                        <td>{{is_null($record->created_at)?"无":$record->created_at}}</td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="4" style="text-align: center;background-color: #cde69c;font-size: medium;">没有查到任何记录！</td></tr>
            @endif
            </tbody>
        </table>
        @include('layout/pagination')
        <div>总共：{{$result->total}}条 每页：{{$result->take}}条</div>
    </div>
    <div class="row-fluid">
        <div class="widget-title"> <span class="icon"> <i class="icon-bar-chart"></i> </span>
            <h5>版权收益统计</h5>
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
    //统计数据
    var data = '<?php echo $statistic;?>';
    data = JSON.parse(data);
    statistic(data);
</script>