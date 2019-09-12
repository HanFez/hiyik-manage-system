<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/9
 * Time: 15:19
 */

?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>添加性别</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-sex">
            <div class="control-group">
                <label class="control-label">性别：</label>
                <div class="controls">
                    <input type="text" name="sexName" class="span5"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">描述：</label>
                <div class="controls">
                    <input type="text" name="sexDescription" class="span5">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-sex">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-sex');
    $('#save-sex').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        param = {};
        param.data = data;
        ajaxData('post', 'addSex', function (result) {
            if(result) {
                //$('#form-sex input').val('');
                $(form).append(result);
            }
        }, [],param);
    });
</script>

