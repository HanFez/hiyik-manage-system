<?php

use App\IekModel\Version1_0\Constants\Path;
use App\IekModel\Version1_0\Constants\IntroductionType;

$r = new ReflectionClass(IntroductionType::class);
$types = $r -> getConstants();
$path = Path::FILE_PATH;

$isProductIntro = isset($isProductIntro) ? $isProductIntro : null;
//作品 publication、作者 author、工艺 craft、场景 scene
$introductions = isset($introductions) ? $introductions : null;
$type = isset($type) ? $type : null;
$typeIntros = null;
$data = isset($data) ? $data : null;
$typeData = null;
if($isProductIntro === false) {
    $typeData = $data;
} else if(!is_null($type)) {
    if(isset($introductions)) {
        foreach ($introductions as $introduction) {
            if($introduction->type === $type) {
                if(is_null($typeIntros)) {
                    $typeIntros = [];
                }
                array_push($typeIntros, $introduction);
            }
        }
    }
    if(isset($data)) {
        foreach ($data as $item) {
            if(isset($item->introduction->type) && $item->introduction->type === $type) {
                $typeData = $item->introduction;

            }
        }
    }
}

if(isset($typeData) && isset($typeData->introductionContent)) {
    $index = array();
    foreach($typeData->introductionContent as $content){
        $index[] = $content->index;
    }
    array_multisort($index, SORT_ASC, $typeData->introductionContent);
}
?>

<form class="form-horizontal" name="{{ $type }}" introductionId="{{ $typeData->id or '' }}">
    @if(isset($isProductIntro) && $isProductIntro === false)
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>介绍类型 :</label>
            <div class="controls">
                <select name="introduction-type">
                    @if(isset($types))
                        @foreach($types as $item)
                            @if(isset($typeData->type) && $typeData->type === $item)
                                <option value="{{ $item }}" selected>
                            @else
                                <option value="{{ $item }}" >
                            @endif
                                    {{ \App\IekModel\Version1_0\IekModel::strTrans($item, 'IntroductionType') }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    @elseif(isset($type) && $type != 'scene')
    <div class="control-group">
        <label class="control-label">引用{{ \App\IekModel\Version1_0\IekModel::strTrans($type, 'IntroductionType') }}介绍 :</label>
        <div class="controls">
            <select name="introduction-id">
                @if(isset($typeIntros))
                    <option value="">请选择引用的介绍</option>
                    @foreach($typeIntros as $typeIntro)
                        @if(isset($typeData->id) && $typeData->id === $typeIntro->id)
                            <option value="{{ $typeIntro->id }}" selected>
                        @else
                            <option value="{{ $typeIntro->id }}" >
                        @endif
                            {{ $typeIntro->name or '未命名' }}
                            -
                            {{ $typeIntro->description or '未添加描述' }}
                        </option>
                    @endforeach
                @endif
            </select>
            <span class="help-block">
                选择了引用介绍之后，原添加的图片和文字将被覆盖喔。
            </span>
        </div>
    </div>
    @endif
    <div class="control-group">
        <label class="control-label">
            {{--@if(isset($isProductIntro) && $isProductIntro === false)
                <span class="text-important">*</span>
            @endif--}}<span class="text-important">*</span>介绍名 :</label>
        <div class="controls">
            <input type="text" class="span11" placeholder="介绍名" name="name"
                   {{ $isProductIntro === false ? 'required' : '' }}
                   value="{{ $typeData->name or '' }}" />
            <span class="help-block">此介绍名可用于搜索介绍</span>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">描述 :</label>
        <div class="controls">
            <input type="text" class="span11" placeholder="描述" name="description"
                   value="{{ $typeData->description or '' }}" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label"><span class="label label-inverse">介绍详情如下，请添加文字或图片</span></label>
        <div class="controls">
            <span class="help-block">
                若没有介绍的文字或图片，将不保存此介绍。
                @if(isset($isProductIntro) && $isProductIntro === true)
                    <br>引用了介绍，若修改了此介绍名、描述或详情，将生成一个新的介绍<br>
                    要修改介绍，请到介绍管理页修改。
                @endif
            </span>
        </div>
    </div>
        @if(isset($typeData->introductionContent))
            @foreach($typeData->introductionContent as $content)
                @if(!isset($content->image_id) && isset($content->content))
                    <div class="control-group introduction text" index="{{ $content->index }}">
                        <div class="box">
                            <div class="btn-group">
                                <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown"><span class="icon-edit"></span><span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a class="reorder">排序</a></li>
                                    <li><a class="delete">删除</a></li>
                                </ul>
                            </div>
                            <textarea class="span12" rows="4">{{ $content->content }}</textarea>
                        </div>
                    </div>
                @elseif(isset($content->image_id))
                    <div class="control-group introduction image" index="{{ $content->index }}" imageid="{{ $content->image_id }}">
                        <div class="box">
                            <div class="btn-group">
                                <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown"><span class="icon-edit"></span><span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a class="add-title" hasTitle="{{ isset($content->content) ? 'true' : 'false' }}">
                                            {{ isset($content->content) ? '修改' : '添加' }}标题</a></li>
                                    <li><a class="upload-file replace-image">替换图片<input type="file"></a></li>
                                    <li><a class="reorder">排序</a></li>
                                    <li><a class="delete">删除</a></li>
                                </ul>
                            </div>
                            @if(isset($content->content))
                                <div class="control-group introduction text">
                                    <div class="box">
                                        <div class="btn-group">
                                            <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown"><span class="icon-edit"></span><span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                                <li><a class="reorder">排序</a></li>
                                                <li><a class="delete">删除</a></li>
                                            </ul>
                                        </div>
                                        <textarea class="span12" rows="4">{{ $content->content }}</textarea>
                                    </div>
                                </div>
                            @endif
                            @if(isset($content->image->norms[0]->uri))
                                <img src="{{ $path.$content->image->norms[0]->uri }}" alt="">
                            @else
                                <img src="/img/default.png" alt="">
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    <div class="control-group">
        <div class="form-actions">
            <a href="javascript:void(0)" class="btn btn-info add-text">添加文字</a>
            <a href="javascript:void(0)" class="btn btn-primary add-image upload-file">添加图片<input type="file"></a>
        </div>
    </div>
    @if(isset($isProductIntro) && $isProductIntro === false)
        <div class="control-group">
            <div class="form-actions">
                <button type="submit" class="btn btn-success">保存</button>
            </div>
        </div>
    @endif
</form>
