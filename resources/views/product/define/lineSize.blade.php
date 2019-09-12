<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/7
 * Time: 11:02
 */
$action = isset($action) ? $action : null;
$materials = isset($materials)?$materials:null;
$lineSize = isset($lineSize)?$lineSize:null;
?>

<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改线条尺寸"}}@else{{"添加线条尺寸"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-line-size">
            <div class="control-group">
                <label class="control-label">材料：</label>
                <div class="controls">
                    <select name="material" id="material-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($materials as $material)
                                <option value="{{$material->id}}" {{$material->id == $lineSize->material_id ? 'selected="selected"':''}}>
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
                <label class="control-label">最大物理长度（mm）：</label>
                <div class="controls">
                    <input type="text" name="phyLengthMax" class="span4" value="{{$action == 'edit'?$lineSize->phy_length_max:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">最小物理长度（mm）：</label>
                <div class="controls">
                    <input type="text" name="phyLengthMin" class="span4" value="{{$action == 'edit'?$lineSize->phy_length_min:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">可选物理长度（mm）：</label>
                <div class="controls">
                    <input type="text" name="phyLength" class="span4" value="{{$action == 'edit'?$lineSize->phy_length:''}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-line-size">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-line-size');
    var action = '{{$action}}';
    var id = '{{is_null($lineSize) ? NULL : $lineSize->id}}';
    $('#save-line-size').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        var materialId = $('#material-id').val();
        var param = {};
        param.data = {};
        param.data = data;
        param.data.materialId = materialId;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateLineSize/'+id, function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createLineSize', function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }
    });
</script>
