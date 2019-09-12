<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/1
 * Time: 10:29
 */
$data = $result->data;
if(!is_null($data)){
    $rejectRequestId = $data->id;
    if($data->rejectResultHandle != null){
        $rejectId = $data->rejectResultHandle->reject_id;
    }
}
?>
<div class="widget-content form-horizontal">
    {{--<div class="control-group">
        <label class="control-label">快递名称：</label>
        <div class="controls">
            <input type="text" name="shipName" id="shipName" value="" />
        </div>
    </div>--}}
    <div class="control-group">
        <label class="control-label">发货单号：</label>
        <div class="controls">
            <input type="text" name="shipNo" id="ship-num" value="" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">平台给用户看的快递费：</label>
        <div class="controls">
            <input type="text" name="fee" id="user-fee" value="" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">快递包装费用：</label>
        <div class="controls">
            <input type="text" name="netFee" id="net-fee" value="" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">快递成本费用：</label>
        <div class="controls">
            <input type="text" name="costFee" id="cost-fee" value="" />
        </div>
    </div>
    {{--<div class="control-group">
        <label class="control-label">是否包邮：</label>
        <div class="controls">
            <label>
                <input type="radio" class="is-free" name="isFree" value="1" />
                包邮</label>
            <label>
                <input type="radio" class="is-free" name="isFree" value="0" />
                不包邮</label>
        </div>
    </div>--}}
    <a href="javascript:void (0);" id="save-exchange" class="btn btn-success">保存</a>
</div>

<script>
    $('#save-exchange').on('click',function(){
        var costFee = $('#cost-fee').val();
        var netFee = $('#net-fee').val();
        var fee = $('#user-fee').val();
        var shipNo = $('#ship-num').val();
        var rejectId = "{{ isset($rejectId) ? $rejectId : null }}";
        var rejectRequestId = "{{$rejectRequestId}}";
        var param = {};
        param.data = {};
        param.data.shipNo = shipNo;
        param.data.fee = fee;
        param.data.netFee = netFee;
        param.data.costFee = costFee;
        param.data.rejectId = rejectId;
        param.data.rejectRequestId = rejectRequestId;
        ajaxData('post', 'exchange', function (result) {
            if(!isNull(result)) {
                $('#container').append(result);
            }
        }, [], param);
    })
</script>