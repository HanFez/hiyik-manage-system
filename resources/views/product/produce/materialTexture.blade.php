<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/5
 * Time: 15:32
 */
$action = isset($action) ? $action : null;
$materials = isset($materials)?$materials:null;
$textures = isset($textures)?$textures:null;
$mt = isset($mt)?$mt:null;
$path = \App\IekModel\Version1_0\Constants\Path::FILE_PATH;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改材料纹理"}}@else{{"添加材料纹理"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-mat-texture">
            <div class="control-group">
                <label class="control-label">材料：</label>
                <div class="controls">
                    <select name="material" id="material" style="width:220px;">
                        @if(!$materials->isEmpty())
                            @foreach($materials as $material)
                                @if($action == 'edit')
                                    <option value="{{$material->id}}" {{$material->id == $mt->material_id ? 'selected':''}}>
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
            <div id="textures" class="control-group thumbnail-box">
                <label class="control-label">纹理：</label>
                <div class="controls">
                    @if(!$textures->isEmpty())
                        @if($action == 'edit')
                            @foreach($textures as $texture)
                                <div class="thumbnail {{$texture->id == $mt->texture_id ? 'active' : ''}}"
                                     title="{{$texture->file_name}}">
                                    <img data="{{$texture->id}}" src="{{$path.$texture->uri}}" style="height: 80px;width: 80px;">
                                    <div class="caption">{{$texture->file_name}}</div>
                                </div>
                            @endforeach
                        @else
                            @foreach($textures as $texture)
                                <div class="thumbnail {{$action == 'edit' ? 'active' : ''}}"
                                     title="{{$texture->file_name}}">
                                    <img data="{{$texture->id}}" src="{{$path.$texture->uri}}" style="height: 80px;">
                                    <div class="caption">{{$texture->file_name}}</div>
                                </div>

                            @endforeach
                        @endif
                    @else
                        <code>{{"请先添加纹理"}}</code>
                    @endif
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">材料摆放方位：</label>
                <div class="controls">
                    @if($action == 'edit')
                        <label>
                            <input type="radio" name="position" value="0" {{$mt->position === 0 ? 'checked="checked"' : ""}}>左
                        </label>
                        <label>
                            <input type="radio" name="position" value="1" {{$mt->position === 1 ? 'checked="checked"' : ""}}>上
                        </label>
                        <label>
                            <input type="radio" name="position" value="2" {{$mt->position === 2 ? 'checked="checked"' : ""}}>右
                        </label>
                        <label>
                            <input type="radio" name="position" value="3" {{$mt->position === 3 ? 'checked="checked"' : ""}}>下
                        </label>
                        <label>
                            <input type="radio" name="position" value="4" {{$mt->position === 4 ? 'checked="checked"' : ""}}>中心
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
                            <input type="radio" name="perspective" value="0" {{$mt->perspective === 0 ? 'checked="checked"' : ""}}>主视图
                        </label>
                        <label>
                            <input type="radio" name="perspective" value="1" {{$mt->perspective === 1 ? 'checked="checked"' : ""}}>左视图
                        </label>
                        <label>
                            <input type="radio" name="perspective" value="2" {{$mt->perspective === 2 ? 'checked="checked"' : ""}}>俯视图
                        </label>
                        <label>
                            <input type="radio" name="perspective" value="3" {{$mt->perspective === 3 ? 'checked="checked"' : ""}}>右视图
                        </label>
                        <label>
                            <input type="radio" name="perspective" value="4" {{$mt->perspective === 4 ? 'checked="checked"' : ""}}>仰视图
                        </label>
                        <label>
                            <input type="radio" name="perspective" value="5" {{$mt->perspective === 5 ? 'checked="checked"' : ""}}>后视图
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
            <div class="control-group">
                <label class="control-label">是否为局部图：</label>
                <div class="controls">
                    @if($action == 'edit')
                        <label>
                            <input type="radio" name="partion" value="0" {{$mt->partion === 0 ? 'checked="checked"' : ""}}>整体图
                        </label>
                        <label>
                            <input type="radio" name="partion" value="1" {{$mt->partion === 1 ? 'checked="checked"' : ""}}>局部图
                        </label>
                    @else
                        <label><input type="radio" name="partion" value="0">整体图</label>
                        <label><input type="radio" name="partion" value="1">局部图</label>
                    @endif
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-mat-texture">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-mat-texture');
    $('#textures .thumbnail').on('click', function () {//选中当前图片
        $('#textures .thumbnail').removeClass('active');
        $(this).addClass('active');
    });
    var action = '{{$action}}';
    var id = '{{is_null($mt) ? '': $mt->id}}';
    $('#save-mat-texture').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        var material = $('#material').select2('val');
        var texture = $("#textures .thumbnail.active img").attr('data');
        param = {};
        param.data = data;
        param.data.material = material;
        param.data.texture = texture;
        //console.log(param);
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateMaterialTexture/'+id, function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createMaterialTexture', function (result) {
                if(result) {
                    form.append(result);
                    //$('#form-mat-texture input').val('');
                }
            }, [],param);
        }
    });
</script>
