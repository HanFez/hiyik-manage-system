<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 15:51
 */
$action = isset($action) ? $action : null;
$hcs = isset($hcs)?$hcs:null;
$hc = isset($hc)?$hc:null;
?>

<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改工艺分类"}}@else{{"添加工艺分类"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-HC">
            <div class="control-group">
                <label class="control-label">分类名称：</label>
                <div class="controls">
                    <input type="text" name="name" class="span4" value="{{$action == 'edit'?$hc->name:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">分类描述：</label>
                <div class="controls">
                    <input type="text" name="des" class="span4" value="{{$action == 'edit'?$hc->description:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">分类等级：</label>
                <div class="controls">
                    <input type="text" data-type="int" name="level" class="span4" disabled="disabled" value="{{$action == 'edit'?$hc->level:0}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">分类所属：</label>
                <div class="controls">
                    <select name="parent_id" id="pid" style="width: 220px;">
                        @if($action == 'edit')
                            <option value="null">No Parent</option>
                            @foreach($hcs as $v)
                                <option value="{{$v->id}}" data-level="{{$v->level}}"
                                {{$hc->parent_id == $v->id ? 'selected="selected"':''}}>
                                    {{$v->name}}
                                </option>
                            @endforeach
                        @elseif(!is_null($hcs))
                            <option value="null">No Parent</option>
                            @foreach($hcs as $v)
                                <option value="{{$v->id}}" data-level="{{$v->level}}">{{$v->name}}</option>
                            @endforeach
                        @else
                            <option value="null">{{"请先添加数据"}}</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-HC">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-HC');
    var action = '{{$action}}';
    var id = '{{is_null($hc) ? NULL : $hc->id}}';
    $('#pid').on('change',function(){
        var level = $('#pid').find("option:selected").attr('data-level');
        if(level === undefined){
            $('input[name="level"]').attr('value',0);
        }else{
            $('input[name="level"]').attr('value',Number(level)+1);
        }
    });
    $('#save-HC').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        var pid = $('#pid').val();
        param = {};
        param.data = data;
        param.data.pid = pid;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateHCategory/'+id, function (result) {
                if(result) {
                    $('#form-HC').append(result);
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createHCategory', function (result) {
                if(result) {
                    $('#form-HC').append(result);
                }
            }, [],param);
        }
    });
</script>

