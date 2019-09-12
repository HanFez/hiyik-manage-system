<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/13/17
 * Time: 16:48 AM
 */

$result = isset($result) ? $result : null;
$museums = null;
$authors = null;
$data = null;
$publication = null;
$isModify = false;
if(isset($result) && $result->isOk() && isset($result->data)) {
    $data = $result->data;
    if(isset($data->museums)) {
        $museums = $data->museums;
    }
    if(isset($data->authors)) {
        $authors = $data->authors;
    }
    if(isset($data->publication)) {
        $publication = $data->publication;
        $isModify = true;
    }
}

?>

@extends('layout/widget')

@section('icon')
    <i class="{{ $isModify === true ? 'icon-pencil' : 'icon-plus' }}"></i>
@stop

@section('title')
    @if($isModify === true)
        修改
    @else
        添加
    @endif
    作品
    @include('layout/required')
@stop

@section('content')
    <form class="form-horizontal" id="publication-form" publicationId="{{ $publication->id or '' }}">
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>编号 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="编号" name="no" required
                       value="{{ $publication->no or '' }}"
                        {{ isset($publication->no) ? 'disabled' : '' }}/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>作者编号 :</label>
            <div class="controls">
                <select name="author-no">
                    @if(isset($authors))
                        @foreach($authors as $author)
                            @if(isset($author->no) && isset($publication->author_no) && $publication->author_no === $author->no)
                                <option value="{{ $author->no or '' }}" selected>
                            @else
                                <option value="{{ $author->no or '' }}">
                            @endif
                                {{ $author->no or '' }}
                                -
                                {{ $author->name or '' }}
                                {{ $author->lang or '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <span class="help-block"><a id="btn-add-author" href="javascript:void(0);" class="link">添加作者</a></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>名字 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="名字" name="name" required
                       value="{{ $publication->name or '' }}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><span class="text-important">*</span>名字翻译 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="名字翻译" name="lang" required
                       value="{{ $publication->lang or '' }}" />
                <span class="help-block">翻译是翻译成中文的。若名字是中文，请原样输入；若是英文，不知道翻译成什么，也请原样输入。</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">描述 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="描述" name="description"
                       value="{{ $publication->description or '' }}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">作品年代 :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="作品年代" name="year"
                       value="{{ $publication->year or '' }}" />
                <span class="help-block">不带单位，例子：1891；1890-92。</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">宽（cm） :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="宽（cm）" name="width" data-type="number"
                       value="{{ $publication->width or '' }}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">高（cm） :</label>
            <div class="controls">
                <input type="text" class="span11" placeholder="高（cm）" name="height" data-type="number"
                       value="{{ $publication->height or '' }}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">珍藏处 :</label>
            <div class="controls">
                <select name="museum-id">
                    @if(isset($museums))
                        @foreach($museums as $museum)
                            @if(isset($museum->id) && isset($publication->museum_id) && $publication->museum_id === $museum->id)
                                <option value="{{ $museum->id or '' }}" selected>
                                @else
                            <option value="{{ $museum->id or '' }}">
                                @endif
                                {{ $museum->name or '' }}
                                {{ $museum->lang or '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <span class="help-block"><a id="btn-add-museum" href="javascript:void(0);" class="link">添加珍藏处</a></span>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-success" isModify="{{ $isModify }}">保存</button>
        </div>
        @if(!isset($isModify) || $isModify == false)
            @include('../layout/importExcel', ['type' => 'publication', 'data' => \App\IekModel\Version1_0\Constants\ImportExcel::PUBLICATION])
        @endif
    </form>
    <script>
        $(function() {
            $('#publication-form').find('.btn[type="submit"]').on('click', savePublication);
            $('#btn-add-author').on('click', function () {
                $('#tb-author-add').trigger('click');
            })
            $('#btn-add-museum').on('click', function () {
                $('#tb-museum-add').trigger('click');
            })
        })
        function savePublication(event) {
            eventUtil.preventDefault(event);
            loadingShow();
            var $this = $(this);
            var $form = $this.closest('form');
            var isModify = $this.attr('isModify');
            var modify = false;
            var publicationId = $form.attr('publicationId');
            var method = 'post';
            var path = originPath();
            var url = path + 'publications';
            if(isModify == '1' && !isNull(publicationId)) {
                method = 'put';
                url += '/' + publicationId;
                modify = true;
            }
            removeInputMessage($form);
            var values = getFormValue($form);

            var $authorNo = $form.find('select[name="author-no"]');
            var authorNo = null;
            if($authorNo.length > 0) {
                authorNo = $authorNo.select2('val');
            }
            var flag = true;
            if(isNull(authorNo)) {
                setInputMessage($authorNo, 'error', '请选择作者编号，若没有作者，请先添加作者');
                flag = false;
            }

            var $museumId = $form.find('select[name="museum-id"]');
            var museumId = null;
            if($museumId.length > 0) {
                museumId = $museumId.select2('val');
            }
            if(isNull(museumId)) {
                museumId = null;
//                setInputMessage($museumId, 'error', '请选择珍藏处，若没有珍藏处，请先添加珍藏处');
//                flag = false;
            }

            if(values != false) {
                values.authorNo = authorNo;
                values.museumId = museumId;
                var params = {
                    data: values,
                    isModify: modify,
                    $form: $form
                };
                if(flag == true) {
                    ajaxData(method, url, handleSavePublication, [], params, saveError);
                }
            } else {
                formNotCompleteNotice();
                loadingHide();
            }
        }
        function handleSavePublication(result, params) {
            var $form = params.$form;
            if(isOk(result)) {
                var isModify = params.isModify;
                var $form = params.$form;
                var data = result.data;
                if(!isNull(data) && isModify == true) {
                    var publicationId = data.id;
                    $form.attr('publicationId', publicationId);
                }
                saveSuccess();
            } else if(result.statusCode == ERRORS.EXIST['code']) {
                $form.find('input[name="no"]').focus();
                setInputMessage($form.find('input[name="no"]'), 'error', '该编号已存在，请重新填写编号');
            } else if(result.statusCode == ERRORS.NOT_ALLOWED['code']) {
                $form.find('input[name="no"]').focus();
                setInputMessage($form.find('input[name="no"]'), 'error', '不允许修改编号');
            } else {
                saveError();
            }
            loadingHide();
        }
    </script>
@stop