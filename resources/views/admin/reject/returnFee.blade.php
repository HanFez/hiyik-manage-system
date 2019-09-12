<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/12/6
 * Time: 16:40
 */
//dd($result);
$data = $result->data;
if(!is_null($data)){
    if($data->rejectResultHandle != null && $data->rejectResultHandle->reject != null){
        $reject = $data->rejectResultHandle->reject;
        $rejectId = $reject->id;
    }
}
?>
<div class="widget-content form-horizontal">
    @if(!is_null($data))
        <div class="control-group">
            <div class="control-label"><label>请勾选用户退货邮费：</label></div>
            <div class="controls">
                @if(isset($reject))
                    @if($reject->backShip != null)
                        <label for="">
                            <input type="checkbox" class="person-return-ship-fee" value="{{$reject->backShip->fee}}" />
                        </label>
                        <strong style="margin-left: 10px;">{{$reject->backShip->fee}}{{$reject->backShip->currency}}</strong>
                    @endif
                @endif
            </div>
        </div>
        <input type="hidden" class="person-reject-id" value="{{$rejectId}}">
        @if($data->order != null && $data->order->personOrder != null)
            <input type="hidden" id="person-id" value="{{$data->order->personOrder->person_id}}">
        @endif
    @endif
</div>
