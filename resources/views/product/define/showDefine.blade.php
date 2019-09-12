<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/14
 * Time: 15:40
 */
$action = isset($action) ? $action : null;
$show = isset($show)?$show:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改装饰定义"}}@else{{"定义装饰"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-show-define">
            <div class="control-group">
                <label class="control-label">装饰名称：</label>
                <div class="controls">
                    <input type="text" name="name" value="{{$action == 'edit'?$show->name:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">装饰描述：</label>
                <div class="controls">
                    <input type="text" name="des" value="{{$action == 'edit'?$show->description:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">是否默认：</label>
                <div class="controls">
                    @if($action == 'edit')
                        <label>是：<input type="radio" name="isDefault" value="1" {{$show->is_default === true ? 'checked="checked"' : ""}}></label>
                        <label>否：<input type="radio" name="isDefault" value="0" {{$show->is_default === false ? 'checked="checked"' : ""}}></label>
                    @else
                        <label>是：<input type="radio" name="isDefault" value="1"></label>
                        <label>否：<input type="radio" name="isDefault" value="0"></label>
                    @endif
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-show-define">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-show-define');
    var action = '{{$action}}';
    var id = '{{is_null($show) ? NULL : $show->id}}';
    $('#save-show-define').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        var param = {};
        param.data = data;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateShowDefine/'+id, function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createShowDefine', function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }
    });
</script>