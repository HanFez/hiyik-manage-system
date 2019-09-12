<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/11
 * Time: 14:27
 */
$action = isset($action) ? $action : null;
$materials = isset($materials)?$materials:null;
$mcs = isset($mcs)?$mcs:null;
$facades = isset($facades)?$facades:null;
$bmd = isset($bmd)?$bmd:null;
$rgba = isset($rgba)?$rgba:null;
$path = \App\IekModel\Version1_0\Constants\Path::FILE_PATH;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改框材料定义"}}@else{{"定义框材料"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-border-material-define">
            <div class="control-group">
                <label class="control-label">名称：</label>
                <div class="controls">
                    <input type="text" name="name" value="{{$action == 'edit'?$bmd->name:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">描述：</label>
                <div class="controls">
                    <input type="text" name="des" value="{{$action == 'edit'?$bmd->description:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">材料：</label>
                <div class="controls">
                    <select name="material" id="material-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($materials as $material)
                                <option value="{{$material->id}}" {{$material->id == $bmd->material_id ? 'selected="selected"':''}}>
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
                <label class="control-label">材料分类：</label>
                <div class="controls">
                    <select name="category" id="category-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($mcs as $mc)
                                <option value="{{$mc->id}}" {{$mc->id == $bmd->category_id ? 'selected="selected"':''}}>
                                    {{$mc->name}}
                                </option>
                            @endforeach
                        @else
                            @foreach($mcs as $mc)
                                <option value="{{$mc->id}}">{{$mc->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div id="facade" class="control-group thumbnail-box">
                <label class="control-label">外观：</label>
                <div class="controls">
                    @if($action == 'edit')
                        @foreach($facades as $facade)
                            <div class="thumbnail {{$facade->id == $bmd->facade_id ? 'active' : ''}}">
                                <img data="{{$facade->id}}" src="{{$path.$facade->uri}}">
                                <div class="caption">{{$facade->file_name}}</div>
                            </div>
                        @endforeach
                    @else
                        @foreach($facades as $facade)
                            <div class="thumbnail">
                                <img data="{{$facade->id}}" src="{{$path.$facade->uri}}">
                                <div class="caption">{{$facade->file_name}}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">物理深度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyDepth" value="{{$action == 'edit'?$bmd->phy_depth:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">物理高度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyHeight" value="{{$action == 'edit'?$bmd->phy_height:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">物理可视高度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyVisualHeight" value="{{$action == 'edit'?$bmd->phy_visual_height:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">压画深度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyPressDepth" value="{{$action == 'edit'?$bmd->phy_press_depth:''}}">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">压画深度偏移(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyPressDepthOffset" value="{{$action == 'edit'?$bmd->phy_press_depth_offset:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">压画高度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyPressHeight" value="{{$action == 'edit'?$bmd->phy_press_height:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">压画高度偏移(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyPressHeightOffset" value="{{$action == 'edit'?$bmd->phy_press_height_offset:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">槽深度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phySlotDepth" value="{{$action == 'edit'?$bmd->phy_slot_depth:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">槽深度偏移(mm)：</label>
                <div class="controls">
                    <input type="number" name="phySlotDepthOffset" value="{{$action == 'edit'?$bmd->phy_slot_depth_offset:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">槽高度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phySlotHeight" value="{{$action == 'edit'?$bmd->phy_slot_height:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">槽高度偏移(mm)：</label>
                <div class="controls">
                    <input type="number" name="phySlotHeightOffset" value="{{$action == 'edit'?$bmd->phy_slot_height_offset:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最大长度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyLengthMax" value="{{$action == 'edit'?$bmd->phy_length_max:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最小长度(mm)：</label>
                <div class="controls">
                    <input type="number" name="phyLengthMin" value="{{$action == 'edit'?$bmd->phy_length_min:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">颜色名称：</label>
                <div class="controls">
                    <input type="text" name="colorName" value="{{$action == 'edit'?$bmd->color_name:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">RGBA：</label>
                <div class="controls">
                    <input type="number" name="r" value="{{$action == 'edit'?$rgba[0]:''}}" class="span1">
                    <input type="number" name="g" value="{{$action == 'edit'?$rgba[1]:''}}" class="span1">
                    <input type="number" name="b" value="{{$action == 'edit'?$rgba[2]:''}}" class="span1">
                    <input type="number" name="a" value="{{$action == 'edit'?$rgba[3]:''}}" class="span1">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">价格：</label>
                <div class="controls">
                    <input type="text" name="price" value="{{$action == 'edit'?$bmd->price:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">单位：</label>
                <div class="controls">
                    <input type="text" name="priceUnit" value="{{$action == 'edit'?$bmd->price_unit:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">货币：</label>
                <div class="controls">
                    <input type="text" name="currency" value="{{$action == 'edit'?$bmd->currency:''}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-border-material-define">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-border-material-define');
    $('#facade .thumbnail').on('click', function () {//选中当前图片
        $('#facade .thumbnail').removeClass('active');
        $(this).addClass('active');
    });
    var action = '{{$action}}';
    var id = '{{is_null($bmd) ? NULL : $bmd->id}}';
    $('#save-border-material-define').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);

        var materialId = $('#material-id').val();
        var categoryId = $('#category-id').val();
        var facadeId = $("#facade .thumbnail.active img").attr('data');
        if(facadeId == undefined) facadeId = null;
        var param = {};
        param.data = data;
        param.data.materialId = materialId;
        param.data.categoryId = categoryId;
        param.data.facadeId = facadeId;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateBorderMaterialDefine/'+id, function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createBorderMaterialDefine', function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }
    });
</script>