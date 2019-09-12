<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 10:48 AM
 */

use App\IekModel\Version1_0\Constants\Path;
$path = Path::FILE_PATH;

$result = isset($result) ? $result : null;
$publications = null;
$introductions = null;
$shops = null;
$product = null;
$productIntroductions = null;
if(isset($result) && isset($result->data)) {
    $result = json_decode(json_encode($result));
    $data = $result->data;
    if(isset($data->publications)) {
        $publications = $data->publications;
    }
    if(isset($data->introductions)) {
        $introductions = $data->introductions;
    }
    if(isset($data->shops)) {
        $shops = $data->shops;
    }
    if(isset($data->product)) {
        $product = $data->product;
        if(isset($product->productIntroduction)) {
            $productIntroductions = $product->productIntroduction;
        }
    }
}

$borders = ['PS框', '实木框'];
$cores = ['特一级宣纸', '进口涂层油画布', '典藏冷压纸'];
$coatings = ['亮光', '亮光+肌理', '哑光', '哑光+肌理'];

//dd($result);
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box collapsible" id="product-set-widget" productId="{{ $product->id or '' }}">
            <div class="widget-title">
                <a href="#collapseProduct" data-toggle="collapse" class="clearfix"><span class="icon"><i class="icon-edit"></i></span>
                    <h5>
                        产品信息
                        @include('layout/required')
                    </h5>
                </a>
            </div>
            <div class="collapse{{-- in--}}" id="collapseProduct">
                <div class="widget-content nopadding clearfix">
                    {{--产品信息--}}
                    <form class="form-horizontal span6" name="product">
                        <div class="control-group">
                            <label class="control-label"><span class="label label-inverse">产品信息</span></label>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>图片 :</label>
                            <div class="controls" name="imageId">
                                <a href="javascript:void(0)" class="btn btn-primary upload-file">添加/替换图片<input type="file"></a>
                                @if(isset($product->image_id) && isset($product->image->norms))
                                    <img imageId="{{ $product->image_id }}" src="{{ $path.$product->image->norms[2]->uri }}" alt="">
                                @endif
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>产品编号 :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="产品编号" name="no" required
                                       value="{{ $product->no or '' }}"
                                        {{ isset($product->no) ? 'disabled' : '' }} />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>名字 :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="名字" name="name" required
                                       value="{{ $product->name or '' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">描述 :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="描述" name="description"
                                       value="{{ $product->description or '' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>宽（cm） :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="宽（cm）" name="width" data-type="number" required
                                       value="{{ $product->width or '' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>高（cm） :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="高（cm）" name="height" data-type="number" required
                                       value="{{ $product->height or '' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>装裱方式 :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="装裱方式" name="mount" required
                                       value="{{ $product->mount or '框画' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">画框 :</label>
                            <div class="controls">
                                <select name="border">
                                    @if(isset($borders))
                                        @foreach($borders as $border)
                                            <option value="{{ $border }}"
                                                    {{ isset($product->border) && $product->border === $border ? 'selected' : '' }}>
                                                {{ $border }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                {{--<input type="text" class="span11" placeholder="画框" name="border" />--}}
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">卡纸 :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="卡纸" name="frame"
                                       value="{{ $product->frame or '' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">防尘玻璃 :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="防尘玻璃" name="front"
                                       value="{{ $product->front or '' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">背板 :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="背板" name="back"
                                       value="{{ $product->back or 'HDF板+木纹贴面' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">产品等级 :</label>
                            <div class="controls">
                                <input type="number" class="span11" placeholder="产品等级" name="level" min="0" data-type="int"
                                       value="{{ $product->level or '0' }}" />
                                <span class="help-block">0：表示特级，其他的是几就是几等级</span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>是否在售 :</label>
                            <div class="controls">
                                <label class=""><input type="radio" name="isSell" style="opacity: 0;" value="0"
                                    {{ (!isset($product->is_sell) || $product->is_sell == true) ? 'checked' : '' }}>是</label>
                                <label class=""><input type="radio" name="isSell" style="opacity: 0;" value="1"
                                    {{ (isset($product->is_sell) && $product->is_sell == false) ? 'checked' : '' }}>否</label>
                                {{--<input type="text" class="span11" placeholder="是否在售" name="isSell" required />--}}
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">商品地址 :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="商品地址" name="uri"
                                       value="{{ $product->uri or '' }}" />
                                <span class="help-block">请必须加上 “ http:// ” 。</span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">店铺地址 :</label>
                            <div class="controls">
                                <select name="shop-id">
                                    @if(isset($shops))
                                        @foreach($shops as $shop)
                                            @if(isset($product->shop_id) && $product->shop_id === $shop->id)
                                                <option value="{{ $shop->id }}" selected>
                                            @else
                                                <option value="{{ $shop->id }}">
                                            @endif
                                                {{ $shop->platform }}
                                                -
                                                {{ $shop->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="help-block"><a href="javascript:void(0);" class="link" name="add-shop">添加店铺</a></span>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal span6" name="core">
                        <div class="control-group">
                            <label class="control-label"><span class="label label-inverse">画芯信息</span></label>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>宽（cm） :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="宽（cm）" name="width" data-type="number" required
                                       value="{{ $product->core->width or '' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>高（cm） :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="高（cm）" name="height" data-type="number" required
                                       value="{{ $product->core->height or '' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>生产工艺 :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="生产方式" name="pattern" required
                                       value="{{ $product->core->pattern or 'Giclee博物馆收藏级复制工艺' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>材料 :</label>
                            <div class="controls">
                                <select name="material">
                                    @if(isset($cores))
                                        @foreach($cores as $core)
                                            <option value="{{ $core }}"
                                                    {{ (isset($product->core->material) && $product->core->material === $core) ? 'selected' : '' }}>
                                                {{ $core }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                {{--<input type="text" class="span11" placeholder="材料" name="material" required />--}}
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>墨水 :</label>
                            <div class="controls">
                                <input type="text" class="span11" placeholder="墨水" name="ink" required
                                       value="{{ $product->core->ink or 'EPSON原装进口墨水' }}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span class="text-important">*</span>作品 :</label>
                            <div class="controls">
                                <select name="publication-id">
                                    @if(isset($publications))
                                        @foreach($publications as $publication)
                                            @if(isset($product->core->publication_id) && $publication->id === $product->core->publication_id)
                                                <option value="{{ $publication->id }}" selected>
                                            @else
                                                <option value="{{ $publication->id }}">
                                            @endif
                                                {{ $publication->no }}
                                                -
                                                {{ $publication->name }}
                                                {{ $publication->lang }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="help-block"><a href="javascript:void(0);" class="link" name="add-publication">添加作品</a></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="widget-title">
                <a href="#produceData" data-toggle="collapse" class="clearfix"> <span class="icon"><i class="icon-edit"></i></span>
                    <h5>生产数据录入</h5>
                </a>
            </div>
            <div class="collapse{{-- in--}}" id="produceData">
                <form class="form-horizontal span6" style="margin-top: 20px">
                    <div class="control-group">
                        <label class="control-label"><span class="label label-inverse">若该项有数量要求，请在其后加上数量。尺寸格式均为 宽x高</span></label>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>装裱方式 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="装裱方式" name="mount" required
                                   value="{{ $product->produceParams->mount or '框画' }}" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>画框编号 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="画框编号" name="borderNo" required
                                   value="{{ $product->produceParams->border_no or '' }}" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>画框尺寸 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:20x30cm" name="borderSize" required
                                   value="{{ $product->produceParams->border_size or '' }}" />
                            {{--<span class="help-block">格式示例:20x30cm</span>--}}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>画芯编号 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="画芯编号" name="coreNo" required
                                   value="{{ $product->produceParams->core_no or '' }}" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>画芯尺寸 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:20x30cm" name="coreSize" required
                                   value="{{ $product->produceParams->core_size or '' }}" />
                            {{--<span class="help-block">格式示例:20x30cm</span>--}}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>画芯材料 :</label>
                        <div class="controls" id="coreMaterial">
                            <select name="coreMaterial">
                                @if(isset($cores))
                                    @foreach($cores as $core)
                                        <option value="{{ $core }}"
                                                {{ (isset($product->produceParams->core_material) && $product->produceParams->core_material === $core) ? 'selected' : '' }}>
                                            {{ $core }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>卡纸类型 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:1号卡纸（5cm x 8cm）" name="frameWidth" required
                                   value="{{ $product->produceParams->flannel_size or '10cm' }}" />
                            <span class="help-block">若没有卡纸，请填0或无</span>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>卡纸尺寸 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:20x30cm" name="frameSize" required
                                   value="{{ $product->produceParams->flannel_size or '' }}" />
                            <span class="help-block">与画芯贴板的尺寸相同</span>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>背板尺寸 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:20x30cm" name="backSize" required
                                   value="{{ $product->produceParams->back_size or '' }}" />
                            {{--<span class="help-block">画芯贴板的尺寸</span>--}}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>绒布宽度 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:20cm" name="flannelWidth" required
                                   value="{{ $product->produceParams->flannel_size or '10cm' }}" />
                            {{--<span class="help-block">格式示例:20x30cm</span>--}}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>绒布尺寸 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:20x30cm" name="flannelSize" required
                                   value="{{ $product->produceParams->flannel_size or '' }}" />
                            <span class="help-block">现阶段与画框尺寸相同，若不同，请单独填写</span>
                        </div>
                    </div>
                </form>
                <form class="form-horizontal span6" style="margin-top: 20px">
                    <div class="control-group">
                        <label class="control-label"><span class="label label-inverse" style="background: white">     </span></label>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>画芯钉 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:1号钉（5个）" name="coreNail" required
                                   value="{{ $product->produceParams->core_nail or '' }}" />
                            {{--<span class="help-block">格式示例:1号钉（5个）</span>--}}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>背板钉 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:1号钉（5个）" name="backNail" required
                                   value="{{ $product->produceParams->back_nail or '' }}" />
                            {{--<span class="help-block">格式示例:1号钉（5个）</span>--}}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>钢丝绳类型 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:1号钢丝绳" name="wireRope" required
                                   value="{{ $product->produceParams->wire_rope or '' }}" />
                            {{--<span class="help-block">格式示例:1号钢丝绳</span>--}}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>锁线器类型 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:1号锁线器（5个）" name="lineLocker" required
                                   value="{{ $product->produceParams->line_locker or '' }}" />
                            {{--<span class="help-block">格式示例:1号锁线器（5个）</span>--}}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>涂层类型 :</label>
                        <div class="controls" id="coating">
                            <select name="coating">
                                @if(isset($coatings))
                                    @foreach($coatings as $coating)
                                        <option value="{{ $coating }}"
                                                {{ (isset($product->produceParams->coating) && $product->produceParams->coating === $coating) ? 'selected' : '' }}>
                                            {{ $coating }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>暗挂型号 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:1号钩（5个）" name="hideHook" required
                                   value="{{ $product->produceParams->hide_hook or '' }}" />
                            {{--<span class="help-block">格式示例:1号钩（5个）</span>--}}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><span class="text-important">*</span>挂钩型号 :</label>
                        <div class="controls">
                            <input type="text" class="span11" placeholder="示例:1号钩（5个）" name="hook" required
                                   value="{{ $product->produceParams->hook or '' }}" />
                            {{--<span class="help-block">格式示例:1号钩（5个）</span>--}}
                        </div>
                    </div>
                </form>
            </div>
            <div class="widget-title">
                <a href="#collapseScene" data-toggle="collapse" class="clearfix"> <span class="icon"><i class="icon-edit"></i></span>
                    <h5>产品场景介绍</h5>
                </a>
            </div>
            <div class="collapse{{-- in--}}" id="collapseScene">
                <div class="widget-content nopadding clearfix">
                    {{--产品场景介绍--}}
                    @include('thirdProduct.introductionForm', [
                        'type' => 'scene',
                        'introductions' => $introductions,
                        'data' => $productIntroductions,
                        'isProductIntro' => true
                    ])
                </div>
            </div>
            <div class="widget-title">
                <a href="#collapseCraft" data-toggle="collapse" class="clearfix"> <span class="icon"><i class="icon-edit"></i></span>
                    <h5>产品工艺介绍</h5>
                </a>
            </div>
            <div class="collapse{{-- in--}}" id="collapseCraft">
                <div class="widget-content nopadding clearfix">
                    {{--产品工艺介绍--}}
                    @include('thirdProduct.introductionForm', [
                        'type' => 'craft',
                        'introductions' => $introductions,
                        'data' => $productIntroductions,
                        'isProductIntro' => true
                    ])
                </div>
            </div>
            <div class="widget-title">
                <a href="#collapsePublication" data-toggle="collapse" class="clearfix"> <span class="icon"><i class="icon-edit"></i></span>
                    <h5>作品介绍</h5>
                </a>
            </div>
            <div class="collapse" id="collapsePublication">
                <div class="widget-content nopadding clearfix">
                    {{--作品介绍--}}
                    @include('thirdProduct.introductionForm', [
                        'type' => 'publication',
                        'introductions' => $introductions,
                        'data' => $productIntroductions,
                        'isProductIntro' => true
                    ])
                </div>
            </div>
            <div class="widget-title">
                <a href="#collapseAuthor" data-toggle="collapse" class="clearfix"> <span class="icon"><i class="icon-edit"></i></span>
                    <h5>作者介绍</h5>
                </a>
            </div>
            <div class="collapse" id="collapseAuthor">
                <div class="widget-content nopadding clearfix">
                    {{--作者介绍--}}
                    @include('thirdProduct.introductionForm', [
                        'type' => 'author',
                        'introductions' => $introductions,
                        'data' => $productIntroductions,
                        'isProductIntro' => true
                    ])
                </div>
            </div>
            <div class="collapse in">
                <div class="widget-content nopadding clearfix">
                    <form class="form-horizontal span12" id="form-actions">
                        <div class="form-actions" style="margin: 0;">
                            <button type="submit" class="btn btn-success">保存</button>
                        </div>
                        @if(!isset($product))
                            @include('../layout/importExcel', ['type' => 'produceParam', 'data' => \App\IekModel\Version1_0\Constants\ImportExcel::PRODUCE_PARAMS])
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="css/tb-product.css">
<script type="text/javascript" src="js/jquery.sortable.js"></script>
<script type="text/javascript" src="js/addProduct.js"></script>
<script>
    $(function () {
        var $widget = $('#product-set-widget');
        //save product.
        $widget.find('.btn[type="submit"]').on('click', saveProduct);
        //add textarea.
        $widget.find('.collapse').find('.add-text').on('click', function() {
            var $this = $(this);
            var $group = $this.closest('.control-group');
            var $box = createIntroductionBox('text');
            $group.before($box);
            bindIntroductionBoxButtonEvent($box);
            $box.find('textarea').focus();
        })
        //add image.
        $widget.find('.collapse form:not(#form-actions)').find('.add-image input[type="file"]').on('change', uploadIntroductionImageEvent);

        var $productForm = $('#collapseProduct');
        //add publication.
        $productForm.find('.link[name="add-publication"]').on('click', function () {
            $('#tb-publication-add').trigger('click');
        })
        //add shop.
        $productForm.find('.link[name="add-shop"]').on('click', function () {
            $('#tb-shop-add').trigger('click');
        })
        //add product image.
        $productForm.find('form[name="product"]').find('.upload-file input[type="file"]').on('change', uploadProductImageEvent);
        //get publication introductions.
        var publicationId = $productForm.find('select[name="publication-id"] option:selected').val();
        getPublicationInfo(publicationId, $productForm);
        $productForm.find('select[name="publication-id"]').on("change", function(e) {
            var publicationId = e.val;
            getPublicationInfo(publicationId, $productForm);
        });
        //get product produce params.
        $productForm.find('input[name="no"]').on('blur', productNoBlurEvent);

        //get introductions.

        var $introductions = $widget.find('.collapse:not(#collapseProduct) form');
        $introductions.each(function () {
            var $this = $(this);
            var $introductionId = $this.find('select[name="introduction-id"] option:selected');
            if(!isNull($introductionId) && $introductionId.length > 0) {
                var introductionId = $introductionId.val();
                getIntroductionInfo(introductionId, $this);
            }
        });
        $introductions.find('select[name="introduction-id"]').on("change", function(e) {
            var $this = $(this);
            var $introductionForm = $this.closest('form');
            var introductionId = e.val;
            getIntroductionInfo(introductionId, $introductionForm);
        });
        $introductions.find('textarea, input').on('input propertychange', function () {
            var $this = $(this);
            var $form = $this.closest('form');
            $form.removeAttr('introductionId');
            $form.find('select[name="introduction-id"]').select2('val', '');
        });
    });
</script>
