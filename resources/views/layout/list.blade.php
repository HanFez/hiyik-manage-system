<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/8/16
 * Time: 10:53 AM
 */
?>
@extends('layout/widget')

@section('icon')
    <i class="icon-th"></i>
@stop

@section('title')
    @yield('title')
@stop

@section('content')
    <div class="widget-content-header">
        @yield('content-header')
    </div>
    <table id="myTable" class="table table-bordered data-table">
        <thead>
            @yield('thead')
        </thead>
        <tbody>
            @yield('tbody')
        </tbody>
    </table>
@stop
@yield('footer')
