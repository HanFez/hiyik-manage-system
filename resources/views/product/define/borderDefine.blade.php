<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/11
 * Time: 10:07
 */
$action = isset($action) ? $action : null;
$materials = isset($materials)?$materials:null;
$shapes = isset($shapes)?$shapes:null;
$border = isset($border)?$border:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改框定义"}}@else{{"定义框"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-border-define">
            <div class="control-group">
                <label class="control-label">材料：</label>
                <div class="controls">
                    <select name="material" id="material-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($materials as $material)
                                <option value="{{$material->id}}" {{$material->id == $border->material_id ? 'selected="selected"':''}}>
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
                                <option value="{{$shape->id}}" {{$shape->id == $border->shape_id ? 'selected="selected"':''}}>
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
                <label class="control-label">最大长度(mm)：</label>
                <div class="controls">
                    <input type="text" name="phyLengthMax" value="{{$action == 'edit'?$border->phy_length_max:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最小长度(mm)：</label>
                <div class="controls">
                    <input type="text" name="phyLengthMin" value="{{$action == 'edit'?$border->phy_length_min:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">价格：</label>
                <div class="controls">
                    <input type="text" name="price" value="{{$action == 'edit'?$border->price:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">单位：</label>
                <div class="controls">
                    <input type="text" name="priceUnit" value="{{$action == 'edit'?$border->price_unit:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">货币：</label>
                <div class="controls">
                    <input type="text" name="currency" value="{{$action == 'edit'?$border->currency:''}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-border-define">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-border-define');
    var action = '{{$action}}';
    var id = '{{is_null($border) ? NULL : $border->id}}';
    $('#save-border-define').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);

        var materialId = $('#material-id').val();
        var shapeId = $('#shape-id').val();
        var param = {};
        param.data = data;
        param.data.materialId = materialId;
        param.data.shapeId = shapeId;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateBorderDefine/'+id, function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createBorderDefine', function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }
    });
</script>