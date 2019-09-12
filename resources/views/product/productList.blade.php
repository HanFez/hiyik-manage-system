<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/1/16
 * Time: 9:51
 */
$path = \App\IekModel\Version1_0\Constants\Path::FILE_PATH;
$transDataTable = trans('dataTable');

$searchText = isset($result->search)?$result->search:null;
$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$type = isset($result->type) ? $result->type : 'all';
$url = 'new_pro/products?type='.$type;
$products = isset($result->data)? $result->data : null;
//dd($products);
?>
@extends('layout/widget')

@section('title')
    产品列表
@stop

@section('content')
        <div class="data-btn-group">
            <div class="btn-group" id="filter-type">
                <button data-toggle="dropdown" class="btn dropdown-toggle">
                    @if(isset($result->type))
                        @if($result->type == 'all')
                            所有产品
                        @elseif($result->type == 'modify')
                            已修改产品
                        @elseif($result->type == 'change')
                            已变动产品
                        @elseif($result->type == 'active')
                            已删除产品
                        @endif
                    @endif
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li class="divider"></li>
                    <li><a data-type="all">所有产品</a></li>
                    <li class="divider"></li>
                    <li><a data-type="modify">已修改产品</a></li>
                    <li class="divider"></li>
                    <li><a data-type="change">已变动产品</a></li>
                    <li class="divider"></li>
                    <li><a data-type="active">已删除产品</a></li>
                </ul>
            </div>
            <div class="search" id="list-search">
                <input placeholder="搜索产品" type="text">
                <button class="tip-bottom btn btn-success" data-original-title="搜索"><i class="icon-search "></i></button>
                @if(!is_null($searchText))
                    <span>
                    产品名称匹配：
                    <span class="badge badge-info">
                        {{ $searchText }}
                    </span>
                    <input id="search-clear" type="submit" class="btn btn-warning btn-mini" value="清空搜索">
                </span>
                @endif
            </div>
        </div>
    <div class="data-list clearfix">
        @if($result->statusCode == 10008)
            <script>
                window.location.href = '/login.html';
            </script>
        @endif

        @if($products != null && !$products->isEmpty())
            <ul class="thumbnails">
                @foreach ($products as $product)
                    <li class="span2">
                        <div class="thumbnail">
                            <a href="javascript:void(0)" data="{{$product->id}}">
                                @if(isset($product->productThumb->thumb->norm))
                                    <img src="{{$path.$product->productThumb->thumb->norm[4]->uri}}"
                                         alt="{{$product->thumb->thumb->file_name or ''}}">
                                @else
                                    <img src="/img/default.png" alt="">
                                @endif
                            </a>
                            <div class="caption">
                                <h5>
                                    <a href="javascript:void(0)" data="{{$product->id}}">
                                        @if(isset($product->productDefine))
                                            {{$product->productDefine->name}}
                                        @else
                                            {{ '无标题' }}
                                        @endif
                                    </a>
                                </h5>
                                <p>创建时间: <span data-time="utc">{{ $product->updated_at }}</span></p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            @else
            <p>{{"还没有该产品的呢！"}}</p>
        @endif
        @include('layout/pagination')
    </div>
    <script>
        bindEventToButtonInListView({
            'type': 'product',
            'take': '{{ $take }}',
            'url': '{{$url}}'
        });
    </script>
@stop

