/**
 * Xj 2017-12-13
 * Product manage add and modify product javascript.
 */

function getIntroductionInfo(introductionId, $form) {
    if(!isNull(introductionId)) {
        var path = originPath();
        ajaxData('get', path + 'introductions/' + introductionId, parseGetIntroductionInfo);
    }
    function parseGetIntroductionInfo(result) {
        if(isOk(result)) {
            var data = result.data;
            if(!isNull(data)) {
                $form.attr('introductionId', data.id);
                if(!isNull(data.name)) {
                    $form.find('input[name="name"]').val(data.name);
                }
                if(!isNull(data.description)) {
                    $form.find('input[name="description"]').val(data.description);
                }
                $form.find('> .introduction').remove();
                if(!isNull(data.introductionContent)) {
                    var contents = data.introductionContent;
                    contents.sort(compare("index"));
                    var path = filePath();
                    for(var i in contents) {
                        var content = contents[i];
                        var type = 'text';
                        var temp = {
                            content: content.content,
                        };
                        if(isNull(content.content) && isNull(content.image_id)) {
                            continue;
                        }
                        if(!isNull(content.image_id)) {
                            type = 'image';
                            temp.imageId = content.image_id;
                            if(isNull(content.image) || isNull(content.image.norms)) {
                                continue;
                            }
                            var norms = content.image.norms;
                            var norm = getOneImageNormInNorms(norms, '1024_1024');
                            if(isNull(norm)) {
                                continue;
                            }
                            temp.imageSrc = path + norm.uri;
                        }
                        var $content = createIntroductionBox(type, temp);
                        if(!isNull($content)) {
                            $form.find('.control-group:not(.introduction)').eq(-1).before($content);
                        }
                    }
                    bindIntroductionBoxButtonEvent($form);
                }
            }
        }
    }
}

function getPublicationInfo(publicationId, $box) {
    if(!isNull(publicationId)) {
        var path = originPath();
        ajaxData('get', path + 'publications/' + publicationId, parseGetPublicationInfo);
    }
    function parseGetPublicationInfo(result) {
        if(isOk(result)) {
            var data = result.data;
            if(!isNull(data)) {
                $box.find('form[name="publication-info"], form[name="author-info"]').remove();
                $('#collapsePublication').find('form[name="publication-info"]').remove();
                var $publication = addPublicationInfo(data);
                if(!isNull($publication)) {
                    $('#collapsePublication').find('form').eq(0).before($publication.prop('outerHTML'));
                    $publication.addClass('span6');
                    $box.find('form').eq(-1).after($publication);
                }
                var pubIntroductions = data.publicationIntroduction;
                var $publicationForm = $('#collapsePublication').find('form[name="publication"]');
                $publicationForm.find('select[name="introduction-id"]').select2('val', '');
                $publicationForm.find('select[name="introduction-id"]').html('');
                var publicationIntroId = $publicationForm.attr('introductionId');
                if(!isNull(pubIntroductions)) {
                    var options = '';
                    for(var i in pubIntroductions) {
                        var intro = pubIntroductions[i];
                        if(i == 0) {
                            options += '<option value="">请选择引用的介绍</option>';
                        }
                        if(!isNull(intro.introduction)) {
                            var introName = intro.introduction.name;
                            if(isNull(introName)) {
                                introName = '未命名';
                            }
                            var introDescription = intro.introduction.description;
                            if(isNull(introDescription)) {
                                introDescription = '未添加描述';
                            }
                            options += '<option value="'+ intro.introduction.id +'" ';
                            if(!isNull(publicationIntroId) && publicationIntroId == intro.introduction.id) {
                                $publicationForm.find('select[name="introduction-id"]').select2('val', publicationIntroId);
                                options += 'selected="selected"';
                            }
                            options += '>' +
                                introName +
                                '-' +
                                introDescription +
                                '</option>';
                        }
                    }
                    if(!isNull(options)) {
                        $publicationForm.find('select[name="introduction-id"]').html(options);
                    }
                }

                $('#collapseAuthor').find('form[name="author-info"]').remove();
                var $author = addAuthorInfo(data.author);
                var authorIntroductions = null;
                if(!isNull($author)) {
                    $('#collapseAuthor').find('form').eq(0).before($author.prop('outerHTML'));
                    $author.addClass('span6');
                    $box.find('form').eq(-1).after($author);
                }
                if(!isNull(data.author)) {
                    authorIntroductions = data.author.authorIntroduction;
                }
                var $authorForm = $('#collapseAuthor').find('form[name="author"]');
                $authorForm.find('select[name="introduction-id"]').select2('val', '');
                $authorForm.find('select[name="introduction-id"]').html('');
                var authorIntroId = $authorForm.attr('introductionId');
                if(!isNull(authorIntroductions)) {
                    var options = '';
                    for(var i in authorIntroductions) {
                        var intro = authorIntroductions[i];
                        if(i == 0) {
                            options += '<option value="">请选择引用的介绍</option>';
                        }
                        if(!isNull(intro.introduction)) {
                            var introName = intro.introduction.name;
                            if(isNull(introName)) {
                                introName = '未命名';
                            }
                            var introDescription = intro.introduction.description;
                            if(isNull(introDescription)) {
                                introDescription = '未添加描述';
                            }
                            options += '<option value="'+ intro.introduction.id +'" ';
                            if(!isNull(authorIntroId) && authorIntroId == intro.introduction.id) {
                                $authorForm.find('select[name="introduction-id"]').select2('val', authorIntroId);
                                options += 'selected="selected"';
                            }
                            options += '>' +
                                introName +
                                '-' +
                                introDescription +
                                '</option>';
                        }
                    }
                    if(!isNull(options)) {
                        $authorForm.find('select[name="introduction-id"]').html(options);
                    }
                }
            }
        }
    }
}

function addPublicationInfo(publication, $box) {
    if(!isNull(publication)) {
        var $publication = createInfoForm('publication');
        if(!isNull($publication)) {
            if(!isNull(publication.no)) {
                createControlGroup('编号 : ', publication.no, $publication);
            }
            if(!isNull(publication.name)) {
                createControlGroup('名字 : ', publication.name, $publication);
            }
            if(!isNull(publication.lang)) {
                createControlGroup('名字翻译 : ', publication.lang, $publication);
            }
            if(!isNull(publication.year)) {
                createControlGroup('年代 : ', publication.year, $publication);
            }
            if(!isNull(publication.width)) {
                createControlGroup('宽（cm） : ', publication.width, $publication);
            }
            if(!isNull(publication.height)) {
                createControlGroup('高（cm） : ', publication.height, $publication);
            }
            if(!isNull(publication.description)) {
                createControlGroup('描述 : ', publication.description, $publication);
            }
            return $publication;
        } else {
            return null;
        }
    } else {
        return null;
    }
}

function addAuthorInfo(author) {
    if(!isNull(author)) {
        var $author = createInfoForm('author');
        if(!isNull($author)) {
            if(!isNull(author.no)) {
                createControlGroup('编号 : ', author.no, $author);
            }
            if(!isNull(author.name)) {
                createControlGroup('姓名 : ', author.name, $author);
            }
            if(!isNull(author.lang)) {
                createControlGroup('姓名翻译 : ', author.lang, $author);
            }
            if(!isNull(author.nationality)) {
                createControlGroup('国籍 : ', author.nationality, $author);
            }
            if(!isNull(author.introduction)) {
                createControlGroup('简介 : ', author.introduction, $author);
            }
            if(!isNull(author.saying)) {
                createControlGroup('名言 : ', author.saying, $author);
            }
            if(!isNull(author.feature)) {
                createControlGroup('艺术特色 : ', author.feature, $author);
            }
            if(!isNull(author.description)) {
                createControlGroup('描述 : ', author.description, $author);
            }
            return $author;
        }
    }
    return null;
}

function createControlGroup(label, controls, $box) {
    var $group = $('<div></div>').addClass('control-group');
    var $label = $('<label></label>').addClass('control-label');
    if(!isNull(label)) {
        $label.html(label);
    }
    var $controls = $('<div></div>').addClass('controls');
    if(!isNull(controls)) {
        $controls.html('<div class="content">'+ controls +'</div>');
    }

    $group.append($label, $controls);
    if(!isNull($box) && $box.length > 0) {
        $box.append($group);
    }
    return $group;
}

function createInfoForm(type) {
    if(isNull(type)) {
        return null;
    }
    var text = '';
    if(type == 'publication') {
        text = '作品';
    } else if(type == 'author') {
        text = '作者';
    }
    var $form = $('<form></form>').addClass('form-horizontal').attr('name', type + '-info');
    var $group = $('<div></div>').addClass('control-group');
    $group.append('<label class="control-label"><span class="label label-inverse">'+ text +'信息</span></label>');
    $form.append($group);
    return $form;
}

function saveProduct(event) {
    eventUtil.preventDefault(event);
    loadingShow();
    var $this = $(this);
    var $form = $this.closest('.widget-box');
    var productId = $form.attr('productId');
    var method ='post';
    var path = originPath();
    var url = path + 'products';
    var params = {};
    if(!isNull(productId)) {
        method = 'put';
        url += '/' + productId;
        params.productId = productId;
    }
    var productValues = getProductValues();
    var $introductions = $form.find('.collapse:not(#collapseProduct) form');
    var introductions = getProductIntroductions($introductions);
    var produceData = getProduceData($('#produceData'));
    if(productValues != false && introductions != false && produceData != false) {
        params.data = introductions;
        params.data.product = productValues;
        params.data.produceData = produceData;
        params.$form = $form.find('form[name="product"]');
        params.produceContent = $('#produceData');
        console.log(params);
        ajaxData(method, url, handleSaveProduct, [], params, saveError);
    } else {
        formNotCompleteNotice();
        loadingHide();
    }
}

function handleSaveProduct(result, params) {
    var $form = params.$form;
    if(isOk(result)) {
        var data = result.data;
        if(!isNull(data) && !isNull(params.productId)) {
            var productId = data.id;
            $form.closest('.widget-box').attr('productId', productId);
        }
        saveSuccess();
    } else if(result.statusCode == ERRORS.EXIST['code']) {
        $form.find('input[name="no"]').focus();
        var collapseId = $form.closest('.collapse').attr('id');
        if(!isNull(collapseId) && !$form.closest('.collapse').hasClass('in')) {
            $form.closest('.collapse').parent().find('a[href="#'+ collapseId +'"]').trigger('click');
        }
        setInputMessage($form.find('input[name="no"]'), 'error', '该编号已存在，请重新填写编号');
    } else if(result.statusCode == ERRORS.NOT_ALLOWED['code']) {
        $form.find('input[name="no"]').focus();
        var collapseId = $form.closest('.collapse').attr('id');
        if(!isNull(collapseId) && !$form.closest('.collapse').hasClass('in')) {
            $form.closest('.collapse').parent().find('a[href="#'+ collapseId +'"]').trigger('click');
        }
        setInputMessage($form.find('input[name="no"]'), 'error', '不允许修改编号');
    } else {
        saveError();
    }
    loadingHide();
}

function saveIntroduction(event) {
    eventUtil.preventDefault(event);
    loadingShow();
    var $this = $(this);
    var $form = $this.closest('form');
    var introductionId = $form.attr('introductionId');
    var method ='post';
    var path = originPath();
    var url = path + 'introductions';
    var params = {};
    if(!isNull(introductionId)) {
        method = 'put';
        url += '/' + introductionId;
        params.introductionId = introductionId;
    }
    removeInputMessage($form);
    var introductions = getProductIntroductions($form, false)
    var $type = $form.find('select[name="introduction-type"]');
    var type = $type.select2('val');
    if(isNull(type)) {
        setInputMessage($type, 'error', '请选择介绍类型');
    } else if(introductions != false && !isNull(type)) {
        if(isNull(introductions) || introductions.length == 0 ||
            isNull(introductions[0].contents) || introductions[0].contents.length == 0) {
            bootstrapQ.alert('请添加图片或文字，没有介绍内容的将不保存');
            loadingHide();
        } else {
            var introduction = introductions[0];
            introduction.type = type;
            params.data = introduction;
            params.$form = $form.find('form[name="product"]');
            ajaxData(method, url, handleSaveIntroduction, [], params, saveError);
        }
    } else {
        formNotCompleteNotice();
        loadingHide();
    }
}

function handleSaveIntroduction(result, params) {
    var $form = params.$form;
    if(isOk(result)) {
        var data = result.data;
        if(!isNull(data) && !isNull(params.productId)) {
            var productId = data.id;
            $form.closest('.widget-box').attr('productId', productId);
        }
        saveSuccess();
    } else {
        saveError();
    }
    loadingHide();
}

function getProductIntroductions($form, isProductIntro) {
    if(isNull(isProductIntro) && isProductIntro != false) {
        isProductIntro = true;
    }
    if(isNull($form) || $form.length == 0) {
        console.log('$form is null');
    } else {
        var introductionIds = null;
        var introductions = null;
        var isTrue = true;
        $form.each(function () {
            var $this = $(this);
            var type = $this.attr('name');
            removeInputMessage($this);
            var values = getFormValue($this);
            var flag = true;
            if(values == false) {
                flag = false;
            }
            var contents = null;
            if(isProductIntro == true) {
                var introductionId = $this.attr('introductionId');
            } else {
                var introductionId = null;
            }
            if(isNull(introductionId)) {
                introductionId = null;
                contents = getIntroductions($this, true);
            }
            if(flag == true) {
                if(!isNull(introductionId)) {
                    if(isNull(introductionIds)) {
                        introductionIds = [];
                    }
                    introductionIds.push(introductionId);
                }
                if(!isNull(contents) && contents.length != 0) {
                    if(isNull(introductions)) {
                        introductions = [];
                    }
                    if(isNull(values.name)) {
                        setInputMessage($this.find('input[name="name"]'), 'error', '若有详细内容文字或图片时，此字段不能为空');
                        isTrue = false;
                    }
                    values.type = type;
                    values.contents = contents;
                    introductions.push(values);
                }
            } else {
                var collapseId = $this.closest('.collapse').attr('id');
                if(!isNull(collapseId) && !$this.closest('.collapse').hasClass('in')) {
                    $this.closest('.collapse').parent().find('a[href="#'+ collapseId +'"]').trigger('click');
                }
                // $this.closest('.collapse').addClass('in');
                isTrue = false;
            }
        });
        if(isTrue == false) {
            return false;
        } else {
            if(isProductIntro == true) {
                return {
                    introductionIds: introductionIds,
                    introductions: introductions
                }
            } else {
                return introductions;
            }
        }
    }
}

function getIntroductions($form, isSubmit) {
    if(isNull(isSubmit)) {
        isSubmit = false;
    }
    if(isNull($form) || $form.length == 0) {
        console.log('$form is null');
        return null;
    } else {
        var introductions = [];
        var index = 0;
        var $introductions = $form.find('> .introduction');
        $introductions.each(function () {
            var $this = $(this);
            var type = null;
            var imageId = null;
            var imageSrc = null;
            var content = null;
            if($this.hasClass('text')) {
                type = 'text';
            } else if($this.hasClass('image')) {
                type = 'image';
                imageId = $this.attr('imageId');
                imageSrc = $this.find('img').attr('src');
            }
            var $textarea = $this.find('textarea');
            if($textarea.length > 0) {
                content = $textarea.val();
                if(!isNull(content)) {
                    content = content.trim();
                }
            }
            if(!isNull(imageId) || !isNull(content)) {
                if(isSubmit == false) {
                    introductions.push({
                        imageId: imageId,
                        imageSrc: imageSrc,
                        content: content,
                        type: type,
                        index: index
                    })
                } else {
                    introductions.push({
                        imageId: imageId,
                        content: content,
                        index: index
                    })
                }
                index ++;
            }
        });
        if(introductions.length == 0) {
            introductions = null;
        }
        return introductions;
    }
}

function getProductValues() {
    var $box = $('#collapseProduct');
    var $productForm = $box.find('form[name="product"]');
    var $coreForm = $box.find('form[name="core"]');
    var flag = true;

    removeInputMessage($productForm);
    removeInputMessage($coreForm);
    var productValues = getFormValue($productForm);
    if(productValues != false) {
        if(!isNull(productValues.isSell)) {
            if(productValues.isSell == '0') {
                productValues.isSell = true;
            } else {
                productValues.isSell = false;
            }
        }
    } else {
        flag = false;
    }

    var $shopId = $productForm.find('select[name="shop-id"]');
    var shopId = null;
    if($shopId.length > 0) {
        shopId = $shopId.select2('val');
    }
    if(isNull(shopId)) {
        shopId = null;
    }

    var $border = $productForm.find('select[name="border"]');
    var border = null;
    if($border.length > 0) {
        border = $border.select2('val');
    }
    if(isNull(border)) {
        border = null;
    }

    var $productImage = $productForm.find('.controls[name="imageId"] img');
    var productImageId = null;
    if($productImage.length > 0) {
        productImageId = $productImage.attr('imageId');
    } else {
        setInputMessage($productForm.find('.controls[name="imageId"] .upload-file'), 'error', '请上传产品的图片');
        flag = false;
    }

    var coreValues = getFormValue($coreForm);
    if(coreValues == false) {
        flag = false;
    }
    var $publicationId = $coreForm.find('select[name="publication-id"]');
    var publicationId = $publicationId.select2('val');
    if(isNull(publicationId)) {
        setInputMessage($publicationId, 'error', '请选择作品，若没有作品，请先添加');
        publicationId = null;
        flag = false;
    }

    var $coreMaterial = $coreForm.find('select[name="material"]');
    var coreMaterial = null;
    if($coreMaterial.length > 0) {
        coreMaterial = $coreMaterial.select2('val');
    }
    if(isNull(coreMaterial)) {
        setInputMessage($coreMaterial, 'error', '请选择画芯材料');
        coreMaterial = null;
        flag = false;
    }

    if(flag == true) {
        coreValues.publicationId = publicationId;
        coreValues.material = coreMaterial;

        productValues.border = border;
        productValues.shopId = shopId;
        productValues.imageId = productImageId;
        productValues.core = coreValues;
        return productValues;
    } else {
        var collapseId = $box.attr('id');
        if(!isNull(collapseId) && !$box.hasClass('in')) {
            $box.parent().find('a[href="#'+ collapseId +'"]').trigger('click');
        }
        // $box.addClass('in');
        return false;
    }
}

function getProduceData() {
    var $form = $('#produceData');
    removeInputMessage($form);
    var values = getFormValue($form);
    var flag = true;
    if(values == false) {
        flag = false;
    }

    var $coreMaterial = $form.find('select[name="coreMaterial"]');
    var coreMaterial = null;
    if($coreMaterial.length > 0) {
        coreMaterial = $coreMaterial.select2('val');
    }
    if(isNull(coreMaterial)) {
        setInputMessage($coreMaterial, 'error', '请选择画芯材料');
        coreMaterial = null;
        flag = false;
    }

    var $coating = $form.find('select[name="coating"]');
    var coating = null;
    if($coating.length > 0) {
        coating = $coating.select2('val');
    }
    if(isNull(coating)) {
        setInputMessage($coating, 'error', '请选择涂层类型');
        coating = null;
        flag = false;
    }

    if(flag == true) {
        values.coreMaterial = coreMaterial;
        values.coating = coating;
        return values;
    } else {
        return false;
    }
}function productNoBlurEvent(event) {
    var val = $(this).val();
    if(!isNull(val) && !isNull(val.trim())) {
        val = val.trim();
        var path = originPath();
        var url = path + 'products/' + val + '/produceParams';
        ajaxData('get', url, function(result) {
            // console.log(result);
            var $form = $('#produceData');
            if(!isNull(result.data)) {
                for(var i in result.data) {
                    var val = result.data[i];
                    if(isNull(val)) {
                        val = '';
                    }
                    var name = snakeToCamelStr(i);
                    var $input = $form.find('input[name="'+ name +'"]');
                    var $select = $form.find('select[name="'+ name +'"]');
                    if($input.length > 0) {
                        $input.val(val);
                    } else if($select.length > 0 && !isNull(val)) {
                        if($select.find('option[value="'+ val +'"]').length == 0) {
                            $select.append('<option value="'+ val +'">' + val + '</option>');
                        }
                        $select.select2('val', val);
                    }
                }
            } else {
                $form.find('input').val('');
                $form.find('select').select2('val', '');
            }
        })
    }
}

function uploadImageError() {
    messageAlert({
        message: '上传图片失败',
        type: 'error'
    });
    loadingHide();
}

function bindIntroductionBoxButtonEvent($box) {
    if(isNull($box) || $box.length == 0) {
        console.log('$box is null');
        return false;
    }
    $box.find('.delete').on('click', function () {
        var $this = $(this);
        var $group = $this.closest('.introduction');
        var $image = $group.parent();
        bootstrapQ.confirm('确定要删除？', function () {
            if($image.hasClass('box')) {
                $image.find('.add-title[hasTitle="true"]').text('添加标题').removeAttr('hasTitle');
            }
            var $form = $this.closest('form');
            clearFormIntroductionId($form);
            $group.remove();
        })
    });
    $box.find('.replace-image input[type="file"]').on('change', uploadIntroductionImageEvent);
    $box.find('.add-title').on('click', function() {
        var $this = $(this);
        var $btnGroup = $this.closest('.btn-group');
        if($this.attr('hasTitle') == 'true') {
            var $text = $btnGroup.parent().find('.introduction');
            $text.find('textarea').focus();
        } else {
            var $box = createIntroductionBox('text');
            $btnGroup.after($box);
            bindIntroductionBoxButtonEvent($box);
            $this.attr('hasTitle', 'true');
            $this.text('修改标题');
        }
    });
    $box.find('.reorder').on('click', reorderIntroductionContent);
    $box.find('input, textarea').on('input propertychange', function () {
        var $this = $(this);
        var $form = $this.closest('form');
        clearFormIntroductionId($form);
    });
}

function clearFormIntroductionId($form) {
    if(isNull($form) || $form.length == 0) {
        return null;
    }
    if(!isNull($form.attr('name'))) {
        $form.removeAttr('introductionId');
        $form.find('select[name="introduction-id"]').select2('val', '');
    }
}

function reorderIntroductionContent() {
    var $this = $(this);
    var $form = $this.closest('form');
    var params = {
        $form: $form
    }
    var introductions = getIntroductions($form, false);
    if(isNull(introductions)) {
        messageAlert({
            message: '请填写文字内容或上传图片',
            type: 'error'
        })
        return false;
    }
    var sortList = '<ul id="sort-list" class="sort-list">';
    for(var i in introductions) {
        var introduction = introductions[i];
        var type = introduction.type;
        var index = introduction.index;
        var content = type == 'image' ? introduction.imageSrc : introduction.content;
        sortList += '<li class="clearfix">';
        sortList += '<span class="pull-left icon-fullscreen sort-icon"></span>';
        sortList += '<span class="pull-left reorder-content" index="'+ index +'">';
        if(type == 'text') {
            sortList += content + '</span>';
            sortList += '<span class="pull-right icon-font sort-icon"></span>';
        } else {
            sortList += '<img src="' + content + '" data-url="' + content + '" /></span>';
            sortList += '<span class="pull-right icon-picture sort-icon"></span>';
        }
        sortList += '</li>';
    }
    bootstrapQ.confirm({'title':'排序','msg': sortList},function () {
        var orderList = orderResultList(introductions);
        var $form = params.$form;
        $form.find('.introduction').remove();
        var $foot = $form.find('.control-group:not(.introduction)').eq(-1);
        for(var i in orderList) {
            var li = orderList[i];
            var $group = createIntroductionBox(li.type, li);
            $foot.before($group);
        }
        clearFormIntroductionId($form);
        bindIntroductionBoxButtonEvent($form);
    }, null, null, params);
    $('.sort-list').sortable({
        /*axis: "y",
         containment: "parent",
         cursor: "move",
         forcePlaceholderSize: true,
         opacity: 0.5*/
    }).bind('sortupdate', function(event, ui) {
        // console.log(ui);
    });
}

function orderResultList(arrList) {
    var orderList = new Array();
    var $lis = $('#sort-list li');
    $lis.each(function (inx) {
        var $this = $(this);
        var $reorderCon = $('.reorder-content', $this);
        var index = Number($reorderCon.attr('index'));
        for(var i in arrList) {
            if(index == arrList[i].index) {
                // console.log('equal index: ' + index);
                orderList[inx] = arrList[i];
            }
        }
    })
    for(var i in orderList) {
        orderList[i].index = i;
    }
    return orderList;
}

function uploadProductImageEvent() {
    loadingShow();
    var $this = $(this);
    var $button = $this.closest('.upload-file');
    var params = {
        $button: $button,
        input: this
    };
    var formData = filePostData(this.files[0]);
    var path = originPath();
    ajaxImageData(formData, path + 'imageFiles', handleUploadProductImage, params, uploadImageError);
}

function handleUploadProductImage(result, params) {
    var isError = true;
    if(!isNull(params) && !isNull(params.input)) {
        params.input.value = '';
    }
    if((isOk(result) || result.statusCode == ERRORS.EXIST['code']) && !isNull(result.data)) {
        var data = result.data;
        var norms = data.norms;
        var norm = getOneImageNormInNorms(norms, '1024_1024');
        if(!isNull(norm)) {
            var imageId = data.id;
            var path = filePath();
            var uri = path + norm.uri;
            var $button = params.$button;
            if($button.next('img').length > 0) {
                $button.next('img').attr('imageId', imageId).attr('src', uri);
                // console.log($button.next('img'));
            } else {
                $button.after('<img imageId="'+ imageId +'" src="'+ uri +'">');
            }
            messageAlert({
                message: '上传图片成功',
                type: 'success'
            })
            isError = false;
        }
    }
    if(isError == true) {
        messageAlert({
            title: '上传图片失败',
            message: result.message,
            type: 'error'
        });
    }
    loadingHide();
}

function uploadIntroductionImageEvent() {
    loadingShow();
    var $this = $(this);
    var $group = $this.closest('.control-group');
    var params = {
        $group: $group,
        input: this
    };
    var $button = $this.closest('.upload-file');
    if($button.hasClass('replace-image')) {
        params.action = 'replace';
    } else {
        params.action = 'add';
    }
    var formData = filePostData(this.files[0]);
    var path = originPath();
    ajaxImageData(formData, path + 'imageFiles', handleUploadIntroductionImage, params, uploadImageError);
}

function handleUploadIntroductionImage(result, params) {
    var isError = true;
    if(!isNull(params) && !isNull(params.input)) {
        params.input.value = '';
    }
    if((isOk(result) || result.statusCode == ERRORS.EXIST['code']) && !isNull(result.data)) {
        var data = result.data;
        var norms = data.norms;
        var norm = getOneImageNormInNorms(norms, '1024_1024');
        if(!isNull(norm)) {
            var imageId = data.id;
            var path = filePath();
            var uri = path + norm.uri;
            var $group = params.$group;
            if(params.action == 'add') {
                var $box = createIntroductionBox('image');
                $group.before($box);
                bindIntroductionBoxButtonEvent($box);
            } else {
                var $box = $group;
            }
            $box.find('img').attr('src', uri);
            if(imageId != $box.attr('imageId')) {
                clearFormIntroductionId($box.closest('form'));
            }
            $box.attr('imageId', imageId);
            messageAlert({
                message: '上传图片成功',
                type: 'success'
            })
            isError = false;
        }
    }
    if(isError == true) {
        messageAlert({
            title: '上传图片失败',
            message: result.message,
            type: 'error'
        });
    }
    loadingHide();
}

function createIntroductionBox(type, obj) {
    if(isNull(type)) {
        console.log('type is null');
    }
    var $group = $('<div></div>').addClass('control-group introduction');
    var $introduction = $('<div></div>').addClass('box');
    var $buttons = $('<div></div>').addClass('btn-group');
    var $showButton = $('<button></button>').addClass('btn btn-mini dropdown-toggle').attr('data-toggle', 'dropdown')
        .append('<span class="icon-edit"></span>')
        .append('<span class="caret"></span></button>');
    var $dropDownMenu = $('<ul></ul>').addClass('dropdown-menu');
    var $deleted = $('<li></li>').append('<a class="delete">删除</a>');
    if(type != 'text') {
        var $addTitle = $('<li></li>').append('<a class="add-title">添加标题</a>');
        var $replace = $('<li></li>').append('<a class="upload-file replace-image">替换图片<input type="file"></a>');
        $dropDownMenu.append($addTitle, $replace);
    }
    var $reorder = $('<li></li>').append('<a class="reorder">排序</a>');
    $dropDownMenu.append($reorder, $deleted);
    $buttons.append($showButton, $dropDownMenu);
    $introduction.append($buttons);

    if(type == 'text') {
        var $text = $('<textarea></textarea>').addClass('span12').attr('rows', 4);
        if(!isNull(obj) && !isNull(obj.content)) {
            $text.val(obj.content);
        }
        $introduction.append($text);
        $group.addClass('text');
    } else {
        var $img = $('<img />');
        if(!isNull(obj) && !isNull(obj.imageSrc) && !isNull(obj.imageId)) {
            $img.attr({
                src: obj.imageSrc
            });
            $group.attr({
                imageId: obj.imageId
            })
        }
        if(!isNull(obj) && !isNull(obj.content)) {
            var $imageTitle = createIntroductionBox('text', obj);
            $introduction.append($imageTitle);
        }
        $introduction.append($img);
        $group.addClass('image');
    }
    $group.append($introduction);
    return $group;
}