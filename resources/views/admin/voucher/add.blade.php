<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/6/8
 * Time: 10:36
 */

use App\IekModel\Version1_0\IekModel;

$targetTypes = ['order', 'product'/*, 'iwall'*/];
$isModify = false;
$voucherGetDate = null;
$expiration = null;
$expirationInvalidTime = null;
$expirationBeginAt = null;
$expirationEndAt = null;
$voucherLimit = null;
if(isset($data)) {
    $isModify = true;
    $voucherGetDate = isset($data->voucherGetDate) ? $data->voucherGetDate : null;
    $expiration = isset($data->expiration) ? $data->expiration : null;
    if(isset($expiration)) {
        foreach($expiration as $val) {
            $expirationName = $val -> name;
            if($expirationName == 'valid_time') {
                $expirationInvalidTime = $val->value;
            } else if($expirationName == 'begin_at') {
                $expirationBeginAt = $val->value;
            } else if($expirationName == 'end_at') {
                $expirationEndAt = $val->value;
            }
        }
    }
    if(isset($data->voucherLimitRelation)) {
        if(isset($data->voucherLimitRelation[0]->voucherLimit)) {
            $voucherLimit = $data->voucherLimitRelation[0]->voucherLimit;
            if(!in_array($voucherLimit->target_type, $targetTypes)) {
                array_push($targetTypes, $voucherLimit->target_type);
            }
        }
    }
} else {
    $data = null;
}
?>
@extends('layout/widget')

@section('icon')
    <i class="icon-plus"></i>
@stop

@section('title')
    添加优惠券
    @include('layout/required')
@stop

@section('content')
    <form class="form-horizontal" id="form-voucher" voucherId="{{ $voucher->id or ''}}">
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>优惠券名：</label>
            <div class="controls">
                <input type="text" name="name" class="span11" required="required"
                    value="{{ $voucher->name or '' }}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">描述：</label>
            <div class="controls">
                <input type="text" name="description" class="span11"
                       value="{{ $voucher->description or '' }}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>领取时间/活动时间：</label>
            <div class="controls">
                <div class="controls-box">
                    <div class="control-group">
                        <label class="control-label">开始时间：</label>
                        <div class="controls">
                            <input type="text" class="span11 datepicker" data-date-format="yyyy-mm-dd" name="beginAt" class="span11" required="required" value="{{ $voucherGetDate->begin_at or '' }}">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">结束时间：</label>
                        <div class="controls">
                            <input type="text" class="span11 datepicker" data-date-format="yyyy-mm-dd" name="endAt" class="span11" required="required" value="{{ $voucherGetDate->end_at or '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>领取后的使用时间：</label>
            <div class="controls">
                <div data-toggle="buttons-radio" class="btn-group" name="expiration">
                    <button class="btn btn-primary {{ (!isset($expiration) || isset($expirationInvalidTime)) ? 'active' : '' }}" type="button" name="validTime">自领取后几小时内可使用</button>
                    <button class="btn btn-primary {{ isset($expirationBeginAt) ? 'active' : '' }}" type="button" name="timeSlice">在此时间段内可使用</button>
                </div>
                <div class="controls-box" name="validTime"
                     style="{{ (!isset($expiration) || isset($expirationInvalidTime)) ? 'display: block' : 'display: none' }}">
                    <label class="control-label">自领取后几小时内可使用：</label>
                    <div class="controls">
                        <input type="text" name="validTime" class="span11" data-type="number" value="{{ $expirationInvalidTime or '' }}">
                    </div>
                </div>
                <div class="controls-box" name="timeSlice" style="{{ isset($expirationBeginAt) ? 'display: block' : 'display: none' }}">
                    <div class="control-group">
                        <label class="control-label">开始时间：</label>
                        <div class="controls">
                            <input type="text" class="span11 datepicker" data-date-format="yyyy-mm-dd" name="expirationBeginAt" class="span11"
                                value="{{ $expirationBeginAt or ''  }}">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">结束时间：</label>
                        <div class="controls">
                            <input type="text" class="span11 datepicker" data-date-format="yyyy-mm-dd" name="expirationEndAt" class="span11"
                                value="{{ $expirationEndAt or ''  }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>优惠券类型：</label>
            <div class="controls">
                <label>
                    <input type="radio" name="voucherType" value="0" {{ (!isset($data) || $data->voucher_type == 0) ? 'checked="checked"' : '' }} />
                    现金券</label>
                <label>
                    <input type="radio" name="voucherType" value="1" {{ (isset($data) && $data->voucher_type == 1) ? 'checked="checked"' : '' }} />
                    折扣券</label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>面值：</label>
            <div class="controls">
                <input type="text" name="figure" class="span11" data-type="number" min="0" required="required"
                    value="{{ $data->figure or ''  }}">
                <span class="help-block">
                    折扣券的面值：0-1<br>
                    现金券的面值：无要求，是数字即可
                </span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>最低使用金额：</label>
            <div class="controls">
                <input type="text" name="minFee" class="span11" data-type="number" min="0" required="required"
                       value="{{ $voucherLimit->min_fee or ''  }}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>货币符号：</label>
            <div class="controls">
                <input type="text" name="currency" class="span11" value="CNY" required="required"
                       value="{{ $data->amount or ''  }}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>总发放量（张）：</label>
            <div class="controls">
                <input type="text" name="amount" class="span11" data-type="int" required="required" min="1"
                       value="{{ $voucherLimit->amount or ''  }}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>每张优惠券的可使用次数：</label>
            <div class="controls">
                <input type="text" name="threshold" class="span11" data-type="int" required="required" min="1"
                       value="{{ $voucherLimit->threshold or 1 }}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>适用类型：</label>
            <div class="controls">
                @if(isset($targetTypes))
                    <select name="targetType" class="span11">
                        @foreach($targetTypes as $val)
                            <option value="{{ $val }}"
                                    {{ (isset($voucherLimit) && $voucherLimit->target_type == $val) ? 'selected="selected' : '' }}>
                                {{ IekModel::strTrans($val, 'table') }}</option>
                        @endforeach
                    </select>
                @else
                    <div>暂无类型可选</div>
                @endif
                <span class="help-block">
                    订单类的优惠券只能用于整个订单上；<br>
                    产品类的优惠券只能用户订单的单个产品上;<br>
                    产品类的优惠券可以和订单类的优惠券同时叠加使用
                </span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>允许创作者使用：</label>
            <div class="controls">
                <label>
                    <input type="radio" name="allowAuthor" value="1"
                            {{ (isset($voucherLimit) && $voucherLimit->allow_author === true) ? 'checked="checked' : '' }}>
                    是</label>
                <label>
                    <input type="radio" name="allowAuthor" value="0"
                            {{ (!isset($voucherLimit) || $voucherLimit->allow_author === false) ? 'checked="checked' : '' }}>
                    否</label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>是否是平台优惠券：</label>
            <div class="controls">
                <label>
                    <input type="radio" name="isUniversal" value="1"
                            {{ (!isset($data) || $data->is_universal === true) ? 'checked="checked' : '' }}>
                    是</label>
                <label>
                    <input type="radio" name="isUniversal" value="0"
                            {{ (isset($data) && $data->is_universal === false) ? 'checked="checked' : '' }}>
                    否</label>
                <span class="help-block">用户若获得了两张优惠券，一张属于平台优惠券，一张不属于平台优惠券，则可以叠加使用</span>
            </div>
        </div>
        <div class="form-actions">
            <a type="submit" class="btn btn-success">保存</a>
        </div>
    </form>
    <script>
        $(function () {
            var $form = $('#form-voucher');
            $('.btn-group .btn', $form).on('click', function () {
                var $this = $(this);
                var $parent = $this.parent();
                var name = $this.attr('name');
                if(isNull(name)) {
                    return false;
                }
                var $box = $parent.siblings('.controls-box[name="'+ name +'"]');
                if($box.length > 0) {
                    $box.siblings('.controls-box').hide();
                    $box.show();
                }
            })
            $('.btn[type="submit"]', $form).on('click',function(){
                removeInputMessage($form);
                var data = getFormValue($form);
                if(data == false) {
                    return false;
                }
                var flag = true;
                if(!compareDate(data.beginAt, data.endAt)) {
                    setInputMessage($form.find('input[name="endAt"]'), 'error', '开始日期不能大于结束日期，请重新填写结束日期');
                    flag = false;
                } else if(!judgeTime(data.endAt)) {
                    setInputMessage($form.find('input[name="endAt"]'), 'error', '结束日期不能大于当前日期');
                    flag = false;
                } else {
                    data.beginAt = formatDate(data.beginAt);
                    data.endAt = formatDate(data.endAt);
                    data.getDate = {
                        beginAt: data.beginAt,
                        endAt: data.endAt
                    };
                    delete data.beginAt;
                    delete data.endAt;
                }
                var $validTime = $('.btn-group .btn[name="validTime"]', $form);
                if($validTime.hasClass('active')) {
                    if(isNull(data.validTime)) {
                        setInputMessage($form.find('input[name="validTime"]'), 'error', '请输入优惠券的使用时间');
                        flag = false;
                    } else {
                        data.expiration = [{
                            key: 'valid_time',
                            val: data.validTime,
                            description: 'valid_time'
                        }]
                        delete data.expirationBeginAt;
                        delete data.expirationEndAt;
                        delete data.validTime;
                    }
                }
                var $timeSlice = $('.btn-group .btn[name="timeSlice"]', $form);
                if($timeSlice.hasClass('active')) {
                    if(isNull(data.expirationBeginAt)) {
                        setInputMessage($form.find('input[name="expirationBeginAt"]'), 'error', '请输入优惠券的开始使用时间');
                        flag = false;
                    }
                    if(isNull(data.expirationEndAt)) {
                        setInputMessage($form.find('input[name="expirationEndAt"]'), 'error', '请输入优惠券的结束使用时间');
                        flag = false;
                    }
                    if(!isNull(data.expirationBeginAt) && !isNull(data.expirationEndAt)) {
                        if (!compareDate(data.expirationBeginAt, data.expirationEndAt)) {
                            setInputMessage($form.find('input[name="expirationEndAt"]'), 'error', '开始日期不能大于结束日期，请重新填写结束日期');
                            flag = false;
                        } else if (!judgeTime(data.expirationEndAt)) {
                            setInputMessage($form.find('input[name="expirationEndAt"]'), 'error', '结束日期不能大于当前日期');
                            flag = false;
                        } else {
                            data.expirationBeginAt = formatDate(data.expirationBeginAt);
                            data.expirationEndAt = formatDate(data.expirationEndAt);
                            data.expiration = [{
                                key: 'begin_at',
                                val: data.expirationBeginAt,
                                description: 'begin_at'
                            },{
                                key: 'end_at',
                                val: data.expirationEndAt,
                                description: 'end_at'
                            }]
                            delete data.expirationBeginAt;
                            delete data.expirationEndAt;
                            delete data.validTime;
                        }
                    }
                }
                var $targetType = $('select[name="targetType"]');
                var targetType = $targetType.select2('val');
                if(isNull(targetType)) {
                    setInputMessage($targetType, 'error', '请选择优惠券适用类型');
                    flag = false;
                } else {
                    data.targetType = targetType;
                }
                if(data.voucherType == '1') {
                    var figure = data.figure;
                    if(figure > 1) {
                        setInputMessage($form.find('input[name="figure"]'), 'error', '请输入正确的折扣面值0-1之间');
                        flag = false;
                    }
                }
                if(flag == true) {
                    var params = {
                        data: data
                    };
                    var voucherId = $form.attr('voucherId');
                    var method = 'post';
                    var url = 'voucher';
                    if(!isNull(voucherId)) {
                        method = 'put';
                        url += '/' + voucherId;
                    }
                    ajaxData(method, url, function (result) {
                        if (result) {
//                        removeInputMessage($form);
                            $('#form-voucher').append(result);
                        }
                    }, [], params);
                }
            });
        })
    </script>
@stop