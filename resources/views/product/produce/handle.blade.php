<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 11:35
 */
$action = isset($action) ? $action : null;
$handle = isset($handle)?$handle:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改工艺"}}@else{{"添加工艺"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-handle">
            <div class="control-group">
                <label class="control-label">工艺名称：</label>
                <div class="controls">
                    <input type="text" name="name" class="span4" value="{{$action == 'edit'?$handle->name:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">工艺描述：</label>
                <div class="controls">
                    <input type="text" name="des" class="span4" value="{{$action == 'edit'?$handle->description:''}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-handle">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-handle');
    var action = '{{$action}}';
    var id = '{{is_null($handle) ? NULL : $handle->id}}';
    $('#save-handle').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        param = {};
        param.data = data;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateHandle/'+id, function (result) {
                if(result) {
                    $('#form-handle').append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createHandle', function (result) {
                if(result) {
                    $('#form-handle').append(result);
                }
            }, [],param);
        }
    });
</script>