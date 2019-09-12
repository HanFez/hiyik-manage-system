<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/20/16
 * Time: 3:04 PM
 */
$transAdmin = trans('admin');
?>
@extends('layout/widget')

@section('icon')
    <i class="icon-pencil"></i>
@stop

@section('title')
    {{ $transAdmin['edit'] or 'edit' }}
    @include('layout/required')
@stop

@section('content')
    <form id="form-password" class="form-horizontal">
        <div class="control-group">
            <label class="control-label">
                <span class="text-important">*</span>
                旧密码 :
            </label>
            <div class="controls">
                <input type="password" class="span5" placeholder="旧密码" name="oldPassword" required="required" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">
                <span class="text-important">*</span>
                新密码 :
            </label>
            <div class="controls">
                <input type="password" class="span5" placeholder="新密码" name="newPassword" required="required" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">
                <span class="text-important">*</span>
                确认密码 :
            </label>
            <div class="controls">
                <input type="password" class="span5" placeholder="确认密码" name="confirmPassword" required="required" value="">
            </div>
        </div>
        <div class="form-actions">
            <a id="modifyPassword" type="submit" class="btn btn-success">{{ $transAdmin['edit'] or 'edit' }}</a>
        </div>
    </form>
    <script>
        $('#modifyPassword').on('click', function () {
            var form = $('#form-password');
            removeInputMessage(form);
            var values = getFormValue(form);
            if(values != false) {
                var request = {};
                request.data = values;
//                console.log(request);
                ajaxData('post', 'manager/password', function (result) {
                    $('#container').append(result);
                }, [], request);
            }
        })
    </script>
@stop