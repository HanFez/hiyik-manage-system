<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/26
 * Time: 11:57
 */
$action = isset($action) ? $action : null;
$mcs = isset($mcs) ? $mcs : null;
$mc = isset($mc)?$mc:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改材料分类"}}@else{{"添加材料分类"}}@endif</h5>
</div>
<div class="widget-content nopadding">
    <form class="form-horizontal" id="form-MC">
        <div class="control-group">
            <label class="control-label">分类名称：</label>
            <div class="controls">
                <input type="text" name="name" class="span4" value="{{$action == 'edit'?$mc->name:''}}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">分类描述：</label>
            <div class="controls">
                <input type="text" name="des" class="span4" value="{{$action == 'edit'?$mc->description:''}}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">分类等级：</label>
            <div class="controls">
                <input type="text" data-type="int" name="level" value="{{$action == 'edit'?$mc->level:0}}" disabled="disabled" class="span4">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">分类所属：</label>
            <div class="controls">
                <select name="parent_id" id="pid" style="width: 220px;">
                    @if($action == 'edit')
                        <option value="null">No Parent</option>
                        @foreach($mcs as $v)
                            <option value="{{$v->id}}" data-level="{{$v->level}}"
                            {{$mc->parent_id == $v->id ? 'selected="selected"':''}}>
                                {{$v->name}}
                            </option>
                        @endforeach
                    @elseif(!is_null($mcs))
                        <option value="null">No Parent</option>
                        @foreach($mcs as $v)
                            <option value="{{$v->id}}" data-level="{{$v->level}}">{{$v->name}}</option>
                        @endforeach
                    @else
                        <option value="null">{{"请先添加数据"}}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="form-actions">
            <a type="submit" class="btn btn-success" id="save-MC">保存</a>
        </div>
    </form>
</div>
</div>
<script>
    var form = $('#form-MC');
    var action = '{{$action}}';
    var id = '{{is_null($mc) ? NULL : $mc->id}}';
    $('#pid').on('change',function(){
        var level = $('#pid').find("option:selected").attr('data-level');
        if(level === undefined){
            $('input[name="level"]').attr('value',0);
        }else{
            $('input[name="level"]').attr('value',Number(level)+1);
        }
    });
    $('#save-MC').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        var pid = $('#pid').val();
        param = {};
        param.data = data;
        param.data.pid = pid;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateMCategory/'+id, function (result) {
                if(result) {
                    $('#form-MC').append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createMCategory', function (result) {
                if(result) {
                    $('#form-MC').append(result);
                }
            }, [],param);
        }
    });
</script>