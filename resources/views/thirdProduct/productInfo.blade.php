<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 10/29/16
 * Time: 3:33 PM
 */

use App\IekModel\Version1_0\Constants\Path;
$path = Path::FILE_PATH;

$data = null;
$introductions = null;
$isSell = null;
$noData = '无';
if(isset($result) && $result->isOk() && isset($result->data)) {
    $result = json_decode(json_encode($result));
    $data = $result->data;
    if(isset($data->is_sell)) {
        $isSell = $data->is_sell;
    }
    if(isset($data->productIntroduction)) {
        $introductions = $data->productIntroduction;
        if(isset($introductions)) {
            foreach ($introductions as $introduction) {
                if(isset($introduction->introduction->introductionContent)) {
                    $index = array();
                    foreach($introduction->introduction->introductionContent as $content){
                        $index[] = $content->index;
                    }
                    array_multisort($index, SORT_ASC, $introduction->introduction->introductionContent);
                }
            }
        }
    }
}

//dd(json_decode(json_encode($data)));
?>
@if($result->statusCode == 0)
    @if($data != null)
        <div class="alert alert-error alert-block alert-right">
            <a class="close" data-dismiss="alert" href="#">×</a>
            <h4 class="alert-heading">产品状态</h4>
            @if($data -> is_removed)
                <span class="label label-danger">已删除</span>
            @else
                <span class="label label-inverse">未删除</span>
            @endif
            @if($isSell === true)
                <span class="label label-success">已在售</span>
            @else
                <span class="label label-inverse">未在售</span>
            @endif
        </div>
        <div class="dialog-title">
            编号 : {{ $data->no or '无编号' }}
        </div>
        <br>
        <div class="dialog-content">
            <div class="group margin-bottom">
                <div class="group-left">
                    产品图 ：
                </div>
                <div class="group-right">
                    @if(isset($data->image))
                        <img src="{{ $path.$data->image->norms[2]->uri }}" />
                    @else
                        {{ $noData }}
                    @endif
                </div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">名字 :</div>
                <div class="group-right">{{ $data->name or $noData }}</div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">描述 :</div>
                <div class="group-right">{{ $data->description or $noData }}</div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">宽（cm）: </div>
                <div class="group-right">{{ $data->width or $noData }}</div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">高（cm）:</div>
                <div class="group-right">{{ $data->height or $noData }}</div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">装裱方式 :</div>
                <div class="group-right">{{ $data->mount or $noData }}</div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">画框 :</div>
                <div class="group-right">{{ $data->border or $noData }}</div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">卡纸 :</div>
                <div class="group-right">{{ $data->frame or $noData }}</div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">防尘玻璃 : </div>
                <div class="group-right">{{ $data->front or $noData }} </div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">产品等级 :</div>
                <div class="group-right">{{ $data->level or $noData }} <br> 0：表示特级，其他的是几就是几等级 </div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">是否在售 : </div>
                <div class="group-right">{{ $data->is_sell === true ? '是' : '否' }} </div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">商品地址 :</div>
                <div class="group-right">
                    @if(isset($data->uri))
                        <a href="{{ $data->uri }}" target="_blank">{{ $data->uri }}</a>
                    @else
                        {{ $noData }}
                    @endif
                </div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">店铺地址 :</div>
                <div class="group-right">
                    @if(isset($data->shop) && isset($data->shop->uri))
                        <a href="{{ $data->shop->uri }}" target="_blank">
                            {{ $data->shop->platform.'-' }}
                            {{ $data->shop->name.'-' }}
                            {{ $data->shop->uri }}
                        </a>
                    @else
                        {{ $noData }}
                    @endif
                </div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">画芯信息 :</div>
                <div class="group-right">
                    @if(isset($data->core))
                        <div class="group margin-bottom">
                            <div class="group-left">宽（cm） :</div>
                            <div class="group-right">{{ $data->core->width or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">高（cm） :</div>
                            <div class="group-right">{{ $data->core->height or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">生产方式 :</div>
                            <div class="group-right">{{ $data->core->pattern or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">材料 :</div>
                            <div class="group-right">{{ $data->core->material or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">墨水 :</div>
                            <div class="group-right">{{ $data->core->ink or $noData }}</div>
                        </div>
                    @else
                        {{ $noData }}
                    @endif
                </div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">作品信息 :</div>
                <div class="group-right">
                    @if(isset($data->core->publication))
                        <div class="group margin-bottom">
                            <div class="group-left">编号 :</div>
                            <div class="group-right">{{ $data->core->publication->no or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">名字 :</div>
                            <div class="group-right">{{ $data->core->publication->name or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">名字翻译 :</div>
                            <div class="group-right">{{ $data->core->publication->lang or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">描述 :</div>
                            <div class="group-right">{{ $data->core->publication->description or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">作品年代（年） :</div>
                            <div class="group-right">{{ $data->core->publication->year or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">宽（cm） :</div>
                            <div class="group-right">{{ $data->core->publication->width or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">高（cm） :</div>
                            <div class="group-right">{{ $data->core->publication->height or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">珍藏处 :</div>
                            <div class="group-right">
                                @if(isset($data->core->publication->museum))
                                    {{ $data->core->publication->museum->name.'-' }}
                                    {{ $data->core->publication->museum->lang }}
                                    @else
                                    {{ $noData }}
                                @endif
                            </div>
                        </div>
                    @else
                        {{ $noData }}
                    @endif
                </div>
            </div>
            <div class="group margin-bottom">
                <div class="group-left">作者信息 :</div>
                <div class="group-right">
                    @if(isset($data->core->publication->author))
                        <div class="group margin-bottom">
                            <div class="group-left">编号 :</div>
                            <div class="group-right">{{ $data->core->publication->author->no or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">姓名 :</div>
                            <div class="group-right">{{ $data->core->publication->author->name or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">姓名翻译 :</div>
                            <div class="group-right">{{ $data->core->publication->author->lang or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">描述 :</div>
                            <div class="group-right">{{ $data->core->publication->author->description or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">国籍 :</div>
                            <div class="group-right">{{ $data->core->publication->author->nationality or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">简介 :</div>
                            <div class="group-right">{{ $data->core->publication->author->introduction or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">名言 :</div>
                            <div class="group-right">{{ $data->core->publication->author->saying or $noData }}</div>
                        </div>
                        <div class="group margin-bottom">
                            <div class="group-left">艺术特色 :</div>
                            <div class="group-right">{{ $data->core->publication->author->feature or $noData }}</div>
                        </div>
                    @else
                        {{ $noData }}
                    @endif
                </div>
            </div>
        </div>
        <div class="dialog-content" >
            @if(isset($introductions))
                @foreach($introductions as $introduction)
                    @if(isset($introduction->introduction->type))
                    <div class="group margin-bottom">
                        <div class="group-left">
                            @if($introduction->introduction->type == 'scene')
                                产品场景
                            @elseif($introduction->introduction->type == 'craft')
                                产品工艺
                            @elseif($introduction->introduction->type == 'publication')
                                作品
                            @elseif($introduction->introduction->type == 'author')
                                作者
                            @else
                                {{ $noData }}
                            @endif
                            介绍 :
                        </div>
                        <div class="group-right">
                            @if(isset($introduction->introduction->introductionContent))
                                @foreach($introduction->introduction->introductionContent as $content)
                                    @if(!isset($content->image))
                                        <div>{{ $content->content or '' }}</div>
                                    @else
                                        <i>{{ $content->content or '' }}</i>
                                        @if(isset($content->image->norms))
                                            <div>
                                            @if(isset($content->image->norms[0]->uri))
                                                <img src="{{ $path.$content->image->norms[0]->uri }}" alt="">
                                            @else
                                                <img src="/img/default.png" alt="">
                                            @endif
                                            </div>
                                        @endif
                                    @endif
{{--                                    <div>{{ 'index: '.$content->index }}</div>--}}
                                @endforeach
                            @else
                                {{ $noData }}
                            @endif
                        </div>
                    </div>
                    @endif
                @endforeach
            @else
                没有任何介绍
            @endif
        </div>
        <div class="dialog-footer">
            <button class="btn btn-danger" name="sell-product" isSell="{{ json_encode($data->is_sell) }}" productId="{{ $data->id }}">
                {{ $data->is_sell ? '下架' : '上架'}}
            </button>
            <button class="btn btn-warning" name="delete-product" isDelete="{{ json_encode($data->is_removed) }}" productId="{{$data->id}}">
                {{ $data->is_removed ? '恢复' : '删除'}}
            </button>
            <button class="btn btn-primary" name="modify-product" productId="{{ $data->id }}">
                修改
            </button>
        </div>
    @endif
@elseif($result->statusCode == 21001)
    @include('message.messageAlert',['type'=>'error','message'=>'无效的产品ID'])
@endif
<script>
    $(function() {
        //sell product.
        $('.btn[name="sell-product"]').on('click', sellProduct);
        //delete product.
        $('.btn[name="delete-product"]').on('click', deleteProduct);
        //modify product.
        $('.btn[name="modify-product"]').on('click', modifyProduct);
    });
    function modifyProduct(event) {
        eventUtil.preventDefault(event);
        var $this = $(this);
        var productId = $this.attr('productId');
        if(isNull(productId)) {
            messageAlert({
                message: '产品ID为空，请刷新重试',
                type: 'error'
            });
        } else {
            var menuEditLi = $('#tb-product-edit');
            menuEditLi.parent().trigger('click');
            var path = originPath();
            var url = path + 'products/' + productId +'/edit';
            var $modal = $this.closest('.dialog-lg');
            $modal.find('.remove').trigger('click');
            ajaxData('get', url, appendViewToContainer, [], {
                init: {
                    file: false
                }
            });
        }
    }
    function sellProduct() {
        loadingShow();
        var $this = $(this);
        var isSell = $this.attr('isSell');
        var productId = $this.attr('productId');
        var params = {};
        params.$btn = $this;
        params.productId = productId;
        params.data = {};
        if(isSell == 'true') {
            params.data.isSell = false;
        } else {
            params.data.isSell = true;
        }
        if(isNull(productId)) {
            sellProductError();
            return false;
        }
        var path = originPath();
        var url = path + 'products/' + productId +'/isSell';
        ajaxData('put', url, handleSellProduct, [], params, sellProductError);
    }

    function handleSellProduct(result, params) {
        if(isOk(result)) {
            messageAlert({
                message: '修改产品在售状态成功',
                type: 'success'
            });
            var isSell = params.data.isSell;
            var text = '上架';
            var label = '未在售';
            if(isSell == true) {
                text = '下架';
                label = '已在售';
            }
            var productId = params.productId;
            var $btn = params.$btn;
            $btn.attr('isSell', isSell).text(text);
            var $label = $btn.parent().siblings('.remove').next().find('.label').eq(-1);
            var $coverLabel = $('.data-list[name="product-list"]').find('.thumbnail[data="'+ productId +'"]').find('.label');
            if(!$coverLabel.hasClass('label-danger')) {
                $label.push($coverLabel[0]);
            }
            if($label.hasClass('label-inverse')) {
                $label.removeClass('label-inverse').addClass('label-success');
            } else {
                $label.addClass('label-inverse').removeClass('label-success');
            }
            $label.text(label);
            loadingHide();
        } else {
            sellProductError();
        }
    }

    function sellProductError() {
        loadingHide();
        messageAlert({
            message: '修改产品在售状态失败',
            type: 'error'
        })
    }

    function deleteProduct() {
        loadingShow();
        var $this = $(this);
        var isDelete = $this.attr('isDelete');
        var productId = $this.attr('productId');
        var params = {};
        params.$btn = $this;
        params.productId = productId;
        params.data = {
            ids: [productId]
        };
        if(isNull(productId)) {
            sellProductError();
            return false;
        }
        var method = 'post';
        var path = originPath();
        var url = path + 'products/';
        if(isDelete == 'true') {
            params.isDelete = false;
            url += 'recover';
        } else {
            params.isDelete = true;
            url += 'del';
            method = 'delete';
        }
        ajaxData(method, url, handleDeleteProduct, [], params, deleteProductError);
    }

    function handleDeleteProduct(result, params) {
        if(isOk(result)) {
            messageAlert({
                message: '修改产品删除状态成功',
                type: 'success'
            });
            var isDelete = params.isDelete;
            var text = '删除';
            var label = '未删除';
            if(isDelete == true) {
                text = '恢复';
                label = '已删除';
            }
            var productId = params.productId;
            var $btn = params.$btn;
            $btn.attr('isDelete', isDelete).text(text);
            var $label = $btn.parent().siblings('.remove').next().find('.label').eq(0);
            var $coverLabel = $('.data-list[name="product-list"]').find('.thumbnail[data="'+ productId +'"]').find('.label');
            $label.push($coverLabel[0]);
            if($label.hasClass('label-inverse')) {
                $label.removeClass('label-inverse label-success').addClass('label-danger');
            } else {
                $label.addClass('label-inverse').removeClass('label-danger label-success');
            }
            $label.text(label);
            loadingHide();
        } else {
            deleteProductError();
        }
    }

    function deleteProductError() {
        loadingHide();
        messageAlert({
            message: '修改产品删除状态失败',
            type: 'error'
        })
    }
</script>