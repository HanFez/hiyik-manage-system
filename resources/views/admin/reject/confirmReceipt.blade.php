<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/12/5
 * Time: 17:20
 */
$reject = $result->data;
//dd($result);
?>

<div class="widget-content form-horizontal">
    @if($reject != null)
        <div class="control-group">
            <label class="control-label">用户退货快递号：</label>
            <div class="controls">
                @if($reject->backShip != null)
                    <strong>{{$reject->backShip->no}}</strong>
                @else
                    用户还未发货
                @endif
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">用户退货邮费：</label>
            <div class="controls">
                @if($reject->backShip != null)
                    <strong>{{$reject->backShip->fee != null ? $reject->backShip->fee : null}}{{$reject->backShip->currency}}</strong>
                @else
                    用户还未发货
                @endif
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">是否同意退邮费：</label>
            <div class="controls">
                <label>
                    <input type="radio" class="ship-fee-result" name="shipFeeResult" value="1" />
                    同意</label>
                <label>
                    <input type="radio" class="ship-fee-result" name="shipFeeResult" value="0" />
                    拒绝</label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">如果拒绝请输入理由：</label>
            <div class="controls">
                <input type="text" id="return-fee-reason" value="" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">退货产品是否有误：</label>
            <div class="controls">
                <label>
                    <input type="radio" class="goods-result" name="goodsResult" value="0" />
                    是</label>
                <label>
                    <input type="radio" class="goods-result" name="goodsResult" value="1" />
                    否</label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">如果是请输入理由：</label>
            <div class="controls">
                <input type="text" id="confirm-reason" value="" />
            </div>
        </div>
    @endif
</div>
<script>
    $('.ship-fee-result').on('click',function(){
        var ship_fee = $(this).val();
        if(ship_fee === '1'){
            $('#return-fee-reason').val('同意退邮');
            $('#return-fee-reason').attr("disabled","disabled");
        }else{
            $('#return-fee-reason').val('');
            $('#return-fee-reason').removeAttr("disabled");
        }
    });
    $('.goods-result').on('click',function(){
        var goods = $(this).val();
        if(goods === '1'){
            $('#confirm-reason').attr("readOnly","readOnly");
        }else{
            $('#confirm-reason').removeAttr("readOnly");
        }
    })
</script>