<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/11/16
 * Time: 5:13 PM
 */
use App\IekModel\Version1_0\IekModel;
$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$type = isset($result->type) ? $result->type : null;
$url = is_null($type) ? null : 'announceList?type='.$type;
$transDataTable = trans('dataTable');
?>
@extends('layout/widget')

@section('title')
    公告列表
@stop
@section('content')
    <div class="btn-group data-btn-group" id="filter-type">
        <button data-toggle="dropdown" class="btn dropdown-toggle">
            {{ IekModel::strTrans($result->typeArray[$result->type], 'announce') }}
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            @foreach($result->typeArray as $key=>$val)
                <li><a data-type="{{$key}}">{{ IekModel::strTrans($val, 'announce') }}</a></li>
                <li class="divider"></li>
            @endforeach
        </ul>
    </div>
    <div class="data-list clearfix">
    @if($result->data->isEmpty())
        {{ $transDataTable['zeroRecords'] or 'zeroRecords' }}
    @endif
    @foreach($result->data as $announce)
        <div class="new-update clearfix">
            <div class="update-done">
                <a href="javascript:void(0)" data="{{ $announce->id }}">
                    <strong>{{ $announce->title }}</strong>
                </a>
                <span>开始时间:{{ $announce->begin_at }}</span>
                <span>结束时间:{{ $announce->end_at }}</span>
            </div>
        </div>
    @endforeach
    @include('layout/pagination')
    </div>
    <script>
        bindEventToButtonInListView({
            'type': 'announce',
            'take': '{{ $take }}'
        })
    </script>
@stop