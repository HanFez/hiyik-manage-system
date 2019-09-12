<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/4
 * Time: 17:08
 */
$action = isset($action) ? $action : null;
$materials = isset($materials)?$materials:null;
$ms = isset($ms)?$ms:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改材料截面"}}@else{{"添加材料截面"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-section">
            <div class="control-group">
                <label class="control-label">材料：</label>
                <div class="controls">
                    <select name="material" id="material" style="width:220px;">
                        @if(!$materials->isEmpty())
                            @foreach($materials as $material)
                                @if($action == 'edit')
                                    <option value="{{$material->id}}" {{$material->id == $ms->material_id ? 'selected':''}}>
                                        {{$material->name}}
                                    </option>
                                @else
                                    <option value="{{$material->id}}">{{$material->name}}</option>
                                @endif
                            @endforeach
                        @else
                            <optoin>{{"请先添加材料"}}</optoin>
                        @endif
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">{{$action == 'edit'?"图形数据：":"材料截图："}}</label>
                <div class="controls">
                    @if($action == 'edit')
                        <textarea name="bezier" id="" cols="30" rows="10">{{$ms->bezier}}</textarea>
                    @else
                        <input type="button" id="getBezier" class="span2" value="获取图形数据">
                    @endif
                </div>
            </div>
            @if($action == 'edit')
                <div class="control-group">
                    <label class="control-label">视窗数据：</label>
                    <div class="controls">
                        <input type="text" name="viewport" value="{{$ms->viewport}}">
                    </div>
                </div>
            @endif
            <div class="control-group">
                <label class="control-label">实际宽度（mm）：</label>
                <div class="controls">
                    <input type="text" name="phy_width" class="span4" value="{{$action == 'edit'?$ms->phy_width:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">实际高度（mm）：</label>
                <div class="controls">
                    <input type="text" name="phy_height" class="span4" value="{{$action == 'edit'?$ms->phy_height:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">材料摆放方位：</label>
                <div class="controls">
                    @if($action == 'edit')
                        <label>
                            <input type="radio" name="position" value="0" {{$ms->position === 0 ? 'checked="checked"' : ""}}>左
                        </label>
                        <label>
                            <input type="radio" name="position" value="1" {{$ms->position === 1 ? 'checked="checked"' : ""}}>上
                        </label>
                        <label>
                            <input type="radio" name="position" value="2" {{$ms->position === 2 ? 'checked="checked"' : ""}}>右
                        </label>
                        <label>
                            <input type="radio" name="position" value="3" {{$ms->position === 3 ? 'checked="checked"' : ""}}>下
                        </label>
                        <label>
                            <input type="radio" name="position" value="4" {{$ms->position === 4 ? 'checked="checked"' : ""}}>中心
                        </label>
                    @else
                        <label><input type="radio" name="position" value="0">左</label>
                        <label><input type="radio" name="position" value="1">上</label>
                        <label><input type="radio" name="position" value="2">右</label>
                        <label><input type="radio" name="position" value="3">下</label>
                        <label><input type="radio" name="position" value="4">中心</label>
                    @endif
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">材料观测方位：</label>
                <div class="controls">
                    @if($action == 'edit')
                        <label>
                            <input type="radio" name="perspective" value="0" {{$ms->perspective === 0 ? 'checked="checked"' : ""}}>主视图
                        </label>
                        <label>
                            <input type="radio" name="perspective" value="1" {{$ms->perspective === 1 ? 'checked="checked"' : ""}}>左视图
                        </label>
                        <label>
                            <input type="radio" name="perspective" value="2" {{$ms->perspective === 2 ? 'checked="checked"' : ""}}>俯视图
                        </label>
                        <label>
                            <input type="radio" name="perspective" value="3" {{$ms->perspective === 3 ? 'checked="checked"' : ""}}>右视图
                        </label>
                        <label>
                            <input type="radio" name="perspective" value="4" {{$ms->perspective === 4 ? 'checked="checked"' : ""}}>仰视图
                        </label>
                        <label>
                            <input type="radio" name="perspective" value="5" {{$ms->perspective === 5 ? 'checked="checked"' : ""}}>后视图
                        </label>
                    @else
                        <label><input type="radio" name="perspective" value="0">主视图</label>
                        <label><input type="radio" name="perspective" value="1">左视图</label>
                        <label><input type="radio" name="perspective" value="2">俯视图</label>
                        <label><input type="radio" name="perspective" value="3">右视图</label>
                        <label><input type="radio" name="perspective" value="4">仰视图</label>
                        <label><input type="radio" name="perspective" value="5">后视图</label>
                    @endif
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-section">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var bezier;
    var form = $('#form-section');
    var action = '{{$action}}';
    var id = '{{is_null($ms)?null:$ms->id}}';
    $('#save-section').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        var material = $('#material').val();
        param = {};
        param.data = data;
        param.data.material = material;
        param.data.arr = bezier;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateMaterialSection/'+id, function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createMaterialSection', function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }
    });

    $('#getBezier').on('click',function(){
        var url = 'new_pro/draw/materialSection';
        bootstrapQ.dialog({
            type: 'get',
            url: url,
            title: '画图',
            foot:false,
            big:true
        });
    });
</script>
