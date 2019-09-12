<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/25/16
 * Time: 11:25 AM
 */
use App\IekModel\Version1_0\Constants\Behavior;
use App\IekModel\Version1_0\IekModel;
$behaviors = Behavior::getConstants();
?>
@extends('layout.widget')
@section('icon')
    <i class="icon-filter"></i>
@stop
@section('title')
    给角色分配权限
@stop
@section('content')
    <form class="form-horizontal">
        <div class="control-group">
            <label class="control-label">角色:</label>
            <div class="controls">
                @if(!is_null($result->data))
                    <select id="roleName">
                        @foreach($result->data as $value)
                            <option value="{{ $value->id }}">{{ IekModel::strTrans($value->name , 'role')}}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">表名:</label>
            <div class="controls">
                @if(!is_null($result->tables))
                    <select id="tableName">
                        @foreach($result->tables as $value)
                            <option value="{{ $value->tablename }}" name="{{ $value->tablename }}">{{ $value->tablename }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">动作:</label>
            <div class="controls">
                @if(!is_null($result->data))
                    <select id="behavior" multiple >
                        @foreach ($behaviors as $behavior)
                            <option name="{{ $behavior }}" value="{{ $behavior }}">{{ $behavior }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <div class="form-actions">
            <a id="save-role-privilege" type="submit" class="btn btn-success">保存</a>
        </div>
    </form>
    <script>
        $(document).ready(function () {
            var defaultId = $('#roleName option').eq(0).attr('value').trim();
            getRoleTableNames(defaultId);
            var defaultTableName = $('#tableName option').eq(0).attr('value').trim();
            getRoleTableBehavior(defaultId, defaultTableName);

            $('#roleName').on("change", function(e) {
                var roleId = e.val;
                getRoleTableNames(roleId);
            })
            $('#tableName').on("change", function(e) {
                var tableName = e.val;
                var roleId = $('#roleName').select2('val');
                getRoleTableBehavior(roleId, tableName);
            })
            $('#save-role-privilege').on('click', function () {
                var roleId = $("#roleName").select2("val");
                var tableName = $("#tableName").select2("val");
                var behaviors = $('#behavior').select2("val");
                var request = {};
                request.data = {};
                request.data.tableName = tableName;
                request.data.behaviors = behaviors;
                ajaxData('post', 'allotPrivilege/' + roleId, function (result) {
                    if(!isNull(result)) {
                        $('#container form').append(result);
                    }
                }, [], request);
            })
        })
        function getRoleTableNames(roleId) {
            if(!isNull(roleId)) {
                ajaxData('get', 'getRoleTable/' + roleId, function (result) {
                    if (!isNull(result)) {
                        $('#tableName option.alert-success').removeClass('alert-success');
                        for (var i in result) {
                            $('#tableName option[name="' + result[i] + '"]').addClass('alert-success');
                        }
                        var defaultTableName = $('#tableName option').eq(0).attr('value').trim();
                        getRoleTableBehavior(roleId, defaultTableName);
                    }
                })
            }
        }
        function getRoleTableBehavior(roleId, tableName) {
            if(!isNull(roleId) && !isNull(tableName)) {
                ajaxData('get', 'getRoleTablePrivilege/' + roleId + '/' + tableName, function (result) {
                    if (!isNull(result)) {
                        $('#behavior option').removeAttr('selected');
                        for (var i in result) {
                            $('#behavior option[name="' + result[i].privilege.behavior + '"]').attr('selected', 'selected');
                        }
                        $('#behavior').select2();
                    }
                })
            }
        }
    </script>
@stop
