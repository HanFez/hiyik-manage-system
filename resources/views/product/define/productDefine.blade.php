<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/6
 * Time: 15:50
 */
$action = isset($action) ? $action : null;
$pd = isset($pd)?$pd:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改产品定义"}}@else{{"定义产品"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-product-define">
            <div class="control-group">
                <label class="control-label">产品定义名称缩写：</label>
                <div class="controls">
                    <input type="text" name="name_abbr" class="span4" value="{{$action == 'edit'?$pd->name_abbr:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">产品定义名称：</label>
                <div class="controls">
                    <input type="text" name="name" class="span4" value="{{$action == 'edit'?$pd->name:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">产品定义描述：</label>
                <div class="controls">
                    <input type="text" name="des" class="span4" value="{{$action == 'edit'?$pd->description:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">产品定义图标：</label>
                <div class="controls">
                    <input type="text" name="icon" class="span4" value="{{$action == 'edit'?$pd->icon:''}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-product-define">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-product-define');
    var action = '{{$action}}';
    var id = '{{is_null($pd) ? NULL : $pd->id}}';
    $('#save-product-define').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        param = {};
        param.data = data;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateProductDefine/'+id, function (result) {
                if(result) {
                    form.append(result);
                    //$('#form-product-define input').val('');
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createProductDefine', function (result) {
                if(result) {
                    form.append(result);
                    //$('#form-product-define input').val('');
                }
            }, [],param);
        }
    });
</script>
