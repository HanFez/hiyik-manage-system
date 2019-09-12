<?php
/**
 * Created by PhpStorm.
 * User: wj
 * Date: 10/24/16
 * Time: 2:10 PM
 */

use App\IekModel\Version1_0\Constants\Path;
$path    = Path::FILE_PATH;
$transDataTable = trans('dataTable');

$url = Path::ORIGIN_PATH.'products';
$data = null;
$products = null;
$isSell = null;
$isDelete = null;
if(isset($result) && $result->isOk() && isset($result->data)) {
    $data = $result->data;
    if(isset($data->data)) {
        $products = $data->data;
    }
    $isOne = true;
    if(isset($data->isSell)) {
        $isSell = json_encode($data->isSell);
        if($isOne) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $url .= 'isSell='.$isSell;
        $isOne = false;
    }
    if(isset($data->isDelete)) {
        $isDelete = json_encode($data->isDelete);
        if($isOne) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $url .= 'isDelete='.$isDelete;
        $isOne = false;
    }
}
$total = isset($data->total) ? $data->total : 0;
$skip  = isset($data->skip) ? $data->skip : 0;
$take  = isset($data->take) ? $data->take : 6;
//echo $total,','.$take.','.$skip.',isSell='.json_encode($isSell).',isDelete='.json_encode($isDelete);
//dd(json_decode(json_encode($result)));
?>
@extends('layout/widget')

@section('title')
    产品列表
@stop

@section('content')
    <div class="data-btn-group">
        是否在售：
        <div class="btn-group" id="is-sell">
            <button data-toggle="dropdown" class="btn dropdown-toggle">
                @if(isset($isSell))
                    @if($isSell == 'true')
                        已在售
                    @else
                        未在售
                    @endif
                @else
                    全部
                @endif
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a data-type="all">全部</a></li>
                <li class="divider"></li>
                <li><a data-type="true">已在售</a></li>
                <li class="divider"></li>
                <li><a data-type="false">未在售</a></li>
            </ul>
        </div>
        是否删除：
        <div class="btn-group" id="is-delete">
            <button data-toggle="dropdown" class="btn dropdown-toggle">
                @if(isset($isDelete))
                    @if($isDelete == 'true')
                        已删除
                    @else
                        未删除
                    @endif
                @else
                    全部
                @endif
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a data-type="all">全部</a></li>
                <li class="divider"></li>
                <li><a data-type="true">已删除</a></li>
                <li class="divider"></li>
                <li><a data-type="false">未删除</a></li>
            </ul>
        </div>
        {{--<div class="search" id="list-search">
            <input placeholder="搜索产品编号" type="text">
            <button class="tip-bottom btn btn-success" data-original-title="搜索"><i class="icon-search "></i></button>
            @if(!is_null($searchText))
                <span>
                    产品编号匹配：
                    <span class="badge badge-info">
                        {{ $searchText }}
                    </span>
                    <input id="search-clear" type="submit" class="btn btn-warning btn-mini" value="清空搜索">
                </span>
            @endif
        </div>--}}
    </div>
    <div class="data-list clearfix" name="product-list">
        @if(isset($products))
            <ul class="thumbnails">
            @foreach ($products as $product)
                    <li class="span2">
                    <div class="thumbnail" data="{{$product->id}}">
                        <a href="javascript:void(0)" data="{{$product->id}}">
                        @if(isset($product->image->norms[2]->uri))
                            <img src="{{ $path.$product->image->norms[2]->uri }}"
                                 alt="{{ $product->name or '' }}">
                        @else
                            <img src="/img/default.png" alt="">
                        @endif
                        </a>
                        @if($product -> is_removed)
                            <span class="data-label label label-danger">已删除</span>
                        @else
                            @if(isset($product->is_sell) && $product->is_sell)
                                <span class="data-label label label-success">已在售</span>
                            @else
                                <span class="data-label label label-inverse">未在售</span>
                            @endif
                        @endif
                        <div class="caption">
                            <h5>
                                <a href="javascript:void(0)" data="{{$product->id}}">
                                    @if(isset($product->no))
                                        编号: {{$product->no}}
                                    @else
                                        {{ '无编号' }}
                                    @endif
                                </a>
                            </h5>
                            <p>名字: {{ $product->name or '' }}</p>
                            <p>尺寸: {{ $product->width or '' }} cm x {{ $product->height or '' }} cm</p>
                            <p>上架时间: <span data-time="utc">{{ $product->updated_at }}</span></p>
                        </div>
                    </div>
                </li>
            @endforeach
            </ul>
        @else
            <p>{{ $transDataTable['zeroRecords'] or 'No Data' }}</p>
        @endif
        @include('layout/pagination', ['result' => $data])
    </div>
    <script>
        $(function () {
            $('#is-sell a').unbind('click').on('click', bindBtnGroupClickEvent);
            $('#is-delete a').unbind('click').on('click', bindBtnGroupClickEvent);
            $('.data-list[name="product-list"] a:not(#pagination a)').on('click', bindDialogShowProductInfo);
        });
        function bindDialogShowProductInfo() {
            var $this = $(this);
            var id = $this.attr('data');
            if(isNull(id)) {
                bootstrapQ.alert('产品ID错误，请刷新重试');
            } else {
                loadingShow();
                var path = originPath();
                var url = path + 'products/' + id;
                ajaxData('get', url, handleGetViewShowProduct, [], 'myProduct');
            }
        }
        function handleGetViewShowProduct(result, dialogId) {
            if(!isNull(result)) {
                createDialogLargeBox(result, 'no-padding margin', dialogId);
                convertUtcTimeToLocalTime(dialogId);
                loadingHide();
            }
        }
        function bindBtnGroupClickEvent() {
            var isSell = '{{ $isSell }}';
            var isDelete = '{{ $isDelete }}';
            var take = '{{ $take }}';
            var skip = 0;
            var $this = $(this);
            var $buttons = $this.closest('.btn-group');
            var id = $buttons.attr('id');
            if(!isNull($this.attr('data-type'))) {
                var type = $this.attr('data-type');
                var path = originPath();
                var url = path + 'products?';
                var flag = true;
                if(id == 'is-sell') {
                    if(isDelete != 'null') {
                        url += 'isDelete=' + isDelete;
                        flag = false;
                    }
                    if(type != 'all') {
                        url += '&isSell=';
                        flag = false;
                    }
                } else {
                    if(isSell != 'null') {
                        url += 'isSell=' + isSell;
                        flag = false;
                    }
                    if(type != 'all') {
                        url += '&isDelete=';
                        flag = false;
                    }
                }
                if(type != 'all') {
                    url += type;
                }
                if(flag == false) {
                    url += '&';
                }
                url += 'take=' + take + '&skip=' + skip;
                ajaxData('get', url,  function (view) {
                    $('#container').html(view);
                    convertUtcTimeToLocalTime('container');
                });
            }
        }
    </script>
@stop
