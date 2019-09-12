<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/10/26
 * Time: 15:11
 */
if(!$result->data->isEmpty()){
    foreach($result->data as $refund){
        $reason = $refund->reason;
        $order = $refund->order;
        $handle = $refund->refundRequestHandle;
    }
}
$path = \App\IekModel\Version1_0\Constants\Path::FILE_PATH;
?>
@if(isset($order) && !is_null($order))
    <div class="row-fluid">
        <div class="span6" style="margin-left: 20px;">
            @if(!is_null($order->orderReceiveInformation))
                <table class="">
                    <tbody>
                    <tr>
                        <td><h4>用户信息</h4></td>
                    </tr>
                    <tr>
                        <td>姓名：</td>
                        <td>
                            @if(!$order->orderReceiveInformation->receiveInformation->name->isEmpty())
                                @foreach($order->orderReceiveInformation->receiveInformation->name as $name)
                                    @if($name->is_forbidden == true)
                                        ***<code>违规被禁</code>
                                    @else
                                        {{$name->first_name.$name->middle_name.$name->last_name}}
                                    @endif
                                @endforeach
                            @else
                                {{'无'}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>地址：</td>
                        <td>
                            @if(!$order->orderReceiveInformation->receiveInformation->address->isEmpty())
                                @foreach($order->orderReceiveInformation->receiveInformation->address as $address)
                                    {{$address->city->merge_name}},{{$address->address}}
                                @endforeach
                            @else
                                {{'无'}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>联系电话：</td>
                        <td>
                            @if(!$order->orderReceiveInformation->receiveInformation->phone->isEmpty())
                                @foreach($order->orderReceiveInformation->receiveInformation->phone as $phone)
                                    {{$phone->phone}}
                                @endforeach
                            @else
                                {{'无'}}
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            @endif
        </div>
        <div class="span6">
            <h4>订单信息</h4>
            <table class="table table-bordered table-invoice">
                <tbody>
                <tr>
                    <td class="width30">订单号：</td>
                    <td class="width70">
                        <strong>{{$order->order_no or '无'}}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="width30">订单创建人：</td>
                    <td class="width70">
                        @if(!is_null($order->personOrder))
                            @foreach($order->personOrder->person->personNick as $name)
                                @if($name->is_active == true)
                                    <strong>{{$name->nick->nick or '无'}}</strong>
                                @endif
                            @endforeach
                        @else
                            {{'无'}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>订单优惠券：</td>
                    <td>
                        @if(!$order->orderPersonVoucher->isEmpty())
                            @foreach($order->orderPersonVoucher as $voucher)
                                <?php
                                if($voucher->personVoucher != null && $voucher->personVoucher->voucher != null)
                                {
                                    $vname = $voucher->personVoucher->voucher->name;
                                    $official = $voucher->personVoucher->voucher->is_official;
                                    $type = $voucher->personVoucher->voucher->voucher_type;
                                    $currency = $voucher->personVoucher->voucher->currency;
                                    $figure = $voucher->personVoucher->voucher->figure;
                                }
                                ?>
                                <strong>{{isset($vname)?$vname:"无"}}</strong>
                                {{isset($official) ? $official === true ? '（平台优惠券）': '（个人优惠券）' : ""}}<br>
                                {{isset($type) ? $type === 0 ? "现金券 ": "折扣券 " : ""}}<br>
                                <strong>
                                    {{isset($figure) ? $figure:""}}{{isset($currency) ? $currency:""}}
                                </strong>
                                {{'[已使用 '.$voucher->used_times.' 次]'}}<br>
                            @endforeach
                        @else
                            <strong>{{'无'}}</strong>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>快递单号：</td>
                    <td>
                        @if(!is_null($order->orderShip))
                            <strong>
                                {{ $order->orderShip->ship->no or '无'}}
                            </strong>
                        @else
                            {{'无'}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>订单状态：</td>
                    <td>
                        @if(!$order->orderStatus->isEmpty())
                            @foreach($order->orderStatus as $status)
                                @if($status->is_current == true)
                                    <strong>
                                        @if(!is_null($status->status))
                                            {{\App\IekModel\Version1_0\IekModel::strTrans($status->status->name,'order')}}
                                        @else
                                            {{'无'}}
                                        @endif
                                    </strong>
                                @endif
                            @endforeach
                        @else
                            {{'无'}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="width30">订单创建时间：</td>
                    <td class="width70">
                        <strong>{{$order->created_at or '无'}}</strong>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <h4>产品信息</h4>
            <table class="table table-bordered table-invoice-full">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>产品信息</th>
                    <th class="head1">产品优惠券</th>
                    <th class="head0 right">数量</th>
                    <th class="head0 right">产品备注</th>
                    <th class="head1 right">单价</th>
                    <th class="head0 right">总价</th>
                    <th class="head0 right">产品详情</th>
                </tr>
                </thead>
                <tbody>
                @if(!$order->orderProducts->isEmpty())
                    @foreach($order->orderProducts as $k=> $product)
                        <tr>
                            <td>{{++$k}}</td>
                            <td width="320px">
                                @if(!is_null($product->products))
                                    <div style="float: left;width: 100px;">
                                        @if(!is_null($product->products->productThumb))
                                            <img src="{{$path.$product->products->productThumb->thumb->norm[4]->uri}}">
                                        @else
                                            <img src="default.jpg" alt="">
                                        @endif
                                    </div>
                                    <div style="float: right">
                                        <p>
                                            尺寸：
                                            {{$product->products->phy_width}}x
                                            {{$product->products->phy_height}}x
                                            {{$product->products->phy_depth}}mm
                                        </p>
                                        <p>
                                            画框：
                                            @if(is_null($product->products->border))
                                                无
                                            @else
                                                {{$product->products->border->materialDefine->name}}
                                            @endif
                                        </p>
                                        <p>
                                            卡纸：
                                            @if($product->products->frame->isEmpty())
                                                无
                                            @else
                                                @foreach($product->products->frame as $k=>$frame)
                                                    {{$frame->materialDefine->name}}（第{{$k+1}}层）<br>
                                                @endforeach
                                            @endif
                                        </p>
                                        <p>
                                            画芯：
                                            @if(is_null($product->products->core))
                                                无
                                            @else
                                                {{$product->products->core->materialDefine->name}}
                                            @endif
                                        </p>
                                        <p>
                                            玻璃：
                                            @if(is_null($product->products->front))
                                                无
                                            @else
                                                {{$product->products->front->materialDefine->name}}
                                            @endif
                                        </p>
                                        <p>
                                            背板：
                                            @if(is_null($product->products->back))
                                                无
                                            @else
                                                {{$product->products->back->materialDefine->name}}
                                            @endif
                                        </p>
                                        <p>
                                            背饰：
                                            @if(is_null($product->products->backFacade))
                                                无
                                            @else
                                                {{$product->products->backFacade->materialDefine->name}}
                                            @endif
                                        </p>
                                    </div>
                                @else
                                    无数据
                                @endif
                            </td>
                            <td>
                                @if(!is_null($product->orderProductVoucher))
                                    @if(!is_null($product->orderProductVoucher->personVoucher))
                                        <strong>
                                            {{$product->orderProductVoucher->personVoucher->voucher->name}}
                                        </strong>
                                        <?php $official = $product->orderProductVoucher->personVoucher->voucher->is_universal;?>
                                        {{$official == true ? '（平台优惠券）': '（个人优惠券）'}}<br>
                                        <?php $type = $product->orderProductVoucher->personVoucher->voucher->voucher_type;?>
                                        {{$type == 0 ? "现金券 ": "折扣券 "}}<br>
                                        <strong>
                                            {{$product->orderProductVoucher->personVoucher->voucher->currency}}
                                            {{$product->orderProductVoucher->personVoucher->voucher->figure}}
                                        </strong>
                                        {{'[已使用 '.$product->orderProductVoucher->used_times.' 次]'}}<br>
                                    @endif
                                @else
                                    {{"无"}}
                                @endif
                            </td>
                            <td class="right">
                                @if(is_null($product))
                                    {{'无数据'}}
                                @else
                                    x{{$product->num}}
                                @endif
                            </td>
                            <td class="right">
                                {{$product->memo or '无'}}
                            </td>
                            @if(!is_null($product))
                                <td class="right">{{$product->price/$product->num.$product->currency}}</td>
                                <td class="right">{{$product->price.$product->currency}}</td>
                            @endif
                            <td class="right">
                                @if(!is_null($product->products))
                                    <a class="product-info" href="javascript:void(0);"
                                       data-id="{{$product->products->id}}">
                                        {{"查看详情"}}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="10">还没有数据！</td></tr>
                @endif
                </tbody>
            </table>
            <h4>订单金额</h4>
            <table class="table table-bordered table-invoice-full">
                <tbody>
                <tr>
                    <td class="right">
                        <strong>折扣价：{{$order->discount_price.$order->currency}}</strong><br>
                        <strong>总价：{{$order->price.$order->currency}}</strong>
                    </td>
                    <td class="right">
                        <strong>
                            @if(!is_null($order->orderShip))
                                <?php $ship = $order->orderShip->ship;?>
                                快递公司：{{is_null($ship->company)?'无':$ship->company->name}}<br>
                                是否包邮：{{$ship->is_free == true?'是':'否'}}<br>
                                快递费用：{{$ship->fee.$order->currency}}<br>
                            @else
                                快递信息：无
                            @endif
                        </strong>
                    </td>
                    <td class="right">
                        <strong>实际支付金额：
                            @if(!is_null($order->orderShip))
                                <?php $ship = $order->orderShip->ship;?>
                                @if($ship->is_free == true)
                                    {{$order->discount_price.$order->currency}}
                                @else
                                    {{$order->discount_price + $ship->fee}}{{$order->currency}}
                                @endif
                            @endif
                        </strong>
                    </td>
                </tr>
                </tbody>
            </table>
            <h4>申请退款记录</h4>
            <table class="table table-bordered table-invoice-full">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>申请时间</th>
                    <th class="head0">申请原因</th>
                    <th class="head1">申请状态</th>
                    <th class="head1">处理时间</th>
                    <th class="head0 right">审核结果</th>
                    <th class="head0 right">审核状态</th>
                    <th class="head0 right">审核人员</th>
                </tr>
                </thead>
                <tbody>
                @if(!$result->data->isEmpty())
                    @foreach($result->data as $k=> $refund)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>{{$refund->created_at}}</td>
                            <td>{{isset($reason)?$reason->reason:'无'}}</td>
                            @if(isset($handle))
                                @if(!is_null($handle->handleResult))
                                    <td>{{'已审核'}}</td>
                                    <td>{{$handle->created_at}}</td>
                                    <td>{{is_null($handle->handleResult->reason)?"无":$handle->handleResult->reason->reason}}</td>
                                    <td>{{$handle->handleResult->status == true? '通过' : '拒绝'}}</td>
                                    <td>{{$handle->operator_id}}</td>
                                @else
                                    <td>{{'未审核'}}</td>
                                    <td>{{'无'}}</td>
                                    <td>{{'无'}}</td>
                                    <td>{{'未审核'}}</td>
                                    <td>{{'无'}}</td>
                                @endif
                            @else
                                <td>{{'未审核'}}</td>
                                <td>{{'无'}}</td>
                                <td>{{'无'}}</td>
                                <td>{{'未审核'}}</td>
                                <td>{{'无'}}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="8">还没有退款申请记录！</td></tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endif
<script>
    $('.product-info').on('click', function(){
        var id = $(this).attr('data-id');
        ajaxData('get', 'new_pro/products/'+id, handleGetViewShowCallback, [], 'myProduct');
    });
</script>