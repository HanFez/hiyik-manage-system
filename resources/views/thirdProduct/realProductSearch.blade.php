<?php

?>

@extends('layout/widget')

@section('icon')
    <i class="icon-search"></i>
@stop

@section('title')
    产品信息查询
@stop

@section('content')
    <form class="form-horizontal" id="search-form">
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>用户见到的唯一编号 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="唯一编号" name="userNo" required />
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-success">查询</button>
            <a href="" target="_blank" style="display: none"></a>
        </div>
    </form>
    <script>
        $(function() {
            $('#search-form').find('.btn[type="submit"]').on('click', searchInfo);
        })
        function searchInfo(event) {
            var $this = $(this);
            eventUtil.preventDefault(event);
            loadingShow();
            var $this = $(this);
            var $form = $this.closest('form');
            var values = getFormValue($form);
            var path = originPath();
            if(values != false) {
                var userNo = values.userNo;
                var url = path + 'realProducts?userNo=' + userNo;
                $this.next('a').attr('href', url).text(userNo);
                loadingHide();
                $this.next('a')[0].click();
            } else {
                formNotCompleteNotice();
                loadingHide();
            }
        }
    </script>
@stop
