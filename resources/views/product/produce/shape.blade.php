<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/27
 * Time: 14:56
 */
$action = isset($action) ? $action : null;
$shape = isset($shape)?$shape:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改形状"}}@else{{"添加形状"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-shape">
            <div class="control-group">
                <label class="control-label">形状名称：</label>
                <div class="controls">
                    <input type="text" name="name" class="span4" value="{{$action == 'edit'?$shape->name:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">形状描述：</label>
                <div class="controls">
                    <input type="text" name="des" class="span4" value="{{$action == 'edit'?$shape->description:''}}">
                </div>
            </div>
            @if($action == 'edit')
                <div class="control-group">
                    <label class="control-label">形状XML数据：</label>
                    <div class="controls">
                        <textarea name="shape" rows="10" cols="20">{{$shape->shape}}</textarea>
                    </div>
                </div>
            @endif
            <div class="control-group">
                <label class="control-label">{{$action == 'edit'?"贝塞尔点：":"添加形状："}}</label>
                <div class="controls">
                    @if($action == 'edit')
                        <textarea name="bezier" id="" cols="30" rows="10">{{$shape->bezier}}</textarea>
                    @else
                        <input type="button" id="getBezier" class="span2" value="获取图形数据">
                    @endif
                </div>
            </div>
            @if($action == 'edit')
                <div class="control-group">
                    <label class="control-label">视窗数据：</label>
                    <div class="controls">
                        <input type="text" name="viewport" value="{{$shape->viewport}}">
                    </div>
                </div>
            @endif
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-shape">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var bezier;
    var form = $('#form-shape');
    var action = '{{$action}}';
    var id = '{{is_null($shape) ? NULL : $shape->id}}';
    $('#save-shape').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        param = {};
        param.data = data;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateShape/'+id, function (result) {
                if(result) {
                    form.append(result);
                }
            }, [],param);
        }else{
            param.data.arr = bezier;
            ajaxData('post', 'new_pro/createShape', function (result) {
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