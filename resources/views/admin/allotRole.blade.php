<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/25/16
 * Time: 4:11 PM
 */
use App\IekModel\Version1_0\IekModel;
?>
@extends('layout.widget')
@section('icon')
    <i class="icon-filter"></i>
@stop
@section('title')
    给管理员分配角色
@stop
@section('content')
    <form class="form-horizontal">
        <div class="control-group">
            <label class="control-label">管理员:</label>
            <div class="controls">
                @if(!is_null($result->manager))
                    <select id="managers">
                        @foreach($result->manager as $manager)
                            @if(is_null($manager->employee))
                                <option value="{{ $manager->id }}">{{ $manager->id }}</option>
                            @else
                                <option value="{{ $manager->id }}">{{ $manager->id }}-{{ $manager->employee->name  }}</option>
                            @endif
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">角色:</label>
            <div class="controls">
                @if(!is_null($result->roles))
                    <select id="roleNames" multiple >
                        @foreach ($result->roles as $role)
                            <option name="{{ $role->name }}" value="{{ $role->id }}">{{ IekModel::strTrans($role->name , 'role') }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <div class="form-actions">
            <a id="save-manager-role" type="submit" class="btn btn-success">保存</a>
        </div>
    </form>
    <script>
        $(document).ready(function () {
            var defaultId = $('#managers option').eq(0).attr('value').trim();
            getManagerRoles(defaultId);

            $('#managers').on("change", function(e) {
                var managerId = e.val;
                getManagerRoles(managerId);
            })
            $('#save-manager-role').on('click', function () {
                var managerId = $("#managers").select2("val");
                var roleNames = $("#roleNames").select2("val");
                var request = {};
                request.data = {};
                request.data.roles = roleNames;
                ajaxData('post', 'allotRole/' + managerId, function (result) {
                    if(!isNull(result)) {
                        $('#container form').append(result);
                    }
                }, [], request);
            })
        })
        function getManagerRoles(managerId) {
            if(!isNull(managerId)) {
                ajaxData('get', 'getManagerRoles/' + managerId, function (result) {
                    if (!isNull(result) && result.statusCode == 0) {
                        $('#roleNames option').removeAttr('selected');
                        var roles = result.data;
                        for (var i in roles) {
                            $('#roleNames option[name="' + roles[i].roles.name + '"]').attr('selected', 'selected');
                        }
                        $('#roleNames').select2();
                    }
                })
            }
        }
    </script>
@stop
