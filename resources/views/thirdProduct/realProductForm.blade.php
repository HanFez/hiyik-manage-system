<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/20/17
 * Time: 9:48 AM
 */

use App\IekModel\Version1_0\Constants\RealProductStatus;
$r = new ReflectionClass(RealProductStatus::class);
$statues = $r -> getConstants();
$result = isset($result) ? $result : null;
$isModify = false;
$data = null;
$products = null;
if(isset($result) && $result->isOk() && isset($result->data)) {
    $result = json_decode(json_encode($result));
    $data = $result->data;
    if(property_exists($data, 'products')) {
        $products = $data->products;
    } else {
        $isModify = true;
    }
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
    生产
    @include('layout/required')
@stop

@section('content')
    <form class="form-horizontal" id="real-product-form" realProductId="{{ $data->id or '' }}">
        @if($isModify == true)
            <div class="control-group">
                <label class="control-label"><span class="text-important">*</span>编号 :</label>
                <div class="controls">
                    <input type="text" class="span11" placeholder="编号" name="no" required
                           value="{{ $data->no or '' }}"
                            {{ isset($data->no) ? 'disabled' : '' }}/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">上级编号 :</label>
                <div class="controls">
                    <input type="text" class="span11" placeholder="上级编号" name="fromNo"
                           value="{{ $data->from_no or '' }}" disabled />
                    <span class="help-block">该编号的产品生产不合格，此产品是由该产品重新生产的</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">质检员 :</label>
                <div class="controls">
                    <input type="text" class="span11" placeholder="质检员" name="checker"
                           value="{{ $data->checker or '' }}" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><span class="text-important">*</span>状态 :</label>
                <div class="controls">
                    <select name="" id="">
                        @if(isset($statues))
                            @foreach($statues as $status)
                                <option value="{{ $status }}" {{ (isset($data->status) && $data->status == $status) ? 'selected' : '' }}>
                                    {{ \App\IekModel\Version1_0\IekModel::strTrans($status, 'RealProductStatus') }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        @else
        <div class="span11" style="margin: 20px auto; float: none;">
            <a name="add-group" class="btn btn-default">添加</a>
            <div class="controls-box">
                <div class="control-group">
                    <label class="control-label"><span class="text-important">*</span>选择生产的产品 :</label>
                    <div class="controls">
                        <select name="product-id" multiple >
                            @if(isset($products))
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->no }}
                                        -
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><span class="text-important">*</span>每个产品生产的数量 :</label>
                    <div class="controls">
                        <input type="number" class="span11" placeholder="生产数量" name="num" min="1" data-type="int" required
                               value="{{ $data->mount or '1' }}"
                                {{ isset($data->mount) ? 'disabled' : '' }}/>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="form-actions">
            <button type="submit" class="btn btn-success" isModify="{{ $isModify }}">保存</button>
        </div>
        @if($isModify == false)
                @include('../layout/importExcel', ['type' => 'taoBaoOrder', 'data' => \App\IekModel\Version1_0\Constants\ImportExcel::TB_ORDER])
                @include('../layout/importExcel', ['type' => 'taoBaoOrderProduct', 'data' => \App\IekModel\Version1_0\Constants\ImportExcel::TB_ORDER_PRODUCT])
        @endif
    </form>
    <script>
        $(function () {
            var $form = $('#real-product-form');
            $form.find('.btn[type="submit"]').on('click', saveRealProduct);
            $form.find('.btn[name="add-group"]').on('click', addGroupControls);
            $form.find('.btn-del').on('click', deleteGroupControls);
        });
        function deleteGroupControls() {
            var $this = $(this);
            var $group = $this.closest('.controls-box');
            $group.remove();
        }
        function addGroupControls() {
            var $this = $(this);
            var $form = $this.closest('form');
            var $group = $form.find('.controls-box').eq(0);
            $group = $($group.prop('outerHTML'));
            $group.find('select option:selected').removeProp('selected');
            $group.find('.select2-container').remove();
            $group.find('input').val(1);
            $group.prepend('<a class="btn-del btn btn-danger">删除</a>');
            $this.parent().append($group);
            $group.find('select').select2();
            $group.find('.btn-del').on('click', deleteGroupControls);
        }
        function saveRealProduct(event) {
            eventUtil.preventDefault(event);
            loadingShow();
            var $this = $(this);
            var $form = $this.closest('form');
            var $products = $form.find('.controls-box');
            var flag = true;
            var values = [];
            $products.each(function () {
                var $group = $(this);
                var $productIds = $group.find('select[name="product-id"]');
                var productIds = $productIds.select2('val');
                var value = getFormValue($group);
                removeInputMessage($group);
                var f = true;
                if(value == false) {
                    flag = false;
                    f = false;
                }
                if(isNull(productIds) || productIds.length == 0) {
                    setInputMessage($productIds, 'error', '请选择需要生产的产品');
                    flag = false;
                    f = false;
                }
                if(f == true) {
                    for(var i in productIds) {
                        values.push({
                            id: productIds[i],
                            num: value.num
                        })
                    }
                }
            })
            if(flag == true) {
                var params = {
                    data: {
                        products: values
                    }
                }
//                console.log(params);
                var path = originPath();
                ajaxData('post', path + 'realProducts', parseSaveRealProduct, [], params);
            } else {
                loadingHide();
            }
        }
        function parseSaveRealProduct(result, params) {
            if(isOk(result)) {
                saveSuccess();
                loadingHide();
            } else {
                saveError();
            }
        }
    </script>
@stop