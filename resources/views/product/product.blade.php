<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/1/17
 * Time: 15:34
 */
$path = \App\IekModel\Version1_0\Constants\Path::FILE_PATH;
$product = $result->data;
?>
@if($result->statusCode == 0)
    @if(!is_null($product))
        <div align="center" id="product-img">
            @if(!is_null($product->productThumb))
                <img  class="group-right" src="{{$path.$product->productThumb->thumb->norm[1]->uri}}" alt="" style="vertical-align: top;">
            @else
                <h3>{{'未裁剪封面图'}}</h3>
            @endif
            {{--<button class="btn-primary right">3D预览</button>--}}
        </div>
        <div class="widget-box">
            <div class="widget-title"><span class="icon"> <i class="icon-ok-sign"></i> </span>
                <h5>产品详情</h5>
            </div>
            <div class="widget-content">
                <table class="table table-bordered table-striped">
                    <tr>
                        <td> 产品类型 </td>
                        <td>
                            @if(!is_null($product->productDefine))
                                {{$product->productDefine->name}}
                            @else
                                {{"未定义"}}
                            @endif
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td> 品牌 </td>
                        <td>海艺客</td>
                        <td><code>HIYIK</code></td>
                    </tr>
                    <tr>
                        <td> 价格 </td>
                        <td>{{$result->price}}{{$product->currency}}</td>
                        <td><code>产品当前定价</code></td>
                    </tr>
                    <tr>
                        <td> 尺寸 </td>
                        <td>{{$product->phy_width.'*'.$product->phy_height.'*'.$product->phy_depth.'（mm）'}}</td>
                        <td><code>（长*宽*高）</code></td>
                    </tr>
                    <tr>
                        <td> 重量 </td>
                        <td>{{$product->weight or '未填写'}}</td>
                        <td><code>（g）</code></td>
                    </tr>
                    <tr>
                        <td> 备注 </td>
                        <td>{{$product->memo or '未填写'}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td> 作者 </td>
                        <td>
                            @if(!is_null($product->person))
                                <a id="product-author" href="javascript:void(0)" data="{{$product->person->person_id}}">
                                    {{$product->person->personNick->nick->nick}}
                                </a>
                            @endif
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td> 装裱师 </td>
                        <td>
                            @if(!is_null($product->postMaker))
                                <a>{{ $product->postMaker->maker->personNick->nick->nick}}</a>
                            @else
                                {{'未填写'}}
                            @endif
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td> 装裱方式 </td>
                        <td>
                            @if(!is_null($product->productDefine))
                                {{$product->productDefine->name}}
                            @endif
                        </td>
                        <td>
                            @if(!is_null($product->productDefine))
                                <code>{{$product->productDefine->name_abbr}}</code>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle"> 框材料 </td>
                        <td>
                            @if(!is_null($product->border))
                                <?php
                                    if(!is_null($product->border->materialDefine)){
                                        $matdefine = $product->border->materialDefine;
                                        $borderDepth = $matdefine->phy_height -
                                        ($matdefine->phy_press_height+$matdefine->phy_press_height_offset);
                                    }
                                ?>
                                {{'材料：'.$product->border->material->name}}<br>
                                {{'编号：'.$product->border->material->serial_no}}
                                    <hr>
                                    重量：{{$product->border->material->weight}}/{{$product->border->material->weight_unit}}
                                    <hr>
                                {{'框内径尺寸：'.($product->border->phy_width-2*$borderDepth).'*'.($product->border->phy_height-2*$borderDepth).'（mm）'}}<br>
                                框厚度：{{$product->border->phy_depth.'（mm）'}}
                                    <hr>
                                @foreach($product->border->line as $line)
                                    {{'使用数量：'.$line->amount.'根  /  每根长度：'.$line->phy_length.'（mm）'}}<br>
                                @endforeach
                                    <hr>
                                价格：{{$result->borderPrice.$product->currency}}<br>用量：{{$product->border->dosage.$product->border->dosage_unit}}
                            @endif
                        </td>
                        <td>
                            <code>外观图</code>
                            @if(!is_null($product->border))
                                <img src="{{$path.$matdefine->facade->uri}}" alt="" width="200" height="150">
                            @else
                                <code>无画芯</code>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td> 画芯材料 </td>
                        <td>
                            @if(!is_null($product->core))
                                材料：{{$product->core->material->name}}<br>
                                编号：{{$product->core->material->serial_no}}
                                <hr>
                                重量：{{$product->core->material->weight}}/{{$product->core->material->weight_unit}}
                                <hr>
                                尺寸：{{$product->core->phy_width.'*'.$product->core->phy_height.'（mm）'}}<br>
                                厚度：{{$product->core->phy_depth.'（mm）'}}
                                <hr>
                                价格：{{$result->coreMaterial.$product->currency}}<br>
                                用量：{{$product->core->dosage.$product->core->dosage_unit}}
                            @else
                                无画芯
                            @endif
                        </td>
                        <td>
                            <code>外观图</code>
                            @if(!is_null($product->core))
                                <img src="{{$path.$product->core->materialDefine->facade->uri}}" alt="" width="150" height="150">
                            @else
                                <code>无画芯</code>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>画芯工艺 </td>
                        <td>
                            @if(!is_null($product->core))
                                工艺名：{{$product->core->coreHandle->name}}
                                <hr>
                                价格：{{$result->coreHandle.$product->currency}}
                            @else
                                无画芯
                            @endif
                        </td>
                        <td>
                            @if(!is_null($product->core))
                                <code>喷印内容面积和的价格</code>
                            @else
                                <code>无画芯</code>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td> 画芯作品 </td>
                        @if(!is_null($product->core))
                            @if(!$product->core->coreContent->isEmpty())
                                <td>
                                    @foreach($product->core->coreContent as $content)
                                        @if(!is_null($content->content))
                                            @if(!is_null($content->content->corePublication))
                                                {{"此作品为用户引用于平台，出自"}}
                                                <a id="core_pub" href="javascript:void(0);"
                                                   data="{{$content->content->corePublication->publication_id}}">
                                                    《{{$content->content->corePublication->title->title->description->content}}》
                                                </a>
                                                <br>
                                            @else
                                                该作品为用户上传
                                            @endif
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    <code>缩略图</code>
                                    @foreach($product->core->coreContent as $content)
                                        @if(!is_null($content->content))
                                            @if(!is_null($content->content->corePublication))
                                                <img src="{{$path.$content->content->corePublication->pubImg->image->norms[4]->uri}}">
                                            @else
                                                <img src="{{$path.$content->content->image->norms[4]->uri}}">
                                            @endif
                                        @endif
                                    @endforeach
                                </td>
                            @else
                                <td>无作品</td>
                                <td><code>无作品</code></td>
                            @endif
                        @else
                            <td>无画芯</td>
                            <td><code>无画芯</code></td>
                        @endif
                    </tr>
                    <tr>
                        <td> 卡纸材料 </td>
                        <td>
                            @if(!$product->frame->isEmpty())
                                @foreach($product->frame as $frame)
                                    第{{$frame->layer+1}}层<br>
                                    材料:{{$frame->material->name}}<br>
                                    编号：{{$frame->material->serial_no}}<br>
                                    重量：{{$frame->material->weight}}/{{$frame->material->weight_unit}}<br>
                                    尺寸：{{$frame->phy_width.'*'.$frame->phy_height.'（mm）'}}<br>
                                    厚度：{{$frame->phy_depth.'（mm）'}}<br>
                                    价格：{{$frame->price.$frame->currency}}<br>
                                    用量：{{$frame->dosage.$frame->dosage_unit}}<br>
                                    卡纸开洞：
                                    @if(!$frame->frameHole->isEmpty())
                                        @foreach($frame->frameHole as $hole)
                                            <br>&nbsp;&nbsp;&nbsp;&nbsp;第{{$hole->hole_index+1}}个洞<br>
                                            洞尺寸：{{$hole->phy_width.'*'.$hole->phy_height}}（mm）<br>
                                            价格：{{$hole->price.$hole->currency}}<br>
                                            用量：{{$hole->dosage.$hole->dosage_unit}}<br>
                                        @endforeach
                                    @else
                                        未开洞
                                    @endif
                                    <hr>
                                @endforeach
                            @else
                                无卡纸
                            @endif
                        </td>
                        <td>
                            <code>外观图</code>
                            @if(!$product->frame->isEmpty())
                                @foreach($product->frame as $frame)
                                    <img src="{{$path.$frame->materialDefine->facade->uri}}" alt="" width="150" height="150">
                                    <br>
                                @endforeach
                            @else
                                <code>无卡纸</code>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td> 面板材料 </td>
                        <td>
                            @if(!is_null($product->front))
                                材料：{{$product->front->material->name}}<br>
                                编号：{{$product->front->material->serial_no}}
                                <hr>
                                重量：{{$product->front->material->weight}}/{{$product->front->material->weight_unit}}
                                <hr>
                                尺寸：{{$product->front->phy_width.'*'.$product->front->phy_height}}（mm）<br>
                                厚度：{{$product->front->phy_depth}}（mm）
                                <hr>
                                价格：{{$result->frontPrice.$product->front->currency}}<br>
                                用量：{{$product->front->dosage.$product->front->dosage_unit}}
                            @else
                                无面板
                            @endif
                        </td>
                        <td>
                            <code>外观图</code>
                            @if(!is_null($product->front))
                                <img src="{{$path.$product->front->materialDefine->facade->uri}}" alt="" width="150" height="150">
                            @else
                                <code>无面板</code>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td> 背板材料 </td>
                        <td>
                            @if(!is_null($product->back))
                                材料：{{$product->back->material->name}}<br>
                                编号：{{$product->back->material->serial_no}}
                                <hr>
                                重量：{{$product->back->material->weight}}/{{$product->back->material->weight_unit}}
                                <hr>
                                尺寸：{{$product->back->phy_width.'*'.$product->back->phy_height}}（mm）<br>
                                厚度：{{$product->back->phy_depth}}（mm）
                                <hr>
                                价格：{{$result->backPrice.$product->back->currency}}<br>
                                用量：{{$product->back->dosage.$product->back->dosage_unit}}
                            @else
                                无背板
                            @endif
                        </td>
                        <td>
                            <code>外观图</code>
                            @if(!is_null($product->back))
                                <img src="{{$path.$product->back->materialDefine->facade->uri}}" alt="" width="150" height="150">
                            @else
                                <code>无背板</code>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td> 背板装饰 </td>
                        <td>
                            @if(!is_null($product->backFacade))
                                材料：{{$product->backFacade->material->name}}<br>
                                编号：{{$product->backFacade->material->serial_no}}
                                <hr>
                                重量：{{$product->backFacade->material->weight}}/{{$product->backFacade->material->weight_unit}}
                                <hr>
                                尺寸：{{$product->backFacade->phy_width.'*'.$product->backFacade->phy_height}}（mm）<br>
                                厚度：{{$product->backFacade->phy_depth}}（mm）
                                <hr>
                                价格：{{$result->backFacadePrice.$product->backFacade->currency}}<br>
                                用量：{{$product->backFacade->dosage.$product->backFacade->dosage_unit}}
                            @else
                                无背板装饰
                            @endif
                        </td>
                        <td>
                            <code>外观图</code>
                            @if(!is_null($product->backFacade))
                                <img src="{{$path.$product->backFacade->materialDefine->facade->uri}}" alt="" width="150" height="150">
                            @else
                                <code>无背板装饰</code>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td> 生产商 </td>
                        <td>成都海艺客技术有限公司</td>
                        <td><code>hiyik.com</code></td>
                    </tr>
                    <tr>
                        <td> 产地 </td>
                        <td>中国 四川</td>
                        <td><code>成都市</code></td>
                    </tr>
                </table>
            </div>
        </div>
    @endif
@elseif($result->statusCode == 21001)
    @include('message.messageAlert',['type'=>'error','message'=>'无效的产品ID'])
@endif
<script>
    $('#core_pub').on('click',function(){
        var id = $(this).attr('data');
        ajaxData('get', 'publications/'+id, handleGetViewShowCallback, [], 'myPublication')
    });
    $('#product-author').on('click', bindEventToShowPublicationAuthor);
</script>
