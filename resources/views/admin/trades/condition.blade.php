<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/10/30
 * Time: 17:29
 */
$today = date("Y-m-d H:i:s", mktime(0,0,0,date('m'),date('d'),date('Y')));
$weekday = date("Y-m-d H:i:s", mktime(0,0,0,date('m'),date('d')-7,date('Y')));
$monday = date("Y-m-d H:i:s", mktime(0,0,0,date('m'),date('d')-30,date('Y')));
?>
<style>
    .btn{
        margin-right: 10px;
    }
    .btn.active{
        background: #afdd22;
        color: white;
    }
</style>
<div class="control-group">
    <label for="checkboxes" class="control-label">支付时间</label>
    <div class="controls">
        <div data-toggle="buttons-radio" name="time" class="btn-group">
            <button class="btn" type="button" data="{{$monday}}">近30天</button>
            <button class="btn" type="button" data="{{$weekday}}">近7天</button>
            <button class="btn" type="button" data="{{$today}}">今天</button>
        </div>
    </div>
    <div class="controls">
        自己选择时间
        <div data-date="" class="input-append date datepicker" data-date-format="yyyy-mm-dd">
            <input type="text" id="start-time" name="startTime">
            <span class="add-on"><i class="icon-th"></i></span>
        </div>
        <div data-date="" class="input-append date datepicker" data-date-format="yyyy-mm-dd">
            <input type="text" id="end-time" name="endTime">
            <span class="add-on"><i class="icon-th"></i></span>
        </div>
    </div>
</div>
