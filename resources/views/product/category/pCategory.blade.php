<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/26
 * Time: 12:12
 */
$action = isset($action) ? $action : null;
$pcs = isset($pcs) ? $pcs : null;
$pc = isset($pc)?$pc:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改产品分类"}}@else{{"添加产品分类"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-PC">
            <div class="control-group">
                <label class="control-label">分类名称：</label>
                <div class="controls">
                    <input type="text" name="name" class="span4" value="{{$action == 'edit'?$pc->name:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">分类描述：</label>
                <div class="controls">
                    <input type="text" name="des" class="span4" value="{{$action == 'edit'?$pc->description:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">分类等级：</label>
                <div class="controls">
                    <input type="text" data-type="int" name="level" disabled="disabled" value="{{$action == 'edit'?$pc->level:0}}" class="span4">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">分类所属：</label>
                <div class="controls">
                    <select name="parent_id" id="pid" style="width: 220px;">
                        @if($action == 'edit')
                            <option value="null">No Parent</option>
                            @foreach($pcs as $v)
                                <option value="{{$v->id}}" data-level="{{$v->level}}"
                                {{$pc->parent_id == $v->id ? 'selected="selected"':''}}>
                                    {{$v->name}}
                                </option>
                            @endforeach
                        @elseif(!is_null($pcs))
                            <option value="null">No Parent</option>
                            @foreach($pcs as $v)
                                <option value="{{$v->id}}" data-level="{{$v->level}}">{{$v->name}}</option>
                            @endforeach
                        @else
                            <option value="null">{{"请先添加数据"}}</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-PC">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-PC');
    var action = '{{$action}}';
    var id = '{{is_null($pc) ? NULL : $pc->id}}';
    $('#pid').on('change',function(){
        var level = $('#pid').find("option:selected").attr('data-level');
        if(level === undefined){
            $('input[name="level"]').attr('value',0);
        }else{
            $('input[name="level"]').attr('value',Number(level)+1);
        }
    });
    $('#save-PC').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        var pid = $('#pid').val();
        param = {};
        param.data = data;
        param.data.pid = pid;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updatePCategory/'+id, function (result) {
                if(result) {
                    $('#form-PC').append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createPCategory', function (result) {
                if(result) {
                    $('#form-PC').append(result);
                }
            }, [],param);
        }
    });
</script>