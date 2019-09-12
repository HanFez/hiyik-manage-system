<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/1/9
 * Time: 9:28
 */
use App\IekModel\Version1_0\Constants\Path;
$path    = Path::FILE_PATH;
$iwall = $iwall->data;
if(!is_null($iwall)){
    $officials = $iwall->official;
    $officialReason = $iwall->officialReason;
    $iwallTitle = $iwall->iwallTitle;
    $iwallCover = $iwall->iwallCover;
    $iwallDescriptions = $iwall->iwallDescriptions;
    $iwallTags = $iwall->iwallTags;
    $iwallScene = $iwall->iwallScene;
    $iwallCrowd = $iwall->iwallCrowd;
    $iwallSex = $iwall->iwallSex;
    $iwallPerson = $iwall->iwallPerson;
    $reasons = $iwall->reasons;
    $iwallForbidden = $iwall->iwallForbidden;
    $wall = $iwall->wall;
}

if($iwall->is_forbidden){
    $is_official = false;
}else{
    if(!$officials->isEmpty()){
        foreach($officials as $off){
            if($off->is_removed === false){
                $is_official = true;
            }else{
                $is_official = false;
            }
        }
    }else{
        $is_official = false;
    }
}

if(!$iwallTags->isEmpty()){
    foreach($iwallTags as $tag){

        if($tag->tags->is_official === true){
            $tagOfficial[] = $tag->tags;
        }
        if($tag->tags->is_official === false){
            $tags[] = $tag->tags;
        }
    }
}
?>

@if($iwall->statusCode == 0)
    @if(!is_null($iwall))
        <div class="alert alert-error alert-block alert-right">
            <a class="close" data-dismiss="alert" href="#">×</a>
            <h4 class="alert-heading">Iwall状态</h4>
            @if($iwall -> is_active)
                @if($iwall -> is_forbidden)
                    <span class="label label-danger">已被禁止</span>
                    <span class="label label-inverse">未被推荐</span>
                @else
                    <span class="label label-inverse">未被禁止</span>
                    @if($is_official)
                        <span class="label label-success">已被推荐</span>
                    @else
                        <span class="label label-inverse">未被推荐</span>
                    @endif
                @endif
            @else
                <span class="label label-danger">已删除</span>
            @endif
        </div>
        <div class="dialog-title">
            标题:
            @if(!is_null($iwallTitle))
                @if($iwallTitle->is_active == true && $iwallTitle->is_removed == false)
                    @if(!is_null($iwallTitle->title) && !is_null($iwallTitle->title->description))
                        <?php $titleIsForbidden = $iwallTitle->title->is_forbidden;?>
                        <span name="forbidden-content">{{$iwallTitle->title->description->content}}</span>
                        @if($iwall->is_active === true && $iwall->is_publish === true)
                            @if(isset($titleIsForbidden))
                                <button class="btn btn-danger" type="i-description"
                                        data-type="{{ $titleIsForbidden ? 'unForbidden' : 'forbidden'}}" data="{{$iwallTitle->title->id}}">
                                    {{ $titleIsForbidden ? '取消禁止' : '禁止'}}
                                </button>
                            @endif
                            @if($titleIsForbidden === true)
                                <a href="javascript:void(0);" class="seeReason group-left" data="seeTitle/{{$iwallTitle->title->id}}">查看原因</a>
                            @endif
                        @endif
                    @else
                        {{'无标题'}}
                    @endif
                @endif
            @else
                {{'无标题'}}
            @endif
        </div>
        <div class="dialog-count">
            <span>喜欢:{{$iwall->like_count}}</span>
            <span>评论:{{$iwall->comment_count}}</span>
            <span>查看:{{$iwall->view_count}}</span>
        </div>
        <div class="dialog-header">
            作者:
            @if(!is_null($iwallPerson) && !is_null($iwallPerson->person))
                @foreach($iwallPerson->person->personNick as $nick)
                    @if( !is_null($nick) && $nick->is_active === true)
                        <a id="iwall-author" href="javascript:void(0)" data="{{$nick->person_id}}">
                            {{$nick->nick->nick}}
                        </a>
                    @else
                        {{"无昵称"}}
                    @endif
                @endforeach
            @else
                {{"该账号已被禁"}}
            @endif
        </div>
        <div class="dialog-tags">
            分类：
            @if(isset($tagOfficial) && !is_null($tagOfficial))
                @foreach($tagOfficial as $tag)
                    <a>{{\App\IekModel\Version1_0\IekModel::strTrans($tag -> name, 'Tag') }}</a>
                @endforeach
            @else
                {{'未选择分类'}}
            @endif
        </div>
        <div class="dialog-tags">
            自定义标签：
            @if(isset($tags) && !is_null($tags))
                <ul>
                    @foreach($tags as $tag)
                        <?php $tagIsForbidden = $tag -> is_forbidden;?>
                        <li>
                            <span name="forbidden-content" class="badge {{ $tagIsForbidden ? '' : 'badge-info'}}">{{ $tag->name }}</span>
                            @if($iwall->is_active !== false && $iwall->is_publish !== false)
                                @if(isset($tagIsForbidden))
                                    <button class="btn btn-danger btn-mini group-left" type="i-tag"
                                            data-type="{{ $tagIsForbidden? 'unForbidden' : 'forbidden'}}" data="{{ $tag->id  }}">
                                        {{ $tagIsForbidden ? '取消禁止' : '禁止'}}
                                    </button>
                                @endif
                                @if($tagIsForbidden === true)
                                    <a href="javascript:void(0);" class="seeReason group-left" data="seeTag/{{$tag->id}}" >查看原因</a>
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ul>
             @else
                <ul>
                    <li>{{'未定义标签'}}</li>
                </ul>
            @endif
        </div>
        <div class="dialog-content">
            <h5>Iwall 图片</h5>
            @if(!is_null($iwallCover))
                <?php $imageIsForbidden = $iwallCover->cover->is_forbidden;?>
                <div class="group">
                    @if($iwall->is_active !== false && $iwall->is_publish !== false)
                        <button class="btn btn-danger group-left" type="i-image" data-type="{{$imageIsForbidden ? 'unForbidden' : 'forbidden'}}"
                                data="{{ $iwallCover->image_id  }}">
                            {{ $imageIsForbidden ? '取消禁止' : '禁止'}}
                        </button>
                        @if($imageIsForbidden === true)
                            <a href="javascript:void(0);" class="seeReason group-left" data="seeImage/{{$iwallCover->cover->id}}" >查看原因</a>
                        @endif
                    @endif
                    <img class="group-right" src="{{$path.$iwallCover->cover->norms[0]->uri}}" alt="">
                    <ul>
                        <li>适用场景 ：
                            @if(!$iwallScene->isEmpty())
                            <span>{{\App\IekModel\Version1_0\IekModel::strTrans($iwallScene[0]->scene->name, 'Scene') }}</span>
                                @else
                                {{"无"}}
                            @endif
                        </li>
                        <li>适用人群 ：
                            @if(!$iwallCrowd->isEmpty())
                            <span> {{\App\IekModel\Version1_0\IekModel::strTrans($iwallCrowd[0]->crowd->name, 'Crowd') }}</span>
                                @else
                                {{"无"}}
                            @endif
                        </li>
                        <li>适用性别 ：
                            @if(!is_null($iwallSex) && !is_null($iwallSex->sex))
                            <span> {{\App\IekModel\Version1_0\IekModel::strTrans($iwallSex->sex->name, 'Sex') }}</span>
                                @else
                            {{"无"}}
                            @endif
                        </li>
                    </ul>
                </div>
            @else
                {{'缺少图片'}}
            @endif
            <h5>产品详情</h5>
            @if(!is_null($wall) && !is_null($wall->wall))
                @if(!$wall->wall->wallProduct->isEmpty())
                    @foreach($wall->wall->wallProduct as $k=>$product)
                        <div class="widget-content">
                            <h4>产品{{$k+1}}</h4>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <td> 产品类型 </td>
                                    <td>
                                        @if(!is_null($product->product->productDefine))
                                            {{$product->product->productDefine->name}}
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
                                    <td>{{'¥'.$product->product->price}}</td>
                                    <td><code>{{$product->product->currency}}</code></td>
                                </tr>
                                <tr>
                                    <td> 尺寸 </td>
                                    <td>{{$product->product->phy_width.'*'.$product->product->phy_height.'*'.$product->product->phy_depth.'（mm）'}}</td>
                                    <td><code>（长*宽*高）</code></td>
                                </tr>
                                <tr>
                                    <td> 重量 </td>
                                    <td>{{$product->product->weight or '未填写'}}</td>
                                    <td><code>（g）</code></td>
                                </tr>
                                <tr>
                                    <td> 备注 </td>
                                    <td>{{$product->product->memo or '未填写'}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td> 作者 </td>
                                    <td>
                                        @if(!is_null($product->product->person))
                                            <a id="product-author" href="javascript:void(0)" data="{{$product->product->person->person_id}}">
                                                {{$product->product->person->personNick->nick->nick}}
                                            </a>
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td> 装裱师 </td>
                                    <td>
                                        @if(!is_null($product->product->postMaker))
                                            <a>{{ $product->product->postMaker->maker->personNick->nick->nick}}</a>
                                        @else
                                            {{'未填写'}}
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle"> 框材料 </td>
                                    <td>
                                        @if(!is_null($product->product->border))
                                            <?php
                                            if(!is_null($product->product->border->materialDefine)){
                                                $matdefine = $product->product->border->materialDefine;
                                                $borderDepth = $matdefine->phy_height -
                                                ($matdefine->phy_press_height+$matdefine->phy_press_height_offset);
                                            }
                                            ?>
                                            {{'材料：'.$product->product->border->material->name}}<br>
                                            {{'编号：'.$product->product->border->material->serial_no}}
                                            <hr>
                                            重量：{{$product->product->border->material->weight}}/{{$product->product->border->material->weight_unit}}
                                            <hr>
                                            {{'框内径尺寸：'.($product->product->border->phy_width-2*$borderDepth).'*'.($product->product->border->phy_height-2*$borderDepth).'（mm）'}}<br>
                                            框厚度：{{$product->product->border->phy_depth.'（mm）'}}
                                            <hr>
                                            @foreach($product->product->border->line as $line)
                                                {{'使用数量：'.$line->amount.'根  /  每根长度：'.$line->phy_length.'（mm）'}}<br>
                                            @endforeach
                                            <hr>
                                            价格：{{'¥'.$product->product->border->price}}<br>总用量：{{$product->product->border->dosage}}（米）
                                        @endif
                                    </td>
                                    <td>
                                        <code>外观图</code>
                                        @if(!is_null($product->product->border))
                                            <img src="{{$path.$matdefine->facade->uri}}" alt="" width="200" height="150">
                                        @else
                                            <code>无画芯</code>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td> 画芯材料 </td>
                                    <td>
                                        @if(!is_null($product->product->core))
                                            材料：{{$product->product->core->material->name}}<br>
                                            编号：{{$product->product->core->material->serial_no}}
                                            <hr>
                                            重量：{{$product->product->core->material->weight}}/{{$product->product->core->material->weight_unit}}
                                            <hr>
                                            尺寸：{{$product->product->core->phy_width.'*'.$product->product->core->phy_height.'（mm）'}}<br>
                                            厚度：{{$product->product->core->phy_depth.'（mm）'}}
                                            <hr>
                                            价格：{{'¥'.$product->product->core->price}}<br>总用量：{{$product->product->core->dosage}}（平米）
                                        @else
                                            无画芯
                                        @endif
                                    </td>
                                    <td>
                                        <code>外观图</code>
                                        @if(!is_null($product->product->core))
                                            <img src="{{$path.$product->product->core->materialDefine->facade->uri}}" alt="" width="150" height="150">
                                        @else
                                            <code>无画芯</code>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>画芯工艺 </td>
                                    <td>
                                        @if(!is_null($product->product->core))
                                            {{$product->product->core->coreHandle->name}}
                                            <hr>
                                            喷印精度：{{$product->product->core->dpi}}DPI
                                        @else
                                            无画芯
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_null($product->product->core))
                                            <code>无画芯</code>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td> 画芯作品 </td>
                                    <td>
                                        <?php
                                        if(!is_null($product->product->core)){
                                            $core = $product->product->core;
                                            if(!$core->coreContent->isEmpty()){
                                                foreach($core->coreContent as $content){
                                                    $text = $content->content;
                                                    $img =  $text->image;
                                                    if(!is_null($content->corePublication))
                                                        $pub = $content->corePublication;
                                                    else
                                                        $pub = null;
                                                }

                                            }
                                        }
                                        ?>

                                        @if(!is_null($product->product->core))
                                            @if(isset($text) && !is_null($text) && is_null($pub))
                                                该作品为用户上传图片
                                            @endif
                                            @if(!is_null($pub))
                                                {{"此作品为用户引用平台作品，出自"}}
                                                <a id="core_pub" href="javascript:void(0);" data="{{$pub->publication_id}}">
                                                    《{{$pub->title->title->description->content}}》
                                                </a>
                                            @endif
                                        @else
                                            无画芯
                                        @endif
                                    </td>
                                    <td>
                                        <code>缩略图</code>
                                        @if(!is_null($product->product->core))
                                            @if(!is_null($text) && is_null($pub))
                                                <img src="{{$path.$img->norms[4]->uri}}" alt="" >
                                            @endif
                                            @if(!is_null($pub))
                                                <img src="{{$path.$pub->pubImg->image->norms[4]->uri}}" alt="">
                                            @endif
                                        @else
                                            <code>无画芯</code>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td> 卡纸材料 </td>
                                    <td>
                                        @if(!$product->product->frame->isEmpty())
                                            @foreach($product->product->frame as $frame)
                                                第{{$frame->layer+1}}层<br>
                                                材料:{{$frame->material->name}}<br>
                                                编号：{{$frame->material->serial_no}}<br>
                                                重量：{{$frame->material->weight}}/{{$frame->material->weight_unit}}<br>
                                                尺寸：{{$frame->phy_width.'*'.$frame->phy_height.'（mm）'}}<br>
                                                厚度：{{$frame->phy_depth.'（mm）'}}<br>
                                                价格：{{'¥'.$frame->price}}<br>总用量：{{$frame->dosage}}（平米）<br>
                                                卡纸开洞：
                                                @if(!$frame->frameHole->isEmpty())
                                                    @foreach($frame->frameHole as $hole)
                                                        <br>&nbsp;&nbsp;&nbsp;&nbsp;第{{$hole->hole_index+1}}个洞<br>
                                                        洞尺寸：{{$hole->phy_width.'*'.$hole->phy_height}}（mm）<br>
                                                        价格：{{'¥'.$hole->price}}<br>总用量：{{$hole->dosage}}（平米）<br>
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
                                        @if(!$product->product->frame->isEmpty())
                                            @foreach($product->product->frame as $frame)
                                                <img src="{{$path.$frame->materialDefine->facade->uri}}" alt="" width="150" height="150">
                                            @endforeach
                                        @else
                                            <code>无卡纸</code>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td> 面板材料 </td>
                                    <td>
                                        @if(!is_null($product->product->front))
                                            材料：{{$product->product->front->material->name}}<br>
                                            编号：{{$product->product->front->material->serial_no}}
                                            <hr>
                                            重量：{{$product->product->front->material->weight}}/{{$product->product->front->material->weight_unit}}
                                            <hr>
                                            尺寸：{{$product->product->front->phy_width.'*'.$product->product->front->phy_height}}（mm）<br>
                                            厚度：{{$product->product->front->phy_depth}}（mm）
                                            <hr>
                                            价格：{{'¥'.$product->product->front->price}}<br>总用量：{{$product->product->front->dosage}}（平米）
                                        @else
                                            无面板
                                        @endif
                                    </td>
                                    <td>
                                        <code>外观图</code>
                                        @if(!is_null($product->product->front))
                                            <img src="{{$path.$product->product->front->materialDefine->facade->uri}}" alt="" width="150" height="150">
                                        @else
                                            <code>无面板</code>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td> 背板材料 </td>
                                    <td>
                                        @if(!is_null($product->product->back))
                                            材料：{{$product->product->back->material->name}}<br>
                                            编号：{{$product->product->back->material->serial_no}}
                                            <hr>
                                            重量：{{$product->product->back->material->weight}}/{{$product->product->back->material->weight_unit}}
                                            <hr>
                                            尺寸：{{$product->product->back->phy_width.'*'.$product->product->back->phy_height}}（mm）<br>
                                            厚度：{{$product->product->back->phy_depth}}（mm）
                                            <hr>
                                            价格：{{'¥'.$product->product->back->price}}<br>总用量：{{$product->product->back->dosage}}（平米）
                                        @else
                                            无背板
                                        @endif
                                    </td>
                                    <td>
                                        <code>外观图</code>
                                        @if(!is_null($product->product->back))
                                            <img src="{{$path.$product->product->back->materialDefine->facade->uri}}" alt="" width="150" height="150">
                                        @else
                                            <code>无背板</code>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td> 背板装饰 </td>
                                    <td>
                                        @if(!is_null($product->product->backFacade))
                                            材料：{{$product->product->backFacade->material->name}}<br>
                                            编号：{{$product->product->backFacade->material->serial_no}}
                                            <hr>
                                            重量：{{$product->product->backFacade->material->weight}}/{{$product->product->backFacade->material->weight_unit}}
                                            <hr>
                                            尺寸：{{$product->product->backFacade->phy_width.'*'.$product->product->backFacade->phy_height}}（mm）<br>
                                            厚度：{{$product->product->backFacade->phy_depth}}（mm）
                                            <hr>
                                            价格：{{'¥'.$product->product->backFacade->price}}<br>总用量：{{$product->product->backFacade->dosage}}（平米）
                                        @else
                                            无背板装饰
                                        @endif
                                    </td>
                                    <td>
                                        <code>外观图</code>
                                        @if(!is_null($product->product->backFacade))
                                            <img src="{{$path.$product->product->backFacade->materialDefine->facade->uri}}" alt="" width="150" height="150">
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
                    @endforeach
                @endif
            @else
                <span>{{'这个IWall没有添加产品'}}</span>
            @endif
            @if(!$iwallDescriptions->isEmpty())
                <p class="dialog-notice">以下为创作过程、细节图及应用场景展示:</p>
                <div class="group">
                    @foreach($iwallDescriptions as $description)
                        @if($description->is_active === true)
                            @if(!is_null($description->description) && !is_null($description->description->description))
                                <?php $descriotionIsForbidden = $description->description->is_forbidden; ?>
                                @if($iwall->is_active !== false && $iwall->is_publish !== false)
                                    @if(isset($descriotionIsForbidden))
                                        <button class="btn btn-danger group-left" type="i-description"
                                                data-type="{{ $descriotionIsForbidden ? 'unForbidden' : 'forbidden'}}" data="{{ $description->description->id}}">
                                            {{ $descriotionIsForbidden ? '取消禁止' : '禁止'}}
                                        </button>
                                    @endif
                                    @if($descriotionIsForbidden === true)
                                        <a href="javascript:void(0);" class="seeReason group-left" data="seeTitle/{{$description->description->id}}" >查看原因</a>
                                    @endif
                                @endif
                                <span class="group-right" name="forbidden-content">{{$description->description->styleText->content}}</span>
                            @endif
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
        @if(!$officialReason->isEmpty())
            <div class="dialog-content dialog-border">
                <div class="group" style="margin-top: 20px">
                    <div class="group-left">
                        推荐记录:
                    </div>
                    <div class="group-right">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>推荐类型</th>
                                <th>推荐原因</th>
                                <th>推荐时间</th>
                                <th>操作人账号</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($officialReason as $v)
                                <?php
                                    if(!is_null($v->operator)){
                                        $operator = $v->operator;
                                    }else{
                                        echo '无数据';
                                    }
                                    if(!is_null($v->reason)){
                                        $reason = $v->reason;
                                    }else{
                                        echo '无数据';
                                    }
                                ?>
                                <tr>
                                    @if($reason->type == 'forbidden')
                                        <td>{{'取消推荐'}}</td>
                                        <td>{{$v->memo or '无数据'}}</td>
                                    @else
                                        <td>{{ \App\IekModel\Version1_0\IekModel::strTrans($reason->type, 'publication') }}</td>
                                        <td>{{$reason->reason or '无数据'}}</td>
                                    @endif
                                    <td data-time="utc">{{ $v->created_at }}</td>
                                    <td>{{ $operator->id or '无数据' }}-{{ $operator->name or '无数据' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        @if(!$iwallForbidden->isEmpty())
            <div class="dialog-content dialog-border">
                <div class="group" style="margin-top: 20px">
                    <div class="group-left">
                        禁止记录:
                    </div>
                    <div class="group-right">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>禁止类型</th>
                                <th>禁止原因</th>
                                <th>禁止时间</th>
                                <th>操作人账号</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($iwallForbidden as $val)
                                @if($val->reason != null)
                                    <tr>
                                        <td>{{ \App\IekModel\Version1_0\IekModel::strTrans($val->reason->type, 'iwall') }}</td>
                                        <td>{{$val->reason->reason}}</td>
                                        <td data-time="utc">{{$val->created_at}}</td>
                                        <td>{{!is_null($val->operator) ? $val->operator->id.'-'.$val->operator->name : ''}}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        @if($iwall->is_active !== false && $iwall->is_publish !== false)
            <div class="dialog-footer">
                <button class="btn btn-danger" type="iwall" data-type="{{ $iwall->is_forbidden ? 'unForbidden' : 'forbidden'}}"
                        data="{{ $iwall->id  }}">
                    {{ $iwall->is_forbidden ? '取消禁止' : '禁止'}}
                </button>
                <button id="iwall-official" class="btn btn-warning" type="i-official"
                        data-type="{{ $is_official === false ? 'official' : 'unOfficial'}}" data="{{$iwall->id}}">
                    {{ $is_official === false ? '推荐' : '取消推荐'}}
                </button>
            </div>
        @endif
        @extends('layout/reason')
    @endif
@elseif($result->statusCode == 30001)
    @include('message.messageAlert',['type'=>'error','message'=>'无效的IwallID'])
@endif
<script>
    $('#iwall-author').on('click', bindEventToShowPublicationAuthor);
    $('#core_pub').on('click',function(){
        var id = $(this).attr('data');
        ajaxData('get', 'publications/'+id, handleGetViewShowCallback, [], 'myPublication')
    });
    $('.seeReason').on('click',function(){
        var url = $(this).attr('data');
        bootstrapQ.dialog({
            type: 'get',
            url: url,
            title: '被禁原因',
            //foot:false
        });
    });
</script>
