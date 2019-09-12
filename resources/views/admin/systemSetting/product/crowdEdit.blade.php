<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/9
 * Time: 14:47
 */

?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>修改人群</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-crowd">
            <div class="control-group">
                <label class="control-label">人群：</label>
                <div class="controls">
                    <input type="text" name="crowdName" class="span5" value="{{$crowd->name}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">描述：</label>
                <div class="controls">
                    <input type="text" name="crowdDescription" class="span5" value="{{$crowd->description}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-crowd">保存</a>
            </div>
            <input type="hidden" id="edit-id" value="{{$crowd->id}}">
        </form>
    </div>
</div>
<script>
    var form = $('#form-crowd');
    var id = $('#edit-id').val();
    $('#save-crowd').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        param = {};
        param.data = data;
        ajaxData('put', 'crowd/'+id, function (result) {
            if(result) {
                $(form).append(result);
            }
        }, [],param);
    });
</script>

