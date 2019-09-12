<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/14
 * Time: 12:08
 */
$action = isset($action) ? $action : null;
$hcs = isset($hcs)?$hcs:null;
$handles = isset($handles)?$handles:null;
$chd = isset($chd)?$chd:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改画芯工艺定义"}}@else{{"定义画芯工艺"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-core-handle-define">
            <div class="control-group">
                <label class="control-label">名称：</label>
                <div class="controls">
                    <input type="text" name="name" value="{{$action == 'edit'?$chd->name:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">描述：</label>
                <div class="controls">
                    <input type="text" name="des" value="{{$action == 'edit'?$chd->description:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">工艺：</label>
                <div class="controls">
                    <select name="handle" id="handle-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($handles as $handle)
                                <option value="{{$handle->id}}" {{$handle->id == $chd->handle_id ? 'selected="selected"':''}}>
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
                <label class="control-label">工艺分类：</label>
                <div class="controls">
                    <select name="category" id="category-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($hcs as $hc)
                                <option value="{{$hc->id}}" {{$hc->id == $chd->category_id ? 'selected="selected"':''}}>
                                    {{$hc->name}}
                                </option>
                            @endforeach
                        @else
                            @foreach($hcs as $hc)
                                <option value="{{$hc->id}}">{{$hc->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最大宽度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyWidthMax" value="{{$action == 'edit'?$chd->phy_width_max:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最小宽度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyWidthMin" value="{{$action == 'edit'?$chd->phy_width_min:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最大高度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyHeightMax" value="{{$action == 'edit'?$chd->phy_height_max:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最小高度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyHeightMin" value="{{$action == 'edit'?$chd->phy_height_max:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最大厚度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyDepthMax" value="{{$action == 'edit'?$chd->phy_depth_max:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最小厚度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyDepthMin" value="{{$action == 'edit'?$chd->phy_depth_min:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">价格：</label>
                <div class="controls">
                    <input type="text" name="price" value="{{$action == 'edit'?$chd->price:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">单位：</label>
                <div class="controls">
                    <input type="text" name="priceUnit" value="{{$action == 'edit'?$chd->price_unit:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最大DPI：</label>
                <div class="controls">
                    <input type="text" name="dpiMax" value="{{$action == 'edit'?$chd->dpi_max:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最小DPI：</label>
                <div class="controls">
                    <input type="text" name="dpiMin" value="{{$action == 'edit'?$chd->dpi_min:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">是否纯色：</label>
                <div class="controls">
                    @if($action == 'edit')
                        <label>是：<input type="radio" name="isFullColor" value="1" {{$chd->is_full_color === true ? 'checked="checked"' : ""}}></label>
                        <label>否：<input type="radio" name="isFullColor" value="0" {{$chd->is_full_color === false ? 'checked="checked"' : ""}}></label>
                    @else
                        <label>是：<input type="radio" name="isFullColor" value="1"></label>
                        <label>否：<input type="radio" name="isFullColor" value="0"></label>
                    @endif
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-core-handle-define">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-core-handle-define');
    var action = '{{$action}}';
    var id = '{{is_null($chd) ? NULL : $chd->id}}';
    $('#save-core-handle-define').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);

        var categoryId = $('#category-id').val();
        var handleId = $('#handle-id').val();
        var param = {};
        param.data = data;
        param.data.handleId = handleId;
        param.data.categoryId = categoryId;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateCoreHandleDefine/'+id, function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createCoreHandleDefine', function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }
    });
</script>