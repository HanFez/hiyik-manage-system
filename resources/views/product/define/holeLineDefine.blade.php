<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/18
 * Time: 16:41
 */
$action = isset($action) ? $action : null;
$materials = isset($materials)?$materials:null;
$shapes = isset($shapes)?$shapes:null;
$holeLine = isset($holeLine)?$holeLine:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改洞线条定义"}}@else{{"定义洞线条"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-hole-line-define">
            <div class="control-group">
                <label class="control-label">材料：</label>
                <div class="controls">
                    <select name="material" id="material-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($materials as $material)
                                <option value="{{$material->id}}" {{$material->id == $holeLine->material_id ? 'selected="selected"':''}}>
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
                <label class="control-label">形状：</label>
                <div class="controls">
                    <select name="shape" id="shape-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($shapes as $shape)
                                <option value="{{$shape->id}}" {{$shape->id == $holeLine->shape_id ? 'selected="selected"':''}}>
                                    {{$shape->name}}
                                </option>
                            @endforeach
                        @else
                            @foreach($shapes as $shape)
                                <option value="{{$shape->id}}">{{$shape->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">是否可变形：</label>
                <div class="controls">
                    @if($action == 'edit')
                        <label>是：<input type="radio" name="isDeformable" value="1" {{$holeLine->is_deformable === true ? 'checked="checked"' : ""}}></label>
                        <label>否：<input type="radio" name="isDeformable" value="0" {{$holeLine->is_deformable === false ? 'checked="checked"' : ""}}></label>
                    @else
                        <label>是：<input type="radio" name="isDeformable" value="1"></label>
                        <label>否：<input type="radio" name="isDeformable" value="0"></label>
                    @endif
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">价格：</label>
                <div class="controls">
                    <input type="text" name="price" value="{{$action == 'edit'?$holeLine->price:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">单位：</label>
                <div class="controls">
                    <input type="text" name="priceUnit" value="{{$action == 'edit'?$holeLine->price_unit:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">货币：</label>
                <div class="controls">
                    <input type="text" name="currency" value="{{$action == 'edit'?$holeLine->currency:''}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-hole-line-define">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-hole-line-define');
    var action = '{{$action}}';
    var id = '{{is_null($holeLine) ? NULL : $holeLine->id}}';
    $('#save-hole-line-define').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);

        var materialId = $('#material-id').val();
        var shapeId = $('#shape-id').val();
        var param = {};
        param.data = data;
        param.data.materialId = materialId;
        param.data.shapeId = shapeId;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateHoleLine/'+id, function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createHoleLine', function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }
    });
</script>