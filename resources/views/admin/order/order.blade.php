<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/16
 * Time: 19:53
 */
use App\IekModel\Version1_0\Constants\Path;
$path    = Path::FILE_PATH;
$type = isset($result->type) ? $result->type : null;
$url = isset($type) ? null : $type;
$order = $err->data;

if(!is_null($order)){
    $receiveInfo = $order->orderReceiveInformation;
    if(!is_null($receiveInfo) && !is_null($receiveInfo->receiveInformation)){
        $names = $receiveInfo->receiveInformation->name;
        $addresses = $receiveInfo->receiveInformation->address;
        $phones = $receiveInfo->receiveInformation->phone;
    }
    $orderProducts = $order->orderProducts;
    $orderVoucher = $order->orderPersonVoucher;
    $personOrder = $order->personOrder;
    $orderStatus = $order->orderStatus;
    foreach($orderStatus as $status){
        if($status->is_current == true){
            $sta = $status->status->name;
            $reason = $status->reason;
        }
    }
    $scores = $order->score;
    $comments = $order->orderComment;
}
$reasons = $err->reasons;
?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-briefcase"></i> </span>
                    <h5>订单详情</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span6">
                            <table class="">
                                <tbody>
                                <tr>
                                    <td><h4>收货人信息</h4></td>
                                </tr>
                                <tr>
                                    <td>收货人姓名：</td>
                                    @if(isset($names) && !$names->isEmpty())
                                        @foreach($names as $name)
                                            <td>{{$name->first_name}}{{$name->middle_name}}{{$name->last_name}}</td>
                                        @endforeach
                                    @else
                                        <td>未填写</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>收货地址：</td>
                                    @if(isset($addresses) && !$addresses->isEmpty())
                                        @foreach($addresses as $address)
                                            <td>
                                                {{$address->city->merge_name}},{{$address->address}}
                                            </td>
                                        @endforeach
                                    @else
                                        <td>未填写</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>联系电话：</td>
                                    @if(isset($phones) && !$phones->isEmpty())
                                        @foreach($phones as $phone)
                                            <td>{{$phone->phone}}</td>
                                        @endforeach
                                    @else
                                        <td>未填写</td>
                                    @endif
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="span6">
                            <table class="table table-bordered table-invoice">
                                <tbody>
                                <tr>
                                    <td class="width30">订单号：</td>
                                    <td class="width70"><strong>{{$order->order_no}}</strong></td>
                                </tr>
                                <tr>
                                    <td class="width30">订单创建人：</td>
                                    <td class="width70">
                                        @if(isset($personOrder) && !is_null($personOrder))
                                            <a href="javascript:void (0);" id="person" data="{{$personOrder->person_id}}">
                                                @foreach($personOrder->person->personNick as $nick)
                                                    @if($nick->is_active == true)
                                                        {{$nick->nick->nick}}
                                                    @endif
                                                @endforeach
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>订单优惠券：</td>
                                    <td>
                                        @if(isset($orderVoucher) && !$orderVoucher->isEmpty())
                                            @foreach($orderVoucher as $opv)
                                                <?php
                                                if(!is_null($opv->personVoucher)){
                                                    $voucher = $opv->personVoucher->voucher;
                                                    if(!is_null($voucher)){
                                                        $official = $voucher->is_official;
                                                        $type = $voucher->voucher_type;
                                                        $currency = $voucher->currency;
                                                        $figure = $voucher->figure;
                                                    }
                                                }
                                                ?>
                                                <strong>{{isset($voucher) && !is_null($voucher) ? $voucher->name:"无"}}</strong>
                                                {{isset($official)?$official === true ? '（平台优惠券）': '（个人优惠券）':""}}<br>
                                                {{isset($type)?$type === 0 ? "现金券 ": "折扣券 ":""}}<br>
                                                <strong>
                                                    {{isset($figure)?$figure:''}}
                                                    {{isset($currency)?$currency:''}}
                                                </strong>
                                                {{'[已使用 '.$opv->used_times.' 次]'}}<br>
                                            @endforeach
                                        @else
                                            {{'无数据'}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>快递单号：</td>
                                    @if(!is_null($order->orderShip))
                                        <td>
                                            <strong>
                                                {{is_null($order->orderShip->ship->no)?"无":$order->orderShip->ship->no}}
                                            </strong>
                                        </td>
                                    @else
                                        <td>无数据</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>订单状态：</td>
                                    @if(!$order->orderStatus->isEmpty())
                                        @foreach($order->orderStatus as $status)
                                            @if($status->is_current)
                                                <td>
                                                    <strong>
                                                        {{\App\IekModel\Version1_0\IekModel::strTrans($status->status->name,'order')}}
                                                    </strong>
                                                </td>
                                            @endif
                                        @endforeach
                                    @else
                                        <td>无数据</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="width30">订单创建时间：</td>
                                    <td class="width70"><strong>{{$order->created_at}}</strong></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <table class="table table-bordered table-invoice-full">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>产品信息</th>
                                    <th>产品优惠券</th>
                                    <th>数量</th>
                                    <th>产品备注</th>
                                    <th>单价</th>
                                    <th>总价</th>
                                    @if($sta == 'success')
                                        <th>评价</th>
                                    @endif
                                    <th>产品详情</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($orderProducts) && !$orderProducts->isEmpty())
                                    @foreach($orderProducts as $k => $product)
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
                                            <td>
                                                @if(is_null($product))
                                                    {{'无数据'}}
                                                @else
                                                    x{{$product->num}}
                                                @endif
                                            </td>
                                            <td>
                                                {{$product->memo or '无'}}
                                            </td>
                                            @if(!is_null($product))
                                                <td>{{$product->price/$product->num.$product->currency}}</td>
                                                <td>{{$product->price.$product->currency}}</td>
                                            @endif
                                            @if($sta == 'success')
                                                <td>
                                                    @if(isset($comments) && !$comments->isEmpty())
                                                        @foreach($comments as $comment)
                                                            @if($comment->product_id === $product->product_id)
                                                                @if(!$comment->comment->isEmpty())
                                                                    @foreach($comment->comment as $com)
                                                                        <div>
                                                                        @if($com->content_type == 0)
                                                                            @if(!is_null($com->text))
                                                                                <span name="forbidden-content">{{$com->text->content}}</span>
                                                                                <button class="btn btn-danger btn-mini" type="order-comment-text"
                                                                                        data-type="{{ $com->text->is_forbidden ? 'unForbidden' : 'forbidden'}}" data="{{ $com->text->id  }}">
                                                                                    {{$com->text->is_forbidden ? '取消禁止' : '禁止'}}
                                                                                </button>
                                                                                @if($com->text->is_forbidden == true)
                                                                                    <a href="javascript:void(0);" class="seeReason" data="seeCommentText/{{$com->text->id}}" >查看原因</a>
                                                                                @endif
                                                                            @endif
                                                                        @endif
                                                                        </div>
                                                                        <div>
                                                                        @if($com->content_type == 1)
                                                                            @if(!is_null($com->image))
                                                                                <span>
                                                                                    <img src="{{$path.$com->image->norms[4]->uri}}">
                                                                                </span>
                                                                                    <button class="btn btn-danger btn-mini" type="order-comment-image"
                                                                                            data-type="{{ $com->image->is_forbidden ? 'unForbidden' : 'forbidden'}}" data="{{ $com->image->id  }}">
                                                                                        {{$com->image->is_forbidden ? '取消禁止' : '禁止'}}
                                                                                    </button>
                                                                                    @if($com->image->is_forbidden == true)
                                                                                        <a href="javascript:void(0);" class="seeReason" data="seeCommentImage/{{$com->image->id}}" >查看原因</a>
                                                                                    @endif
                                                                            @endif
                                                                        @endif
                                                                        </div>
                                                                    @endforeach
                                                                    <a name="reply-comment" href="javascript:void(0);"
                                                                       data="{{$comment->id}}" class="btn btn-primary">回复</a>

                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{'无'}}
                                                    @endif
                                                </td>
                                            @endif
                                            <td>
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
                                    <tr>
                                        <td rowspan="9"><code>没有产品信息</code></td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            <table class="table table-bordered table-invoice-full">
                                <tbody>
                                <tr>
                                    @if(!is_null($order->orderShip))
                                        <?php
                                        if(!is_null($order->orderShip->ship)){
                                            $is_free = $order->orderShip->ship->is_free;
                                            if($is_free == true){
                                                $fee = 0;
                                            }else{
                                                $fee = $order->orderShip->ship->fee;
                                            }
                                        }else{
                                            $fee =  "无";
                                        }
                                        ?>
                                        <td class="right">
                                            <strong>
                                                快递公司：{{is_null($order->orderShip->ship->company)?'无':$order->orderShip->ship->company->name}}<br>
                                                是否包邮：{{$is_free == true?'是':'否'}}<br>
                                                快递费用：{{$fee.$order->currency}}
                                            </strong>
                                        </td>
                                    @else
                                        <td class="right">无数据</td>
                                    @endif
                                    <td class="right">
                                        <code>用户备注：{{$order->memo or '无'}}</code><br>
                                        <code>系统备注：{{$order->platformMemo->memo or '无'}}</code>
                                    </td>
                                    <td class="right"><strong>支付方式：
                                            @if($order->orderPay != null)
                                                @if($order->orderPay->thirdPay != null)
                                                    {{ "第三方支付" }}
                                                    <p>支付状态： {{ $order->orderPay->thirdPay->status == false ? "支付失败":"支付成功" }}</p>
                                                    {{--<input type="hidden" id="pay-type" value="aliPay">--}}
                                                @elseif($order->orderPay->wealthPay != null)
                                                    {{ "平台钱包支付" }}
                                                    {{--<input type="hidden" id="pay-type" value="walletPay">--}}
                                                @else
                                                    {{ "未支付" }}
                                                @endif
                                            @else
                                                {{ "未支付" }}
                                            @endif
                                        </strong></td>
                                    <td class="right">
                                        <strong>
                                            折扣价：{{$order->discount_price}}{{$order->currency}}
                                        </strong><br>
                                        <strong>总价：{{$order->price.$order->currency}}</strong>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="pull-right">
                                <h4><span>
                                        实付金额：
                                        @if($sta == 'waitPay')
                                            0
                                        @elseif($is_free == true)
                                            {{$order->discount_price.$order->currency}}
                                        @else
                                            {{$order->discount_price + $fee}}{{$order->currency}}
                                        @endif
                                </span></h4>
                                <br>
                                @if($sta == 'waitProduct')
                                    @if(!$order->refundRequest->isEmpty())
                                        <?php $handle = $order->refundRequest[0]->refundRequestHandle;?>
                                        @if(is_null($handle))
                                            <code>已申请退款</code>
                                        @elseif(!is_null($handle) && $handle->handleResult->status == true)
                                            <code>退款申请已通过</code>
                                        @elseif(!is_null($handle) && $handle->handleResult->status == false)
                                            <a class="btn btn-primary btn-large pull-right" id="check-material" data="{{$order->id}}" href="javascript:void(0);">{{ "查看生产材料" }}</a>
                                        @endif
                                    @else
                                        <a class="btn btn-primary btn-large pull-right" id="check-material" data="{{$order->id}}" href="javascript:void(0);">{{ "查看生产材料" }}</a>
                                    @endif
                                @elseif($sta == 'producing')
                                    <a class="btn btn-primary btn-large pull-right" id="product-complete" data="{{$order->id}}" href="javascript:void(0);">{{ "生产完成" }}</a>
                                @elseif($sta == 'waitAccept')
                                    <a class="btn btn-primary btn-large pull-right" id="accept-product" data="{{$order->id}}" href="javascript:void(0);">{{ "验收产品" }}</a>
                                @elseif($sta == 'waitSend')
                                    <a class="btn btn-primary btn-large pull-right" id="send-goods" data="{{$order->id}}" href="javascript:void(0);">{{ "去发货" }}</a>
                                @elseif($sta == 'close')
                                    <h5>关闭原因：</h5>
                                        @if(isset($reason) && $reason != null)
                                            <code>{{$reason->reason}}</code><br>
                                            {{-- @if($reason->reason == '材料停产' && $order->orderPay !== null && $order->orderPay->orderReturnPay == null)
                                                 <a class="btn btn-primary pull-right" id="refund-order"
                                                    data="{{$order->id}}" href="javascript:void(0);">退款</a>
                                             @else--}}
                                            {{--@if(!is_null($order->orderPay) && !is_null($order->orderPay->orderReturnPay))
                                                <code>材料停产(已退款)</code>
                                            @endif--}}
                                        @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="widget-content">
                    @if($sta == 'success')
                        <h4>订单评价</h4>
                        @if(isset($scores) && !$scores->isEmpty())
                            <div style="padding-left: 10px;">
                                @foreach($scores as $score)
                                    <div class="control-group">
                                        @if($score->type == '0')
                                            <label class="control-label">服务态度</label>
                                            <div style="margin-left: 40px;">
                                                <span>分值：{{$score->score}}</span>
                                                @if(!is_null($score->reason))
                                                    <p>原因：{{$score->reason->reason}}</p>
                                                @endif
                                            </div>
                                        @endif
                                        @if($score->type == '1')
                                            <label class="control-label">物流速度</label>
                                            <div style="margin-left: 40px;">
                                                <span>分值：{{$score->score}}</span>
                                                @if(!is_null($score->reason))
                                                    <p>原因：{{$score->reason->reason}}</p>
                                                @endif
                                            </div>
                                        @endif
                                        @if($score->type == '2')
                                            <label class="control-label">商品质量</label>
                                            <div style="margin-left: 40px;">
                                                <span>分值：{{$score->score}}</span>
                                                @if(!is_null($score->reason))
                                                    <p>原因：{{$score->reason->reason}}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @extends('layout/reason')
                        @else
                            {{'订单还未评价！'}}
                        @endif
                    @endif
                    <div class="refund-result"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.product-info').on('click', function(){
        var id = $(this).attr('data-id');
        ajaxData('get', 'new_pro/products/'+id, handleGetViewShowCallback, [], 'myProduct');
    });
    $('#person').on('click',function(){
        var id = $(this).attr('data');
        bootstrapQ.dialog({
            type: 'get',
            url: 'persons/'+id,
            title: '用户详情',
            className: 'modal-lg',
            foot: false
        });
    });
    //已完成订单回复评论专用
    $('a[name="reply-comment"]').on('click',function(){
        var id = $(this).attr('data');
        bootstrapQ.alert({
            msg: '<div class="control-group">'+
            '<div class="control-label">'+
            '<label>回复内容：</label></div>'+
            '<textarea id="reply-text" rows="3" cols="10"></textarea>'+
            '</div><div class="control-group">'+
            '<div class="control-label">'+
            '<label>上传图片：</label></div>'+
            '<input id="reply-image" type="file" />'+
            '</div>',
            title: '回复'
        },function(){
            var text = $('#reply-text').val();
            if(isNull(text)){text=0;}
            var file = document.getElementById('reply-image').files[0];
            if(isUndefined(file)){file=1;}
            var formData = new FormData();
            formData.append('content',text);
            formData.append('fileName',file);
            $.ajax({
                url: 'reply/'+id,//传向后台服务器文件
                type: 'post',    //传递方法
                data: formData,  //传递的数据
                dataType : 'json',  //传递数据的格式
                async:false, //这是重要的一步，防止重复提交的
                cache: false,  //设置为false，上传文件不需要缓存。
                contentType: false,//设置为false,因为是构造的FormData对象,所以这里设置为false。
                processData: false,//设置为false,因为data值是FormData对象，不需要对数据做处理。
                success: function (responseStr) {
                    console.log(responseStr)
                    if(!isNull(responseStr)){
                        if(responseStr.statusCode == 0 ){
                            //form.find('img').attr('data-id', responseStr.data.id);
                            messageAlert({
                                message : responseStr.message,
                                type : 'success'
                            });
                        }else{
                            messageAlert({
                                message : responseStr.message,
                                type : 'error'
                            });
                        }
                    }
                },
                error: function () {
                    messageAlert({
                        message : "上传出错",
                        type : 'error'
                    });
                }
            });
            /*var params = {};
            params.data = {};
            params.data.content = text;
            params.data.type = 0;
            ajaxData('post', 'reply/'+id, function (result) {
                if(!isNull(result)) {
                    $('#container').append(result);
                }
            }, [], params);*/
        });
    });
    //待生产订单专用
    $('#check-material').on('click',function(){
        var id = $(this).attr('data');
        bootstrapQ.dialog({
            type : 'get',
            url : 'toCheck/'+id,
            title : '审核材料'
        },function(){
            var statusName = $('input[name="status"]:checked').val();
            var reason = $('#reason').val();
            if(isNull(reason)){reason=null;}
            var params = {};
            params.data = {};
            params.data.statusName = statusName;
            params.data.reason = reason;
            ajaxData('post', 'checkMaterial/'+id, function (result) {
                if(!isNull(result)) {
                    $('#container').append(result);
                }
            }, [], params);
        })
    });
    //生产中订单专用
    $('#product-complete').on('click',function(){
        var id = $(this).attr('data');
        var params = {};
        ajaxData('post', 'checkProduce/'+id, function (result) {
            if(!isNull(result)) {
                $('#container').append(result);
            }
        }, [], params);
    });
    //待验收订单专用
    $('#accept-product').on('click',function(){
        var id = $(this).attr('data');
        bootstrapQ.dialog({
            type : 'get',
            url : 'toAccept',
            title : '验收产品'
        },function(){
            var statusName = $('input[name="status"]:checked').val();
            var reason = $('#reason').val();
            if(isNull(reason)){reason=null;}
            var params = {};
            params.data = {};
            params.data.statusName = statusName;
            params.data.reason = reason;
            ajaxData('post', 'accept/'+id, function (result) {
                if(!isNull(result)) {
                    $('#container').append(result);
                }
            }, [], params);
        })
    });
    //待发货订单专用
    $('#send-goods').on('click',function(){
        var id = $(this).attr('data');
        bootstrapQ.dialog({
            type : 'get',
            url : 'toSend',
            title : '填写发货信息'
        },function(){
            var no = $('#ship-no').val();
            var costFee = $('#cost-fee').val();
            var providerId = $('#company').find('option:selected').val();
            var statusName = 'waitConfirm';
            var params = {};
            params.data = {};
            params.data.statusName = statusName;
            params.data.no = no;
            params.data.costFee = costFee;
            params.data.providerId = providerId;
            ajaxData('post', 'send/'+id, function (result) {
                if(!isNull(result)) {
                    $('#container').append(result);
                }
            }, [], params);
        })
    });
    $('.seeReason').on('click',function(){
        var url = $(this).attr('data');
        bootstrapQ.dialog({
            type: 'get',
            url: url,
            title: '被禁原因'
        });
    });
</script>