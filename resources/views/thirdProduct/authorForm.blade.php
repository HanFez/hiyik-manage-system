<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/13/17
 * Time: 16:48 AM
 */

$result = isset($result) ? $result : null;
$isModify = false;
$data = null;
if(isset($result) && $result->isOk() && isset($result->data)) {
    $data = $result->data;
    $isModify = true;
}
?>

@extends('layout/widget')

@section('icon')
    <i class="{{ $isModify === true ? 'icon-pencil' : 'icon-plus' }}"></i>
@stop

@section('title')
    @if($isModify === true)
        修改
    @else
        添加
    @endif
    作者
    @include('layout/required')
@stop

@section('content')
    <form class="form-horizontal" id="author-form" authorId="{{ $data->id or '' }}">
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>编号 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="编号" name="no" required
                       value="{{ $data->no or '' }}"
                        {{ isset($data->no) ? 'disabled' : '' }}/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>姓名 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="姓名" name="name" required
                       value="{{ $data->name or '' }}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>姓名翻译 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="姓名翻译" name="lang" required
                       value="{{ $data->lang or '' }}" />
                <span class="help-block">翻译是翻译成中文的。若姓名是中文，请原样输入；若是英文，不知道翻译成什么，也请原样输入。</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">描述 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="描述" name="description"
                       value="{{ $data->description or '' }}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">国籍 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="国籍" name="nationality"
                       value="{{ $data->nationality or '' }}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">简介 :</label>
            <div class="controls">
                <textarea class="span11" rows="5" placeholder="简介" name="introduction">{{ $data->introduction or '' }}</textarea>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">名言 :</label>
            <div class="controls">
                <textarea class="span11" rows="5" placeholder="名言" name="saying">{{ $data->saying or '' }}</textarea>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">艺术特色 :</label>
            <div class="controls">
                <textarea class="span11" rows="5" placeholder="艺术特色" name="feature">{{ $data->feature or '' }}</textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-success" isModify="{{ $isModify }}">保存</button>
        </div>
        @if(!isset($isModify) || $isModify == false)
            @include('../layout/importExcel', ['type' => 'author', 'data' => \App\IekModel\Version1_0\Constants\ImportExcel::AUTHOR])
        @endif
    </form>
    <script>
        $(function() {
            $('#author-form').find('.btn[type="submit"]').on('click', saveAuthor);
        })
        function saveAuthor(event) {
            eventUtil.preventDefault(event);
            loadingShow();
            var $this = $(this);
            var $form = $this.closest('form');
            var isModify = $this.attr('isModify');
            var modify = false;
            var authorId = $form.attr('authorId');
            var method = 'post';
            var path = originPath();
            var url = path + 'authors';
            if(isModify == '1' && !isNull(authorId)) {
                method = 'put';
                url += '/' + authorId;
                modify = true;
            }
            removeInputMessage($form);
            var values = getFormValue($form);
            if(values != false) {
                var params = {
                    data: values,
                    isModify: modify,
                    $form: $form
                };
                ajaxData(method, url, handleSaveAuthor, [], params, saveError);
            } else {
                formNotCompleteNotice();
                loadingHide();
            }
        }
        function handleSaveAuthor(result, params) {
            var $form = params.$form;
            if(isOk(result)) {
                var isModify = params.isModify;
                var $form = params.$form;
                var data = result.data;
                if(!isNull(data) && isModify == true) {
                    var authorId = data.id;
                    $form.attr('authorId', authorId);
                }
                saveSuccess();
            } else if(result.statusCode == ERRORS.EXIST['code']) {
                $form.find('input[name="no"]').focus();
                setInputMessage($form.find('input[name="no"]'), 'error', '该编号已存在，请重新填写编号');
            } else if(result.statusCode == ERRORS.NOT_ALLOWED['code']) {
                $form.find('input[name="no"]').focus();
                setInputMessage($form.find('input[name="no"]'), 'error', '不允许修改编号');
            } else {
                saveError();
            }
            loadingHide();
        }
    </script>
@stop