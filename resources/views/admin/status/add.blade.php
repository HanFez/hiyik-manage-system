<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/13
 * Time: 10:43
 */
?>

<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>添加状态</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-status">
            <div class="control-group">
                <label class="control-label">状态：</label>
                <div class="controls">
                    <input type="text" name="status" class="span4">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">描述：</label>
                <div class="controls">
                    <input type="text" name="description" class="span4">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-status">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-status');
    $('#save-status').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        param = {};
        param.data = data;
        ajaxData('post', 'addStatus', function (result) {
            if(result) {
                $('#form-status input').val('');
                $('#form-status').append(result);
            }
        }, [],param);
    });
</script>
