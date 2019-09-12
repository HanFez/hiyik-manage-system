<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/25/16
 * Time: 5:06 PM
 */
$type = isset($err->type) ? $err->type : null;
$employees = $err->data;

$transAdmin = trans('admin');
?>
@extends('layout.widget')
@section('icon')
    @if($type === 'add')
        <i class="icon-plus"></i>
    @else
        <i class="icon-pencil"></i>
    @endif
@stop
@section('title')
    {{ $transAdmin[$type] or $type }}管理员
@stop
@section('content')
    <form class="form-horizontal">
        @if(isset($employees) && !is_null($employees))
            <div class="control-group">
                <label class="control-label">员工:</label>
                <div class="controls">
                    @if(isset($employees) && !is_null($employees))
                        <select id="employees">
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->id }}-{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        @else
            <input type="hidden" id="edit-id" value="{{$err->id}}">
        @endif
        <div class="control-group">
            <label class="control-label">密码:</label>
            <div class="controls">
                <input type="password" name="password">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">确认密码:</label>
            <div class="controls">
                <input type="password" name="confirmPassword">
            </div>
        </div>
        <div class="form-actions">
            <a id="add-manager" type="submit" class="btn btn-success">{{ $transAdmin[$type] or $type }}</a>
        </div>
    </form>
    <script>
        $(document).ready(function () {
            $('#add-manager').on('click', function () {
                var type = "{{$type}}";
                if(type == 'add'){
                    var employeeId = $('#employees').select2('val');
                }else{
                    employeeId = $('#edit-id').val();
                }
                var password = $('#container input[name="password"]').val();
                var confirmPassword = $('#container input[name="confirmPassword"]').val();
                var request = {};
                request.data = {};
                request.data.password = password;
                request.data.confirmPassword = confirmPassword;
                request.data.id = employeeId;
                if(type == 'add'){
                    ajaxData('post', 'manager', function (result) {
                        if(!isNull(result)) {
                            removeInputMessage($('#container form'));
                            $('#container form').append(result);
                            //ajaxData('get','manager',appendViewToContainer);
                        }
                    }, [], request);
                }else{
                    ajaxData('put', 'manager/'+employeeId, function (result) {
                        if(!isNull(result)) {
                            removeInputMessage($('#container form'));
                            $('#container form').append(result);
                            //ajaxData('get','manager',appendViewToContainer);
                        }
                    }, [], request);
                }

            })
        })
    </script>
@stop
