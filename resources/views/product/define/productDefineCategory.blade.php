<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/7
 * Time: 18:01
 */
$action = isset($action) ? $action : null;
$pds = isset($pds)?$pds:null;
$pcs = isset($pcs)?$pcs:null;
$pdc = isset($pdc)?$pdc:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改产品定义分类"}}@else{{"添加产品定义分类"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-product-define-category">
            <div class="control-group">
                <label class="control-label">定义产品：</label>
                <div class="controls">
                    <select name="productDefine" id="product-define-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($pds as $pd)
                                <option value="{{$pd->id}}" {{$pd->id == $pdc->product_define_id ? 'selected="selected"':''}}>
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
                <label class="control-label">产品分类：</label>
                <div class="controls">
                    <select name="productCategory" id="category-id" style="width: 220px;">
                        @if($action == 'edit')
                            @foreach($pcs as $pc)
                                <option value="{{$pc->id}}" {{$pc->id == $pdc->category_id ? 'selected="selected"':''}}>
                                    {{$pc->name}}
                                </option>
                            @endforeach
                        @else
                            @foreach($pcs as $pc)
                                <option value="{{$pc->id}}">{{$pc->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-product-define-category">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-product-define-category');
    var action = '{{$action}}';
    var id = '{{is_null($pdc) ? NULL : $pdc->id}}';
    $('#save-product-define-category').unbind('click').bind('click',function(){
        removeInputMessage(form);
        //var data = getFormValue(form);

        var productDefineId = $('#product-define-id').val();
        var categoryId = $('#category-id').val();
        var param = {};
        param.data = {};
        param.data.productDefineId = productDefineId;
        param.data.categoryId = categoryId;
        if(action == 'edit'){
            ajaxData('put', 'new_pro/updateProductDefineCategory/'+id, function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }else{
            ajaxData('post', 'new_pro/createProductDefineCategory', function (result) {
                if(result) {
                    form.append(result);
                    //form.val('');
                }
            }, [],param);
        }
    });
</script>