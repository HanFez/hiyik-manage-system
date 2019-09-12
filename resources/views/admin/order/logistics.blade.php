<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/11/13
 * Time: 9:50
 */
$data = $result->data;
//dd($data);
if($data != null){
    $ship = $data->ship;
    if($ship != null){
        $shipMessage = $ship->shipMessage;
        if($shipMessage != null && $shipMessage->message != null){
            $message = json_decode($shipMessage->message);
            if($message->showapi_res_body->flag != false){
                $statusArr = ['查询异常', '暂无记录', '运输中', '派送中', '已签收', '用户拒签', '疑难件', '无效单', '超时单', '签收失败', '退回'];
                $weekArr = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
                $shipStatus = $message->showapi_res_body->status;
                if($shipStatus == -1){
                    $shipStatus = '待查询';
                }else {
                    $shipStatus = $statusArr[$shipStatus];
                }
                $tempData = [];
                foreach($message->showapi_res_body->data as $k => $shipData){
                    $week = date('w',strtotime($shipData->time));
                    $weekText = $weekArr[$week];
                    $tempData[$k]['week'] = $weekText;
                    $tempData[$k]['time'] = $shipData->time;
                    $tempData[$k]['content'] = $shipData->context;
                }
            }
        }
    }
}

?>
@if(!is_null($data))
    <div class="widget-box">
        <div class="widget-content">
            @if($message->showapi_res_body->flag == true)
                <form class="form-horizontal" id="shipMessage">
                    <div class="control-group">
                        <label class="control-label">是否获取到物流信息：</label>
                        <div class="controls">
                            <strong>{{ $message->showapi_res_body->flag == true ? "是": "否" }}</strong>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">物流名称：</label>
                        <div class="controls" id="shipName" data="{{$message->showapi_res_body->expSpellName}}">
                            <strong>{{ $message->showapi_res_body->expTextName }}</strong>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">物流电话：</label>
                        <div class="controls">
                            <strong>{{ $message->showapi_res_body->tel }}</strong>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">快递单号：</label>
                        <div class="controls">
                            <strong>{{ $message->showapi_res_body->mailNo }}</strong>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">订单状态：</label>
                        <div class="controls">
                            <strong>{{ $shipStatus }}</strong>
                        </div>
                    </div>
                    @if($message->showapi_res_body->data != null)
                        <div class="control-group">
                            <label class="control-label">物流节点跟踪：</label>
                            <div class="controls">
                                <ul>
                                    @foreach($tempData as $key => $d)
                                        <li style="margin-top: 10px;">
                                            @if($key == 0)
                                                <span style="color: #ff0000">
                                                    {{ $d['week'] }}
                                                    <span style="margin: 20px 20px;">{{$d['time']}}</span>
                                                    {{ $d['content'] }}
                                                </span>
                                            @else
                                                {{ $d['week'] }}
                                                <span style="margin: 20px 20px;">{{$d['time']}}</span>
                                                {{ $d['content'] }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    <div class="control-group">
                        <label class="control-label">查询时间：</label>
                        <div class="controls">
                            <strong><?php echo $shipMessage->updated_at?></strong>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">请选择快递公司：</label>
                        <div class="controls">
                            @if(!$result->company->isEmpty())
                                <select name="shipName">
                                    @foreach($result->company as $company)
                                        <option value="{{$company->name}}" {{$company->id == $ship->provider_id ? "selected='selected'":''}}>
                                            {{$company->description}}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            {{--<input type="text" placeholder="例:shunfeng" name="shipName" class="span2">--}}
                            <a href="javascript:void (0);" class="btn btn-success" type="button" id="shipSearch" data="{{ !is_null($ship)?$ship->no:null }}">刷新信息</a>
                        </div>
                    </div>
                </form>
            @else
                <div class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label">是否获取到物流信息：</label>
                        <div class="controls">
                            <strong>{{ $message->showapi_res_body->flag == true ? "是": "否" }}</strong>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">物流查询结果：</label>
                        <div class="controls">
                            <strong>{{ $message->showapi_res_body->msg }}</strong>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">查询时间：</label>
                        <div class="controls">
                            <strong><?php echo $shipMessage->updated_at?></strong>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">请选择快递公司：</label>
                        <div class="controls">
                            @if(!$result->company->isEmpty())
                                <select name="shipName">
                                    @foreach($result->company as $company)
                                        <option value="{{$company->name}}" {{$company->id == $ship->provider_id ? "selected='selected'":''}}>
                                            {{$company->description}}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            {{--<input type="text" placeholder="例:shunfeng" name="shipName" class="span2">--}}
                            <a href="javascript:void (0);" class="btn btn-success" type="button" id="shipSearch"
                               data="{{ !is_null($ship)?$ship->no:null }}">重新查询</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@else
    <div class="widget-box">
        <div class="widget-content">
            <form id="shipMessage" class="form-horizontal">
                <div class="control-group">
                    <label class="control-label">物流查询结果：</label>
                    <div class="controls">
                        <strong>{{ $result->message }}</strong>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">请选择快递公司：</label>
                    <div class="controls">
                        @if(!$result->company->isEmpty())
                            <select name="shipName">
                                @foreach($result->company as $company)
                                    <option value="{{$company->name}}" {{$company->id == $result->orderShip->ship->provider_id ? "selected='selected'":''}}>
                                        {{$company->description}}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        {{--<input type="text" placeholder="例:shunfeng" name="shipName" class="span2">--}}
                        <a href="javascript:void (0);" class="btn btn-success" type="button" id="shipSearch" data="{{ $result->no }}">查询快递</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif

{{--<script src="/js/logistics.js"></script>--}}
<script>
    $('#shipSearch').on('click',function(){
        var no = $(this).attr('data');
        var shipName = $('select[name="shipName"] option:selected').val();
        ajaxData('get','queryLog?shipName='+shipName+'&no='+no,function(result){
            if(!isNull(result.showapi_res_body)){
                var html = '<div class="control-group">' +
                    '<label class="control-label">接口调用</label>' +
                    '<div class="controls"><strong>';
                    if(result.showapi_res_body.ret_code == 0){
                        html += '调用成功';
                    }else{
                        html += '调用失败';
                    }
                html += '</strong></div></div>';
                if(result.showapi_res_body.flag == true){
                    html += '<div class="control-group">' +
                        '<label class="control-label">物流信息</label>' +
                        '<div class="controls"><strong>获取成功</strong></div>' +
                    '</div>' +
                    '<div class="control-group">' +
                        '<label class="control-label">物流名称</label>' +
                        '<div class="controls"><strong>'+result.showapi_res_body.expTextName+'</strong></div>' +
                    '</div>' +
                    '<div class="control-group">' +
                        '<label class="control-label">物流电话</label>' +
                        '<div class="controls"><strong>'+result.showapi_res_body.tel+'</strong></div>' +
                    '</div>' +
                    '<div class="control-group">' +
                        '<label class="control-label">快递单号</label>' +
                        '<div class="controls"><strong>'+result.showapi_res_body.mailNo+'</strong></div>' +
                    '</div>' +
                    '<div class="control-group">' +
                        '<label class="control-label">物流节点跟踪</label>' +
                        '<div class="controls"><ul>';
                            for (var i in result.showapi_res_body.data){
                                html += '<li><span style="margin: 20px 20px;">'+result.showapi_res_body.data[i].time+'</span>' +
                                result.showapi_res_body.data[i].context+'</li>';
                            }
                        html += '</ul></div>' +
                    '</div>';
                }else{
                    html += '<div class="control-group">' +
                        '<label class="control-label">物流信息</label>' +
                        '<div class="controls"><strong>获取失败</strong></div>' +
                    '</div>'+
                    '<div class="control-group">' +
                        '<label class="control-label">查询结果</label>' +
                        '<div class="controls"><strong>' +result.showapi_res_body.msg+'</strong></div>' +
                    '</div>';
                }
                $('#shipMessage').append(html);
            } else {
                $('#shipMessage').append(result);
            }
        });
    });
</script>