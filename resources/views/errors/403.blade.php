<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/29/16
 * Time: 3:50 PM
 */
?>
@extends('layout/widget')

@section('icon')
    <i class="icon-info-sign"></i>
@stop

@section('title')
    Error 403
@stop

@section('content')
    <div class="error_ex">
        <h1>403</h1>
        <h3>Opps, You're lost.</h3>
        <p>Access to this page is forbidden</p>
        <a class="btn btn-warning btn-big"  href="/">Back to Home</a> </div>
    </div>
@stop
