<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/7
 * Time: 15:47
 */
$action = isset($action) ? $action : null;
$pds = isset($pds)?$pds:null;
$products = isset($products)?$products:null;
$predefine = isset($predefine)?$predefine:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改预定义"}}@else{{"预定义产品"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-predefine">
            <div class="control-group">
                <label class="control-label">定义产品：</label>
                <div class="controls">
                    <select name="productDefine" id="product-define-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($pds as $pd)
                                <option value="{{$pd->id}}" {{$pd->id == $predefine->product_define_id ? 'selected="selected"':''}}>
                                    {{$pd->name}}
                                </option>
                            @endforeach
                        @else
                            @foreach($pds as $pd)
                                <option value="{{$pd->id}}">{{$pd->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">产品：</label>
                <div class="controls">
                    <select name="product" id="product-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($products as $product)
                                <option value="{{$product->id}}" {{$product->id == $predefine->product_id ? 'selected="selected"':''}}>
                                    {{$product->product_define_id}}
                                </option>
                            @endforeach
                        @else
                            @foreach($products as $product)
                                <option value="{{$product->id}}">{{$product->id}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-predefine">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-predefine');
    var action = '{{$action}}';
    var id = '{{is_null($predefine) ? NULL : $predefine->id}}';
    $('#save-predefine').unbind('click').bind('click',function(){
        removeInputMessage(form);
        //var data = getFormValue(form);

        var productDefineId = $('#product-define-id').val();
        var productId = $('#product-id').val();
        var param = {};
        param.data = {};
        param.data.productDefineId = productDefineId;
        param.data.productId = productId;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updatePredefine/'+id, function (result) {
                if(result) {
                    $('#form-predefine').append(result);
                    //$('#form-predefine input').val('');
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createPredefine', function (result) {
                if(result) {
                    $('#form-predefine').append(result);
                    //$('#form-predefine input').val('');
                }
            }, [],param);
        }
    });
</script>
