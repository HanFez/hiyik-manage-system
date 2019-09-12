<?php

?>

@extends('layout/widget')

@section('icon')
    <i class="icon-search"></i>
@stop

@section('title')
    订单信息查询
@stop

@section('content')
    <form class="form-horizontal" id="search-form">
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>订单编号 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="订单编号" name="no" required />
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
                var no = values.no;
                var url = path + 'orders?no=' + no;
                $this.next('a').attr('href', url).text(no);
                loadingHide();
                $this.next('a')[0].click();
            } else {
                formNotCompleteNotice();
                loadingHide();
            }
        }
    </script>
@stop
