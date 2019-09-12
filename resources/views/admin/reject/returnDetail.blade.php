<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/11
 * Time: 10:45
 */
use App\IekModel\Version1_0\Constants\Path;
$path = Path::FILE_PATH;
$reject = $result->data;
?>
@if(!is_null($reject))
    <div class="data-list clearfix">
        <div class="widget-box">
            @if(!$reject->rejectProducts->isEmpty())
                @foreach($reject->rejectProducts as $product)
                    @if(!is_null($product))
                        <div class="widget-content">
                            <div class="new-update clearfix form-horizontal">
                                <div class="control-group">
                                    <div class="control-label">
                                        <label>退换产品类型：</label>
                                    </div>
                                    <div class="controls">
                                        @if(!is_null($product->products->productDefine))
                                            {{$product->products->productDefine->name or null}}
                                        @endif
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">退换产品数量：</label>
                                    <div class="controls">
                                        {{$product->num or null}}
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">退换产品图：</label>
                                    <div class="controls" style="width:200px;height: 200px;">
                                        <img src="{{$path.$product->products->productThumb->thumb->norm[4]->uri}}">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">用户退换原因：</label>
                                    <div class="controls">
                                        @if(!is_null($product->reason))
                                            {{$product->reason->reason or null}}
                                        @endif
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">用户上传实际照片：</label>
                                    <div class="controls">
                                        @if(!$product->rejectRequestImages->isEmpty())
                                            @foreach($product->rejectRequestImages as $image)
                                                @if(!$image->images->isEmpty())
                                                    <img src="{{$path.$image->images[4]->uri}}">
                                                @endif
                                            @endforeach
                                        @else
                                            <code>用户未上传图片</code>
                                        @endif
                                    </div>
                                </div>
                                @if($result->audit == 'true')
                                    @if(!is_null($product->rejectHandle))
                                        <div class="control-group">
                                            <label class="control-label">审核管理员：</label>
                                            <div class="controls">
                                                {{$product->rejectHandle->operator_id or null}}
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">审核状态：</label>
                                            <div class="controls">
                                                @if(!is_null($product->rejectHandle->rejectHandleResult))
                                                    {{ $product->rejectHandle->rejectHandleResult->status == 0 ?"拒绝":"同意"}}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">是否回收：</label>
                                            <div class="controls">
                                                @if(!is_null($product->rejectHandle->rejectHandleResult))
                                                    {{$product->rejectHandle->rejectHandleResult->is_recycling == true ? "回收":"不回收"}}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">审核结果：</label>
                                            <div class="controls">
                                                @if(!is_null($product->rejectHandle->rejectHandleResult->reason))
                                                    {{ $product->rejectHandle->rejectHandleResult->reason->reason }}
                                                @else
                                                    {{"无"}}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">审核时间：</label>
                                            <div class="controls">
                                                {{$product->rejectHandle->rejectHandleResult->created_at or null}}
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                <hr>
                                @if(!is_null($reject->order))
                                    @if(!is_null($reject->order->orderReceiveInformation))
                                        <div class="control-group">
                                            <label class="control-label">收货人姓名：</label>
                                            <div class="controls">
                                                @if(!$reject->order->orderReceiveInformation->receiveInformation->name->isEmpty())
                                                    @foreach($reject->order->orderReceiveInformation->receiveInformation->name as $name)
                                                        {{$name->first_name.$name->middle_name.$name->last_name}}
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">联系电话：</label>
                                            <div class="controls">
                                                @if(!$reject->order->orderReceiveInformation->receiveInformation->phone->isEmpty())
                                                    @foreach($reject->order->orderReceiveInformation->receiveInformation->phone as $phone)
                                                        {{$phone->phone}}
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">收货地址：</label>
                                            <div class="controls">
                                                @if(!$reject->order->orderReceiveInformation->receiveInformation->address->isEmpty())
                                                    @foreach($reject->order->orderReceiveInformation->receiveInformation->address as $address)
                                                        {{$address->city->merge_name}},{{$address->address}}
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <h4>申请退换记录</h4>
                            <table class="table table-bordered table-invoice-full">
                                <thead>
                                <tr>
                                    <th>申请时间</th>
                                    <th>申请原因</th>
                                    <th>申请状态</th>
                                    <th>处理时间</th>
                                    <th>处理内容</th>
                                    <th>处理状态</th>
                                    <th>处理人</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$product->created_at}}</td>
                                    <td>
                                        @if(is_null($product->reason))
                                            {{$product->reason->reason or null}}
                                        @else
                                            无
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_null($reject->result))
                                            {{"未审核"}}
                                        @endif
                                        @if($reject->result === 0)
                                            {{"通过"}}
                                        @endif
                                        @if($reject->result === 1)
                                            {{"拒绝"}}
                                        @endif
                                    </td>
                                    @if(!is_null($product->rejectHandle))
                                        <td>{{$product->rejectHandle->created_at or "无"}}</td>
                                        @if($product->rejectHandle->rejectHandleResult != null)
                                            <?php $result = $product->rejectHandle->rejectHandleResult;?>
                                            <td>{{is_null($result->reason)?"无":$result->reason->reason}}</td>
                                            <td>
                                                @if($result->status == 0)
                                                    {{"通过"}}
                                                @else
                                                    {{"拒绝"}}
                                                @endif
                                            </td>
                                        @endif
                                        <td>{{$product->rejectHandle->operator_id or "无"}}</td>
                                    @else
                                        <td>未处理</td>
                                        <td>未处理</td>
                                        <td>未处理</td>
                                        <td>未处理</td>
                                    @endif
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
@endif
