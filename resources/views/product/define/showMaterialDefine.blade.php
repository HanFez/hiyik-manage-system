<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/14
 * Time: 16:22
 */
$action = isset($action) ? $action : null;
$materials = isset($materials)?$materials:null;
$shows = isset($shows)?$shows:null;
$smd = isset($smd)?$smd:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改装饰材料定义"}}@else{{"定义装饰材料"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-show-material-define">
            <div class="control-group">
                <label class="control-label">材料：</label>
                <div class="controls">
                    <select name="material" id="material-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($materials as $material)
                                <option value="{{$material->id}}" {{$material->id == $smd->material_id ? 'selected="selected"':''}}>
                                    {{$material->name}}
                                </option>
                            @endforeach
                        @else
                            @foreach($materials as $material)
                                <option value="{{$material->id}}">{{$material->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">装饰：</label>
                <div class="controls">
                    <select name="show" id="show-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($shows as $show)
                                <option value="{{$show->id}}" {{$show->id == $smd->handle_id ? 'selected="selected"':''}}>
                                    {{$show->name}}
                                </option>
                            @endforeach
                        @else
                            @foreach($shows as $show)
                                <option value="{{$show->id}}">{{$show->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">数量：</label>
                <div class="controls">
                    <input type="text" name="amount" value="{{$action == 'edit'?$smd->amount:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">是否默认：</label>
                <div class="controls">
                    @if($action == 'edit')
                        <label>是：<input type="radio" name="isDefault" value="1" {{$smd->is_default === true ? 'checked="checked"' : ""}}></label>
                        <label>否：<input type="radio" name="isDefault" value="0" {{$smd->is_default === false ? 'checked="checked"' : ""}}></label>
                    @else
                        <label>是：<input type="radio" name="isDefault" value="1"></label>
                        <label>否：<input type="radio" name="isDefault" value="0"></label>
                    @endif
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-show-material-define">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-show-material-define');
    var action = '{{$action}}';
    var id = '{{is_null($smd) ? NULL : $smd->id}}';
    $('#save-show-material-define').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);

        var materialId = $('#material-id').val();
        var showId = $('#show-id').val();
        var param = {};
        param.data = data;
        param.data.materialId = materialId;
        param.data.showId = showId;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateShowMaterialDefine/'+id, function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createShowMaterialDefine', function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }
    });
</script>