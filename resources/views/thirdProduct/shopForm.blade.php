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
    店铺
    @include('layout/required')
@stop

@section('content')
    <form class="form-horizontal" id="shop-form" shopId="{{ $data->id or '' }}">
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>名字 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="名字" name="name" required
                       value="{{ $data->name or '' }}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">描述 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="描述" name="description"
                       value="{{ $data->description or '' }}"  />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>店铺地址 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="店铺地址" name="uri" required
                       value="{{ $data->uri or '' }}"  />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>三方平台 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="三方平台" name="platform" required
                       value="{{ $data->platform or '淘宝' }}" />
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-success" isModify="{{ $isModify }}">保存</button>
        </div>
    </form>
    <script>
        $(function() {
            $('#shop-form').find('.btn[type="submit"]').on('click', saveShop);
        })
        function saveShop(event) {
            eventUtil.preventDefault(event);
            loadingShow();
            var $this = $(this);
            var $form = $this.closest('form');
            var isModify = $this.attr('isModify');
            var modify = false;
            var shopId = $form.attr('shopId');
            var method = 'post';
            var path = originPath();
            var url = path + 'shops';
            if(isModify == '1' && !isNull(shopId)) {
                method = 'put';
                url += '/' + shopId;
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
                ajaxData(method, url, handleSaveShop, [], params, saveError);
            } else {
                formNotCompleteNotice();
                loadingHide();
            }
        }
        function handleSaveShop(result, params) {
            if(isOk(result)) {
                var isModify = params.isModify;
                var $form = params.$form;
                var data = result.data;
                if(!isNull(data) && isModify == true) {
                    var shopId = data.id;
                    $form.attr('shopId', shopId);
                }
                saveSuccess();
            } else {
                saveError();
            }
            loadingHide();
        }
    </script>
@stop