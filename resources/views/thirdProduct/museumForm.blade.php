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
    珍藏处
    @include('layout/required')
@stop

@section('content')
    <form class="form-horizontal" id="museum-form" museumId="{{ $data->id or '' }}">
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>名字 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="名字" name="name" required
                       value="{{ $data->name or '' }}"  />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>名字翻译 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="名字翻译" name="lang" required
                       value="{{ $data->lang or '' }}"  />
                <span class="help-block">翻译是翻译成中文的。若名字是中文，请原样输入；若是英文，不知道翻译成什么，也请原样输入。</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">描述 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="描述" name="description"
                       value="{{ $data->description or '' }}"  />
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-success" isModify="{{ $isModify }}">保存</button>
        </div>
        @if(!isset($isModify) || $isModify == false)
            @include('../layout/importExcel', ['type' => 'museum', 'data' => \App\IekModel\Version1_0\Constants\ImportExcel::MUSEUM])
        @endif
    </form>
    <script>
        $(function() {
            $('#museum-form').find('.btn[type="submit"]').on('click', saveMuseum);
        })
        function saveMuseum(event) {
            eventUtil.preventDefault(event);
            loadingShow();
            var $this = $(this);
            var $form = $this.closest('form');
            var isModify = $this.attr('isModify');
            var modify = false;
            var museumId = $form.attr('museumId');
            var method = 'post';
            var path = originPath();
            var url = path + 'museums';
            if(isModify == '1' && !isNull(museumId)) {
                method = 'put';
                url += '/' + museumId;
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
                ajaxData(method, url, handleSaveMuseum, [], params, saveError);
            } else {
                formNotCompleteNotice();
                loadingHide();
            }
        }
        function handleSaveMuseum(result, params) {
            if(isOk(result)) {
                var isModify = params.isModify;
                var $form = params.$form;
                var data = result.data;
                if(!isNull(data) && isModify == true) {
                    var museumId = data.id;
                    $form.attr('museumId', museumId);
                }
                saveSuccess();
            } else {
                saveError();
            }
            loadingHide()
        }
    </script>
@stop