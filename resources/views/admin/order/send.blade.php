<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/18
 * Time: 16:01
 */
?>
<div class="form-horizontal">
<div class="widget-box">
    <div class="widget-content">
        <div class="control-group">
            <label class="control-label">快递公司：</label>
            <div class="controls">
                <select name="company" id="company">
                    @if(!$company->isEmpty())
                        @foreach($company as $item)
                            <option value="{{$item->id}}">{{$item->description}}/{{$item->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">快递单号：</label>
            <div class="controls">
                <input type="text" name="no" id="ship-no" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">快递成本：</label>
            <div class="controls">
                <input type="text" name="costFee" id="cost-fee" value="">
            </div>
        </div>
    </div>
</div>
</div>
