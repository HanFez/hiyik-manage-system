<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/24/16
 * Time: 2:20 PM
 */
use App\IekModel\Version1_0\Constants\Behavior;
$behaviors = Behavior::getConstants();
?>
@extends('layout.widget')
@section('icon')
    <i class="icon-filter"></i>
@stop
@section('title')
    定义权限
@stop
@section('content')
    <form class="form-horizontal">
        <div class="control-group">
            <label class="control-label">表名:</label>
            <div class="controls">
                @if(!is_null($result->data))
                <select id="tableName">
                    @foreach($result->data as $value)
                        <?php
                            $hasAll = true;
                            foreach ($behaviors as $behavior) {
                                if(!isset($value->{$behavior})) {
                                    $hasAll = false;
                                }
                            }
                        ?>
                        <option class="{{ $hasAll === true ? '' : 'alert-error'}}" value="{{ $value->tablename }}">{{ $value->tablename }}</option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">动作:</label>
            <div class="controls">
                @if(!is_null($result->data))
                    @foreach($result->data as $value)
                    <select multiple name="{{ $value->tablename }}" class="hide" >
                        @foreach ($behaviors as $behavior)
                            @if(!isset($value->{$behavior}))
                                <option value="{{ $behavior }}">{{ $behavior }}</option>
                            @else
                                <option value="{{ $behavior }}" selected>{{ $behavior }}</option>
                            @endif
                        @endforeach
                    </select>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="form-actions">
            <a id="save-privilege" type="submit" class="btn btn-success">保存</a>
        </div>
    </form>
    <script>
        $(document).ready(function () {
            $('#container select:not(#tableName)').eq(0).removeClass('hide').addClass('show');
            $('#tableName').on("change", function(e) {
                var tableName = e.val;
                $('#container select:not(#tableName)').prev().removeClass('show').addClass('hide');
                if(!isNull(tableName)) {
                    $('select[name="'+ tableName +'"]').prev().removeClass('hide').addClass('show');
                }
            })
            $('#save-privilege').on('click', function () {
                var tableName = $("#tableName").select2("val");
                var behaviors = $('#container select:not(#tableName)');
                var behavior = null;
                behaviors.each(function () {
                    var $this = $(this);
                    var prev = $this.prev();
                    if(prev.hasClass('show')) {
                        console.log($this.select2('val'))
                        behavior = $this.select2('val');
                    }
                })
                if(isNull(behavior) || behavior.length == 0) {
                    messageAlert({
                        'message': '请选择动作',
                        'type': 'error'
                    });
                } else {
                    var request = {};
                    request.data = {};
                    request.data.tableName = tableName;
                    request.data.behaviors = behavior;
                    ajaxData('post', 'privilege/assign', function (result) {
                        $('#container form').append(result);
                    }, [], request);
                }
            })
        })
    </script>
@stop
