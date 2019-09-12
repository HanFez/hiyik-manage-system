<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 11:51
 */
$action = isset($action) ? $action : null;
$material = isset($material) ? $material : null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改材料"}}@else{{"添加材料"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-material">
            <div class="control-group">
                <label class="control-label">材料名称：</label>
                <div class="controls">
                    <input type="text" name="name" class="span4" value="{{$action == 'edit'?$material->name:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">材料描述：</label>
                <div class="controls">
                    <input type="text" name="des" class="span4" value="{{$action == 'edit'?$material->description:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">材料序列号：</label>
                <div class="controls">
                    <input type="text" name="serial_no" class="span4" value="{{$action == 'edit'?$material->serial_no:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">重量：</label>
                <div class="controls">
                    <input type="text" name="weight" class="span4" value="{{$action == 'edit'?$material->weight:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">重量单位：</label>
                <div class="controls">
                    <input type="text" name="weightUnit" class="span4" value="{{$action == 'edit'?$material->weight_unit:''}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-material">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-material');
    var action = '{{$action}}';
    var id = '{{is_null($material) ? NULL : $material->id}}';
    $('#save-material').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        param = {};
        param.data = data;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateMaterial/'+id, function (result) {
                if(result) {
                    $('#form-material').append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createMaterial', function (result) {
                if(result) {
                    $('#form-material').append(result);
                }
            }, [],param);
        }
    });
</script>
