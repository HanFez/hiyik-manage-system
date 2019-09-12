<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/9
 * Time: 9:48
 */
?>
@if(!$order->orderProducts->isEmpty())
    @foreach($order->orderProducts as $k=>$product)
        <div class="span4">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-eye-open"></i> </span>
                    <h5>产品{{$k+1}}</h5>
                </div>
                <div class="widget-content nopadding">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>产品材料</th>
                            <th>材料状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>画框</td>
                            <td>
                                @if(!is_null($product->products->border))
                                    @if($product->products->border->materialDefine->is_removed == false)
                                        有材料
                                    @else
                                        无材料
                                    @endif
                                @else
                                    <code>产品不含画框</code>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>卡纸</td>
                            <td>
                                @if(!$product->products->frame->isEmpty())
                                    @foreach($product->products->frame as $frame)
                                        @if($frame->materialDefine->is_removed == false)
                                            有材料
                                        @else
                                            无材料
                                        @endif
                                    @endforeach
                                @else
                                    <code>产品不含卡纸</code>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>画芯</td>
                            <td>
                                @if(!is_null($product->products->core))
                                    @if($product->products->core->materialDefine->is_removed == false)
                                        有材料
                                    @else
                                        无材料
                                    @endif
                                @else
                                    <code>产品不含画芯</code>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>玻璃</td>
                            <td>
                                @if(!is_null($product->products->front))
                                    @if($product->products->front->materialDefine->is_removed == false)
                                        有材料
                                    @else
                                        无材料
                                    @endif
                                @else
                                    <code>产品不含玻璃</code>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>背板</td>
                            <td>
                                @if(!is_null($product->products->back))
                                    @if($product->products->back->materialDefine->is_removed == false)
                                        有材料
                                    @else
                                        无材料
                                    @endif
                                @else
                                    <code>产品不含背板</code>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>背饰</td>
                            <td>
                                @if(!is_null($product->products->backFacade))
                                    @if($product->products->backFacade->materialDefine->is_removed == false)
                                        有材料
                                    @else
                                        无材料
                                    @endif
                                @else
                                    <code>产品不含背饰</code>
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@endif
<div class="span4">
    <div class="widget-title"> <span class="icon"> <i class="icon-eye-open"></i> </span>
        <h5>审核</h5>
    </div>
    <div class="control-group">
        <div class="control-label"><label>状态：</label></div>
        <div class="controls">
            @foreach($status as $st)
                @if($st->name == 'producing')
                    <input type="radio" name="status" value="{{$st->name}}" class="span1" checked onchange="write_word()">提交生产
                @endif
                @if($st->name == 'waitProduct')
                    <input type="radio" name="status" value="{{$st->name}}" class="span1" onchange="write_word()">等待生产
                @endif
                @if($st->name == 'close')
                    <input type="radio" name="status" value="{{$st->name}}" class="span1" onchange="write_word()">关闭订单
                @endif
            @endforeach
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <label>操作理由：</label>
        </div>
        <div class="controls">
            <textarea name="reason" id="reason" class="span11">材料充足，提交生产</textarea>
        </div>
    </div>
</div>
<script>
    function write_word(){
        var click = $('input[type="radio"]:checked');
        var text = click.val();
        if(text=='producing'){
            $('#reason').val('材料充足，提交生产');
        }else if(text=='waitProduct'){
            $('#reason').val('材料不足，等待进货');
        }else if(text=='close'){
            $('#reason').val('材料已停产');
        }
    }
</script>