<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/6/8
 * Time: 20:19
 */

?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>修改平台优惠券</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-platform">
            <div class="control-group">
                <label class="control-label">优惠券：</label>
                <div class="controls">
                    <select name="voucherId" id="voucher" style="width:300px;">
                        @if(!$vouchers->isEmpty())
                            @foreach($vouchers as $voucher)
                                <option value="{{$voucher->id}}"
                                        {{$voucher->id === $platform->voucher_id ? 'selected="selected"': ""}}>
                                    {{$voucher->name}}
                                </option>
                            @endforeach
                        @else
                            <option value="">无数据</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">使用次数：</label>
                <div class="controls">
                    <input type="text" name="usetime" class="span4" value="{{$platform->usetime}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-platform">保存</a>
            </div>
            <input type="hidden" id="edit-id" value="{{$platform->id}}">
        </form>
    </div>
</div>
<script>
    var form = $('#form-platform');
    var id = $('#edit-id').val();
    $('#save-platform').unbind('click').bind('click',function(){
        removeInputMessage(form);
        var data = getFormValue(form);
        var voucherId = $('#voucher').select2('val');
        param = {};
        param.data = data;
        data.voucherId = voucherId;
        ajaxData('put', 'platformVoucher/'+ id, function (result) {
            if(result) {
                $('#form-platform').append(result);
            }
        }, [],param);
    });
</script>

