<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/13/17
 * Time: 16:48 AM
 */

$result = isset($result) ? $result : null;
$data = null;
$isModify = false;
if(isset($result) && $result->isOk() && isset($result->data)) {
    $result = json_decode(json_encode($result));
    $data = $result->data;
    $isModify = true;
}
//dd($result);
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
    介绍
    @include('layout/required')
@stop

@section('content')
    @include('thirdProduct.introductionForm', [
        'data' => $data,
        'isProductIntro' => false
    ])
    <link rel="stylesheet" href="css/tb-product.css">
    <script type="text/javascript" src="js/jquery.sortable.js"></script>
    <script type="text/javascript" src="js/addProduct.js"></script>
    <script>
        $(function () {
            var $widget = $('#container');
            bindIntroductionBoxButtonEvent($widget);
            //save product.
            $widget.find('.btn[type="submit"]').on('click', saveIntroduction);
            //add textarea.
            $widget.find('.add-text').on('click', function() {
                var $this = $(this);
                var $group = $this.closest('.control-group');
                var $box = createIntroductionBox('text');
                $group.before($box);
                bindIntroductionBoxButtonEvent($box);
                $box.find('textarea').focus();
            })
            //add image.
            $widget.find('.add-image input[type="file"]').on('change', uploadIntroductionImageEvent);
        })
    </script>
@stop