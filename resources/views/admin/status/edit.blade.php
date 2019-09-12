<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/12
 * Time: 15:36
 */
?>

<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>修改状态</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-status">
            <div class="control-group">
                <label class="control-label">状态：</label>
                <div class="controls">
                    <input type="text" name="status" class="span4" value="{{$status->name}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">描述：</label>
                <div class="controls">
                    <input type="text" name="description" class="span4" value="{{$status->description}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-status">保存</a>
            </div>
            <input type="hidden" id="edit-id" value="{{$status->id}}">
        </form>
    </div>
</div>
<script>
    var form = $('#form-status');
    var id = $('#edit-id').val();
    $('#save-status').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        param = {};
        param.data = data;
        ajaxData('put', 'status/'+ id , function (result) {
            if(result) {
                $('#form-status').append(result);
            }
        }, [],param);
    });
</script>

