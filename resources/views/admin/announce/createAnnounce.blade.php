<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/11/16
 * Time: 5:18 PM
 */
$transAdmin = trans('admin');
$type = 'edit';
if(!isset($result) || is_null($result)) {
    $type = 'add';
}
?>
@extends('layout/widget')

@section('icon')
    <i class="{{ $type == 'edit' ? 'icon-pencil' : 'icon-plus'}}"></i>
@stop

@section('title')
    {{ $transAdmin[$type] or $type }}
    @include('layout/required')
@stop

@section('content')
    <form class="form-horizontal" id="announceForm">
        <div class="control-group">
            <label class="control-label">
                <span class="text-important">*</span>
                标题 :
            </label>
            <div class="controls">
                <input type="text" class="span5" placeholder="title" name="title" required="required" value="{{ $result->data->title or '' }}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">
                <span class="text-important">*</span>
                开始时间 :
            </label>
            <div class="controls">
                <input class="span5 datepicker" placeholder="startTime" required="required" name="begin_at" data-date-format="yyyy-mm-dd" data-date="2016-10-28" type="date" value="{{$result->data->begin_at or ''}}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">
                <span class="text-important">*</span>
                结束时间 :
            </label>
            <div class="controls">
                <input class="span5 datepicker" placeholder="endTime" required="required" name="end_at" data-date-format="yyyy-mm-dd" data-date="2016-10-28" type="date" value="{{$result->data->end_at or ''}}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">
                备注 :
            </label>
            <div class="controls">
                <input type="text" class="span5" placeholder="memo" name="memo" value="{{$result->data->memo or ''}}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">
                <span class="text-important">*</span>
                公告内容 :
            </label>
            <div class="controls">
                <div id="editor">

                </div>
            </div>
        </div>
        <div class="form-actions">
            @if($type == 'add')
                <a type="button" id="announceSubmit" url="createAnnounce" class="btn btn-success">{{ $transAdmin[$type] or $type }}</a>
            @else
                <a type="button" id="announceSubmit" url="modifyAnnounce/{{$result->data->id}}" class="btn btn-success">{{ $transAdmin[$type] or $type }}</a>
            @endif
        </div>
    </form>
    <script>
        initSample();
        $('#announceSubmit').click(function(){
            var val = CKEDITOR.instances.editor.getData();
            var data = getFormValue($('#announceForm'));
            if(data == false || isNull(val)){
                return ;
            }
            data.content = val;
            var params = {};
            params.data = data;
            ajaxData('post',$(this).attr('url'),callback,'',params);
        });

        function callback(data) {
            $('body').append(data);
        }
    </script>
    @if(isset($result->data->content))
        <script>
            var content = '<?php echo str_replace("\n",'<br>',str_replace("\r",'<br>',str_replace("\r\n",'<br>',$result->data->content)))?>';
            console.log(content);
            CKEDITOR.instances.editor.setData(content);
        </script>
    @endif
@stop