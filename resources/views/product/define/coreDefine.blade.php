<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/13
 * Time: 15:23
 */
$action = isset($action) ? $action : null;
$materials = isset($materials)?$materials:null;
$handles = isset($handles)?$handles:null;
$core = isset($core)?$core:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改画芯定义"}}@else{{"定义画芯"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-core-define">
            <div class="control-group">
                <label class="control-label">材料：</label>
                <div class="controls">
                    <select name="material" id="material-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($materials as $material)
                                <option value="{{$material->id}}" {{$material->id == $core->material_id ? 'selected="selected"':''}}>
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
                <label class="control-label">工艺：</label>
                <div class="controls">
                    <select name="handle" id="handle-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($handles as $handle)
                                <option value="{{$handle->id}}" {{$handle->id == $core->handle_id ? 'selected="selected"':''}}>
                                    {{$handle->name}}
                                </option>
                            @endforeach
                        @else
                            @foreach($handles as $handle)
                                <option value="{{$handle->id}}">{{$handle->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">价格：</label>
                <div class="controls">
                    <input type="text" name="price" value="{{$action == 'edit'?$core->price:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">单位：</label>
                <div class="controls">
                    <input type="text" name="priceUnit" value="{{$action == 'edit'?$core->price_unit:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">货币：</label>
                <div class="controls">
                    <input type="text" name="currency" value="{{$action == 'edit'?$core->currency:''}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-core-define">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-core-define');
    var action = '{{$action}}';
    var id = '{{is_null($core) ? NULL : $core->id}}';
    $('#save-core-define').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);

        var materialId = $('#material-id').val();
        var handleId = $('#handle-id').val();
        var param = {};
        param.data = data;
        param.data.materialId = materialId;
        param.data.handleId = handleId;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateCoreDefine/'+id, function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createCoreDefine', function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }
    });
</script>