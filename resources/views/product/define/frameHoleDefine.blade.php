<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/19
 * Time: 9:39
 */
$action = isset($action) ? $action : null;
$materials = isset($materials)?$materials:null;
$shapes = isset($shapes)?$shapes:null;
$frameHole = isset($frameHole)?$frameHole:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改卡纸开洞定义"}}@else{{"定义卡纸开洞"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-frame-hole-define">
            <div class="control-group">
                <label class="control-label">形状：</label>
                <div class="controls">
                    <select name="shape" id="shape-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($shapes as $shape)
                                <option value="{{$shape->id}}" {{$shape->id == $frameHole->shape_id ? 'selected="selected"':''}}>
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
                <label class="control-label">斜面度(°)：</label>
                <div class="controls">
                    <input type="text" name="bevel" value="{{$action == 'edit'?$frameHole->bevel:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最小宽度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyWidthMin" value="{{$action == 'edit'?$frameHole->phy_width_min:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最小高度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyHeightMin" value="{{$action == 'edit'?$frameHole->phy_height_min:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最小外边距(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyMarginMin" value="{{$action == 'edit'?$frameHole->phy_margin_min:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">是否可变形：</label>
                <div class="controls">
                    @if($action == 'edit')
                        <label>是：<input type="radio" name="isDeformable" value="1" {{$frameHole->is_deformable === true ? 'checked="checked"' : ""}}></label>
                        <label>否：<input type="radio" name="isDeformable" value="0" {{$frameHole->is_deformable === false ? 'checked="checked"' : ""}}></label>
                    @else
                        <label>是：<input type="radio" name="isDeformable" value="1"></label>
                        <label>否：<input type="radio" name="isDeformable" value="0"></label>
                    @endif
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">价格：</label>
                <div class="controls">
                    <input type="text" name="price" value="{{$action == 'edit'?$frameHole->price:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">单位：</label>
                <div class="controls">
                    <input type="text" name="priceUnit" value="{{$action == 'edit'?$frameHole->price_unit:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">货币：</label>
                <div class="controls">
                    <input type="text" name="currency" value="{{$action == 'edit'?$frameHole->currency:''}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-frame-hole-define">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-frame-hole-define');
    var action = '{{$action}}';
    var id = '{{is_null($frameHole) ? NULL : $frameHole->id}}';
    $('#save-frame-hole-define').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        var shapeId = $('#shape-id').val();
        var param = {};
        param.data = data;
        param.data.shapeId = shapeId;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateFrameHole/'+id, function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createFrameHole', function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }
    });
</script>