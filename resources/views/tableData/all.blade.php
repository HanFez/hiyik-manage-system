<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/26/16
 * Time: 10:22 AM
 */
use App\IekModel\Version1_0\IekModel;

$tableNames = IekModel::getTables();
?>

@extends('layout.widget')
@section('icon')
    <i class="icon-th"></i>
@stop
@section('title')
    表数据
@stop
@section('content')
    <form class="form-horizontal">
        <div class="control-group">
            <label class="control-label">表名:</label>
            <div class="controls">
                @if(!is_null($tableNames))
                    <select id="tableNames">
                        @foreach($tableNames as $value)
                            <option value="{{ $value->tablename }}" name="{{ $value->tablename }}">{{ $value->tablename }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <div id="tablesData" style="padding: 20px 20px 0">

        </div>
    </form>
    <script>
        $(document).ready(function () {
            var tableName = $('#tableNames option').eq(0).attr('value').trim();
            getTableDataList(tableName);

            $('#tableNames').on("change", function(e) {
                var tableName = e.val;
                getTableDataList(tableName);
            })
        })
        function getTableDataList(tableName) {
            if(!isNull(tableName)) {
                ajaxData('get', 'getAll/' + tableName, function (result) {
                    $('#tablesData').html(result);
                    initPageElement('tablesData');
                })
            }
        }
    </script>
@stop