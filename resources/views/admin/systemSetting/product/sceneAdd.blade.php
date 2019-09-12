<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/8
 * Time: 16:27
 */
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>添加场景</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-scene">
            <div class="control-group">
                <label class="control-label">场景：</label>
                <div class="controls">
                    <input type="text" name="sceneName" class="span5">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">类型：</label>
                <div class="controls">
                    <input type="text" name="sceneClass" class="span5">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">描述：</label>
                <div class="controls">
                    <input type="text" name="sceneDescription" class="span5">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-scene">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-scene');
    $('#save-scene').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        param = {};
        param.data = data;
        ajaxData('post', 'addScene', function (result) {
            if(result) {
                //$('#form-scene input').val('');
                $(form).append(result);
            }
        }, [],param);
    });
</script>
