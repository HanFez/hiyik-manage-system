<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/29
 * Time: 9:19
 */
$path = \App\IekModel\Version1_0\Constants\Path::FILE_PATH;
$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$cartId = isset($result->cartId) ? $result->cartId : null;
$url = 'cart/'.$cartId;
$transDataTable = trans('dataTable');
$carts = $result->data;
?>
@extends('layout/widget')
@section('title')
    购物车
@stop
@section('content')
    <div class="data-list clearfix">
        <div class="widget-content nopadding">
            <table class="table table-bordered table-invoice-full">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>产品信息</th>
                    <th>数量</th>
                    <th>重量</th>
                    <th>价格</th>
                    <th>添加日期</th>
                </tr>
                </thead>
                <tbody>
                @if(!$carts->isEmpty())
                    @foreach($carts as $k => $cart)
                        <tr>
                            <td>{{++$k}}</td>
                            <td width="400">
                                @if(!is_null($cart->products))
                                    <div style="float: left;">
                                        @if(!is_null($cart->products->productThumb))
                                            <img src="{{$path.$cart->products->productThumb->thumb->norm[4]->uri}}">
                                        @else
                                            <img src="default.jpg" alt="">
                                        @endif
                                    </div>
                                    <div style="float: right">
                                        <p>
                                            产品类型：
                                            @if(!is_null($cart->products->productDefine))
                                                {{$cart->products->productDefine->name}}
                                            @endif
                                        </p>
                                        <p>
                                            尺寸：
                                            {{$cart->products->phy_width}}x
                                            {{$cart->products->phy_height}}x
                                            {{$cart->products->phy_depth}}mm
                                        </p>
                                        <p>
                                            画框：
                                            @if(is_null($cart->products->border))
                                                无
                                            @else
                                                {{$cart->products->border->materialDefine->name}}
                                            @endif
                                        </p>
                                        <p>
                                            卡纸：
                                            @if($cart->products->frame->isEmpty())
                                                无
                                            @else
                                                @foreach($cart->products->frame as $k=>$frame)
                                                    {{$frame->materialDefine->name}}（第{{$k+1}}层）<br>
                                                @endforeach
                                            @endif
                                        </p>
                                        <p>
                                            画芯：
                                            @if(is_null($cart->products->core))
                                                无
                                            @else
                                                {{$cart->products->core->materialDefine->name}}
                                            @endif
                                        </p>
                                        <p>
                                            玻璃：
                                            @if(is_null($cart->products->front))
                                                无
                                            @else
                                                {{$cart->products->front->materialDefine->name}}
                                            @endif
                                        </p>
                                        <p>
                                            背板：
                                            @if(is_null($cart->products->back))
                                                无
                                            @else
                                                {{$cart->products->back->materialDefine->name}}
                                            @endif
                                        </p>
                                        <p>
                                            背饰：
                                            @if(is_null($cart->products->backFacade))
                                                无
                                            @else
                                                {{$cart->products->backFacade->materialDefine->name}}
                                            @endif
                                        </p>
                                    </div>
                                @else
                                    无数据
                                @endif
                            </td>
                            <td>
                                x{{$cart->num or null}}
                            </td>
                            @if(!is_null($cart->products))
                                <td>{{$cart->products->weight}}</td>
                                <td>{{$cart->products->price.$cart->products->currency}}</td>
                            @endif
                            <td>{{$cart->added_at}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td rowspan="9"><code>购物车还未添加产品</code></td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        @include('layout/pagination')
    </div>
@stop