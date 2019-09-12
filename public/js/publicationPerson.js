/**
 * Created by xj on 11/4/16 11:08 AM.
 */

function bindEventToButtonInListView(params) {
    bindEventToFilterButton(params);
    bindEventToSearchList(params);
    bindEventToClearSearch(params);
    dialogShowInformation(params.type);
}
function bindEventToShowAnnounceEdit() {
    var $this = $(this);
    if(!isUndefined($this.attr('data'))) {
        var announceId = $this.attr('data');
        var url = '/announce/' + announceId + '/edit';
        var menuEditLi = $('#announce-edit');
        menuEditLi.parent().trigger('click');
        ajaxData('get', url, appendViewToContainer);
        $('#myAnnounce .remove').trigger('click');
    }
}
function bindEventToShowPublicationAuthor() {
    var $this = $(this);
    if(!isUndefined($this.attr('data'))) {
        var personId = $this.attr('data');
        var url = '/persons/' + personId;
        ajaxData('get', url, handleGetViewShowCallback, [], 'myPerson');
        $('#myPublication .remove').trigger('click');
    }
}
function bindEventToShowProduct() {
    var $this = $(this);
    if(!isUndefined($this.attr('data-product')) && !isUndefined($this.attr('data-cart-product'))) {
        var productId = $this.attr('data-product');
        var cartId = $this.attr('data-cart-product');
        var url = '/product/'+productId+'/cartProduct/'+cartId;
        ajaxData('get', url, handleGetViewShowCallback, [], 'myProduct');
        //$('#myOrder .remove').trigger('click');
    }
}
function bindEventToShowIwallAuthor() {
    var $this = $(this);
    if(!isUndefined($this.attr('data'))) {
        var personId = $this.attr('data');
        var url = '/persons/' + personId;
        ajaxData('get', url, handleGetViewShowCallback, [], 'myPerson');
        $('#myIwall .remove').trigger('click');
    }
}
function bindEventToShowAuthorPublication() {
    var $this = $(this);
    if(!isUndefined($this.attr('data'))) {
        var publicationId = $this.attr('data');
        var url = '/publications/' + publicationId;
        ajaxData('get', url, handleGetViewShowCallback, [], 'myPerson');
        // $('#myPerson .remove').trigger('click');
    }
}
function bindEventToShowAuthorPublications() {
    var $this = $(this);
    if(!isUndefined($this.attr('data'))) {
        var personId = $this.attr('data');
        if($('.dialog-lg').length > 0) {
            $('.dialog-lg').slideUp("slow", function () {
                $('.dialog-lg').remove();
                showAuthorPublications(personId);
            })
        } else {
            showAuthorPublications(personId);
        }
    }
}
function bindEventToShowAuthorIwalls() {
    var $this = $(this);
    if(!isUndefined($this.attr('data'))) {
        var personId = $this.attr('data');
        if($('.dialog-lg').length > 0) {
            $('.dialog-lg').slideUp("slow", function () {
                $('.dialog-lg').remove();
                showAuthorIwalls(personId);
            })
        } else {
            showAuthorIwalls(personId);
        }
    }
}
function showAuthorIwalls(personId) {
    $('#person-iwall-list').parent().trigger('click');
    var url = '/iwall?type=person&personId=' + personId +  '&take=12&skip=0';
    ajaxData('get', url, appendViewToContainer);
}
function showAuthorPublications(personId) {
    $('#person-publication-list').parent().trigger('click');
    var url = '/publications?type=person&personId=' + personId + '&take=12&skip=0';
    ajaxData('get', url, appendViewToContainer);
}
function bindEventToFilterButton(params) {
    $('#filter-type a').unbind('click').on('click', function () {
        if(!isNull($(this).attr('data-type'))) {
            var type = $(this).attr('data-type');
            //$('#filter-type').attr('data-type',type);//列表按钮的html不刷新时可用
            var url = getShowListUrl(params, type);
            if(!isNull(url)) {
                if(params.type == 'report' || params.type == 'advice' || params.type == 'folder') {
                    ajaxData('get', url,  function (view) {
                        $('#container').html(view);
                        convertUtcTimeToLocalTime('container');
                    })
                } else {
                    ajaxData('get', url, appendViewToContainer);
                }
            }
        }
    })
}

function bindEventToSearchList(params) {
    $('#list-search button').unbind('click').on('click', function () {
        var url = params.url;
        console.log(url)
        var searchText = getVal($(this).prev());
        var type = params.type;
        //if(type == 'publication' || type == 'person') {
            if(type == 'publication') {
                url += '&title=';
            } else if(type == 'person') {
                url += '&nick=';
            }else if(type == 'iwall'){
                url += '&title=';
            }else if(type == 'order'){
                url += '&title=';
            }else if(type == 'product'){
                url += '?title=';
            }
            if(!isNull(searchText)) {
                url += searchText + '&take=' + params.take + '&skip=0';
                ajaxData('get', url, appendViewToContainer);
            }
        //}
    })
}
function bindEventToClearSearch(params) {
    $('#list-search #search-clear').unbind('click').on('click', function () {
        var url = params.url + '&take=' + params.take + '&skip=0';
        ajaxData('get', url, appendViewToContainer);
    })
}
function getShowListUrl(params, dataType) {
    if(!isNull(params) && !isUndefined(params.type) && !isUndefined(params.take)) {
        var type = params.type;
        var take = params.take;
        var url = '';
        switch(type) {
            case 'publication' :
                url = 'publications?type=';
                if(dataType == 'unforbidden') {
                    url += 'view&isForbidden=false';
                } else if(dataType == 'forbidden') {
                    url += 'view&isForbidden=true';
                } else {
                    url += dataType;
                }
                break;
            case 'iwall':
                url = 'iwall?type='+dataType;
                break;
            case 'person' :
                url = 'persons?isForbidden=';
                if(dataType == 'forbidden') {
                    url += 'true';
                } else {
                    url += 'false';
                }
                break;
            case 'announce' :
                url += 'announceList?type=' + dataType;
                break;
            case 'order' :
                url += 'order?type=' + dataType;
                break;
            case 'statistics' :
                url += 'orderStatistics?type=' + dataType;
                break;
            case 'advice' :
                url += 'getAdviceList?' + dataType;
                break;
            case 'report' :
                url += params.url + '&' + dataType;
                break;
            case 'purchase' :
                url += 'purchase?type='+dataType;
                break;
            case 'reward':
                url += 'reward?type='+dataType;
                break;
            case 'refundRecord':
                url += 'refundRecord?type='+dataType;
                break;
            case  'refundRequest':
                url += 'refundRequest?auditing='+dataType;
                break;
            case  'reject':
                url += 'reject?auditing='+dataType;
                break;
            case  'refundList':
                url += 'refundList?type='+dataType;
                break;
            case 'product':
                url += 'new_pro/products?type='+dataType;
                break;
            default:
                if(!isNull(params.url)) {
                    url = params.url;
                    if(url.indexOf('?') == -1) {
                        url += '?';
                    } else {
                        url += '&';
                    }
                    url += dataType;
                    break;
                } else {
                    return null;
                }
        }
        url += '&take='+ take +'&skip=0';
        return url;
    }
}

function dialogShowInformation(type) {
    $('.data-list a:not(#pagination a)').on('click', function () {
        var $this = $(this);
        if(!isUndefined($this.attr('data')) && !isNull(type)) {
            var id = $this.attr('data');
            var url = null;
            var dialogId = null;
            if(type == 'publication') {
                url = 'publications/';
                dialogId = 'myPublication';
            } else if(type == 'person') {
                url = 'persons/';
                dialogId = 'myPerson';
            } else if(type == 'announce') {
                url = 'announce/';
                dialogId = 'myAnnounce';
            }else if(type == 'order') {
                url = 'order/';
                dialogId = 'myOrder';
            } else if(type == 'iwall'){
                url = 'iwall/';
                dialogId = 'myIwall';
            }else if(type == 'product'){
                url = 'new_pro/products/';
                dialogId = 'myProduct';
            }
            if(!isNull(url)) {
                url += id;
                ajaxData('get', url, handleGetViewShowCallback, [], dialogId);
            }
        }
    })
}

function handleGetViewShowCallback(result, dialogId) {
    if(!isNull(result)) {
        createDialogLargeBox(result, 'no-padding margin', dialogId);
        convertUtcTimeToLocalTime(dialogId);
        dialogReasonBox();
        $('#' + dialogId + ' img').unbind('error').on('error', function () {
            $(this).attr('src', '/img/default.png');
        })
    }
}
function dialogReasonBox() {
    $('.dialog-lg .dialog-body button').on('click', bindEventToShowReason);
}
function bindEventToShowReason() {
    var $this = $(this);
    var id = $this.attr('id');
    var type = $this.attr('type').trim();
    var targetId = $this.attr('data').trim();
    var dataType = $this.attr('data-type').trim();

    var params = {};
    params.data = {};
    params.data.type = type;
    params.data.targetId = targetId;
    params.dataType = dataType;

    var url = null;
    if(dataType == 'forbidden' || dataType == 'unForbidden') {
        url = '/' + dataType;
        params.forbiddenContent = $this.siblings('*[name="forbidden-content"]').text().trim();
    } else if(dataType == 'official' || dataType == 'unOfficial') {
        if(id == 'iwall-official') {
            url = '/iwall/' + targetId + '/' + dataType;
        } else {
            url = '/publications/' + targetId + '/' + dataType;
        }
    } else if(dataType == 'audit'){
        url = '/auditAnnounce/' + targetId;
    } else if(dataType == 'delete'){
        url = '/' + type + '/' + dataType + '/' + targetId;
    } else if(dataType == 'unGag') {
        url = '/persons/' + targetId +'/gag';
    } else if(dataType == 'gag') {
        url = '/personGag/' + targetId;
        //params.method = 'put';
        params.method = 'post';
    }
    if(!isNull(url)) {
        params.url = url;

        bootstrapQ.confirm({
            'id': 'myReason',
            'msg': $('#form-reason').html()
        }, postReason, '', dialogReasonBoxCallback, params);
    }
}
function dialogReasonBoxCallback(params) {
    if(!isUndefined(params)) {
        $('#myReason input[data-type="' + params.dataType + '"]').parent().removeClass('hide');
        if(!isNull(params.forbiddenContent)) {
            $('#myReason .content').text(params.forbiddenContent);
            $('#myReason .content').parent().parent().removeClass('hide').addClass('show');
            if(params.dataType == 'forbidden') {
                $('#myReason input[name="filters"]').parent().parent().removeClass('hide').addClass('show');
            }
        }
        var radios = $('#myReason input');
        var hasRadio = false;
        radios.each(function () {
            var $this = $(this);
            if(!$this.parent().hasClass('hide')) {
                hasRadio = true;
            }
        })
        if(hasRadio == false) {
            $('#myReason input[type="radio"]').parent().parent().parent().addClass('hide');
        }
        initPageElement('myReason');
        if(params.dataType != 'audit') {
            $('#myReason form input[type="radio"][name="reason-id"]').on('click', function () {
                var $this = $(this);
                var isChecked = $this.attr('checked');
                var value = $this.val().trim();
                if (isChecked == 'checked' && value == 'other') {
                    $('textarea[name="other"]').parent().removeClass('hide').addClass('show');
                } else {
                    $('textarea[name="other"]').parent().removeClass('show').addClass('hide');
                }
            })
            if($('#myReason input[data-type="' + params.dataType + '"]').length == 0) {
                $('#myReason form input[type="radio"][name="reason-id"]').eq(-1).prop('checked','checked').trigger('click');
            } else {
                $('#myReason input[data-type="' + params.dataType + '"]').eq(0).prop('checked','checked').trigger('click');
            }
        }
        if(/*params.dataType == 'gag' || */params.dataType == 'unGag') {
            $('#myReason .gag').removeClass('hide').addClass('show');
        }
    }
}
function postReason(params) {
    if(!isUndefined(params)) {
        var radios = $('#myReason form input[name="reason-id"]');
        var hasRadio = false;
        if(isUndefined(params.method)) {
            params.method = 'post';
        }
        radios.each(function () {
            var $this = $(this);
            var label = $this.parent().parent().parent();
            if (!label.hasClass('hide')) {
                hasRadio = true;
            }
        })
        if(hasRadio && isUndefined($('#myReason form input[name="reason-id"]:checked').val())) {
            messageAlert({
                'message': '请选择原因',
                'type': 'error'
            })
            return false;
        } else {
            var reasonId = hasRadio == false ? null : $('#myReason form input[name="reason-id"]:checked').val().trim();
            if(reasonId == 'other') {
                params.data.reasonType = 'text';
                params.data.reason = $('#myReason textarea[name="other"]').val().trim();
                if(isNull(params.data.reason)) {
                    messageAlert({
                        'message': '请输入原因',
                        'type': 'error'
                    })
                    return false;
                }
            } else if(params.dataType == 'reply') {
                params.data.replyType = 'id';
                params.data.reply = reasonId;
                params.data.memo = $('#myReason textarea[name="memo"]').val().trim();
            } else {
                params.data.reasonType = 'id';
                params.data.reason = reasonId;
            }
            if(params.data.type == 'announce'){
                params.data.memo = $('#myReason textarea[name="memo"]').val().trim();
            }
            if(!isNull(params.forbiddenContent) && params.dataType == 'forbidden') {
                var filter = $('#myReason input[name="filters"]');
                var filters = getForbiddenFilters(filter, params.forbiddenContent);
                if(filters == false) {
                    return false;
                } else {
                    params.data.filters = filters;
                }
            }
            if(/*params.dataType == 'gag' || */params.dataType == 'unGag') {
                params.data.type = getSelectVal($('#myReason form select[name="type"]'));
                var interval = getSelectVal($('#myReason form select[name="interval"]'));
                if(interval == '-1') {
                    params.data.isForever = true;
                } else {
                    params.data.interval = interval;
                    params.data.isForever = false;
                }
            }
            ajaxData(params.method, params.url, handlePostReason, [], params);
        }
        return false;
    }
}
function getForbiddenFilters(filter, forbiddenContent) {
    var filterValue = filter.val().trim();
    var data = [];
    if(isNull(filterValue)) {
        messageAlert({
            'message': '请输入被禁内容中的敏感词,多个敏感词,以逗号隔开',
            'type': 'error'
        })
        filter.focus();
        return false;
    } else {
        var filters = filterValue.split('，');
        for(var i in filters) {
            filters[i] = filters[i].split(',');
            for(var j in filters[i]) {
                var val = filters[i][j].trim();
                if(!isNull(val) && data.indexOf(val) == -1) {
                    if(forbiddenContent.indexOf(val) == -1) {
                        messageAlert({
                            'message': '敏感词 "'+ val +'" 不在该被禁内容中',
                            'type': 'error'/*,
                            'sticky': true*/
                        })
                        return false;
                    } else {
                        data.push(val);
                    }
                }
            }
        }
        if(data.length > 0) {
            return data;
        } else {
            return false;
        }
    }
}
function handlePostReason(result) {
    $('body').append(result);
    $('#myReason').modal('hide');
}