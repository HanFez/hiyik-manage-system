<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/22
 * Time: 10:15
 */
$total = isset($result->total) ? $result->total : 0;
$take  = isset($result->take) ? $result->take : 6;
$type = isset($result->type) ? $result->type : null;
$url = is_null($type) ? null : 'orderStatistics?type='.$type;

$today = date("Y-m-d H:i:s", mktime(0,0,0,date('m'),date('d'),date('Y')));
$weekday = date("Y-m-d H:i:s", mktime(0,0,0,date('m'),date('d')-7,date('Y')));
$monday = date("Y-m-d H:i:s", mktime(0,0,0,date('m'),date('d')-30,date('Y')));

$times = [];
if($result->data->isEmpty()){
    $times = null;
    $count = null;
    $prices = null;
}else{
    $prices = [];

    foreach($result->data as $k=>$data){
        $times[] = $k;
        $count[] = count($data);
        $price = 0;
        foreach($data as $pri){
            $price += $pri->price;
        }
        $prices[] = round($price,2);
    }
}

$times = json_encode($times);
$count = json_encode($count);
$prices = json_encode($prices);
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title"> <span class="icon"> <i class="icon-table"></i> </span>
                <h5>订单统计</h5>
            </div>
            <div class="widget-content">
                <div class="btn-group data-btn-group" id="filter-type" data-type="{{ $result->type }}">
                    <button data-toggle="dropdown" class="btn dropdown-toggle">
                        {{ \App\IekModel\Version1_0\IekModel::strTrans($result->type, 'order') }}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        @foreach($result->status as $status)
                            <li><a data-type="{{$status}}">{{ \App\IekModel\Version1_0\IekModel::strTrans($status, 'order') }}</a></li>
                            <li class="divider"></li>
                        @endforeach
                    </ul>

                </div>
                <div class="control-group">
                    <div class="controls">
                        <div data-toggle="buttons-radio" class="btn-group" id="starttime">
                            <button class="btn btn-primary" type="button" data="{{$today}}">今天</button>
                            <button class="btn btn-primary active" type="button" data="{{$weekday}}">近7天</button>
                            <button class="btn btn-primary" type="button" data="{{$monday}}">近30天</button>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div data-date="" class="input-append date datepicker" data-date-format="yyyy-mm-dd">
                            <input type="text" id="start" name="startTime">
                            <span class="add-on"><i class="icon-th"></i></span>
                        </div>

                        <div data-date="" class="input-append date datepicker" data-date-format="yyyy-mm-dd">
                            <input type="text" id="end" name="endTime">
                            <span class="add-on"><i class="icon-th"></i></span>
                        </div>
                        <div >
                            <button type="submit" class ="btn btn-success" title="查询" id="timeSearch"><i class="icon-white">查询</i></button>
                        </div>
                    </div>
                </div>
                <div>
                    <table class="table table-striped table-bordered table-hover" >
                        <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="20%">创建日期</th>
                            <th width="20%">订单数量</th>
                            <th width="20%">交易金额量</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($result->data) && !$result->data->isEmpty())
                            <?php $id = 0;?>
                            @foreach($result->data as $k=>$item)
                                <?php $id++;?>
                                <tr>
                                    <td>{{$id}}</td>
                                    <td>{{$k}}</td>
                                    <td>{{count($item)}}</td>
                                    <td>
                                        <?php
                                            $price = 0;
                                            foreach($item as $pri){
                                                $price += $pri->price;
                                                $unit = $pri->currency;
                                            }
                                        ?>
                                        {{$unit.$price}}
                                    </td>
                                </tr>
                            @endforeach
                            <?php unset($id);?>
                        @else
                            <tr>
                                <td colspan="4" style="text-align: center;background-color: #70e674;">{{"ε=(´ο｀*)))没有数据!"}}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row-fluid">
                <div class="widget-title"> <span class="icon"> <i class="icon-bar-chart"></i> </span>
                    <h5>图形显示</h5>
                </div>
                <div class="widget-content">
                    <div id="main" style="height:500px">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    bindEventToButtonInListView({
        'type': 'statistics',
        'take': '{{$take}}',
        'url': '{{ $url }}'
    });
    $('#starttime .btn').on('click',function(){
        var startTime = $(this).attr('data');
        var type = $('#filter-type').attr('data-type');
        ajaxData('get', 'orderStatistics?type='+type+'&startTime='+startTime,appendViewToContainer);
    });
    $('#timeSearch').unbind('click').on('click',function(){
        var startTime = $('#start').val();
        var endTime = $('#end').val();
        if(isNull(startTime)){
            startTime = null;
        }
        if(isNull(endTime)){
            endTime = null;
        }
        var type = $('#filter-type').attr('data-type');
        ajaxData('get','orderStatistics?type='+type+'&startTime='+ startTime+'&endTime='+endTime,appendViewToContainer);
    });
</script>
<script src="/build/dist/echarts.js"></script>
<script type="text/javascript">
    var str = '<?php echo $times;?>';
    var str1 = '<?php echo $count;?>';
    var str2 = '<?php echo $prices;?>';
    var times = JSON.parse(str);
    if(!isNull(times)){
        times.reverse();
    }
    var num = JSON.parse(str1);
    if(!isNull(num)){
        num.reverse();
    }
    var prices = JSON.parse(str2);
    if(!isNull(prices)){
        prices.reverse();
    }
    // 路径配置
    require.config({
        paths: {
            echarts: '/build/dist',
        }
    });
    // 使用
    if (times != null && num != null && prices != null) {
        require(
                [
                    'echarts',
                    'echarts/chart/bar', // 使用柱状图
                    'echarts/chart/line', // 使用折线图
                ],
                function (ec) {
                    // 基于准备好的dom，初始化echarts图表
                    var myChart = ec.init(document.getElementById('main'));

                    var option = {
                        title: {
                            text: '订单统计',
                            subtext: ''
                        },
                        tooltip: {
                            trigger: 'axis'
                        },
                        legend: {
                            data: ['订单数量', '交易金额量']
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                dataZoom: {
                                    yAxisIndex: 'none'
                                },
                                dataView: {show: true, readOnly: false},
                                magicType: {show: true, type: ['line', 'bar']},
                                restore: {show: true},
                                saveAsImage: {show: true}
                            }
                        },
                        xAxis: {
                            type: 'category',
                            boundaryGap: false,
                            data: times

                        },
                        yAxis: {
                            type: 'value',
                            axisLabel: {
                                formatter: '{value}'
                            }
                        },
                        series: [
                            {
                                name: '订单数量',
                                type: 'line',
                                data: num,
                                markPoint: {
                                    data: [
                                        {type: 'max', name: '最大值'},
                                        {type: 'min', name: '最小值'}
                                    ]
                                },
                                markLine: {
                                    data: [
                                        {type: 'average', name: '平均值'}
                                    ]
                                }
                            },
                            {
                                name: '交易金额量',
                                type: 'line',
                                data: prices,
                                markPoint: {
                                    data: [
                                        {type: 'max', name: '最大值'},
                                        {type: 'min', name: '最小值'}
                                    ]
                                },
                                markLine: {
                                    data: [
                                        {type: 'average', name: '平均值'}
                                    ]
                                }
                            }
                        ]
                    };

                    // 为echarts对象加载数据
                    myChart.setOption(option);
                }
        );
    }
</script>