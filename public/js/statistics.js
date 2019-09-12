/**
 * Created by Administrator on 2017/11/1.
 */
function statistic(data){
    var dateTime = [];
    var buy = [];
    var cash = [];
    for (var item in data) {
        dateTime.push(item);
        buy.push(data[item].nums);
        cash.push(data[item].moneys);
        require.config({
            paths: {
                echarts: '/build/dist',
            }
        });
        // 使用
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
                        text: '',
                        subtext: ''
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['数量', '金额']
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
                        data: dateTime

                    },
                    yAxis: {
                        type: 'value',
                        axisLabel: {
                            formatter: '{value}'
                        }
                    },
                    series: [
                        {
                            name: '数量',
                            type: 'line',
                            data: buy,
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
                            name: '金额',
                            type: 'line',
                            data: cash,
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

}