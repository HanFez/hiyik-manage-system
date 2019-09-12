/**
 * Created by xj on 11/9/16 3:17 PM.
 */

function jumpToAddData(self) {
    var type = $(self).attr('data-type');
    if(!isNull(type)) {
        $('#' + type + '-add').trigger('click');
    }
}

function showRowInfo($btn, data, uri, $dataTable) {
    if(!isNull(data)) {
        var html = createRowInfo(data, uri);
        var title = '详情';
        var modalId = 'myInfoModal';
        var className = 'modal-lg';
        var showEditBtn = true;
        if($btn.siblings('.btn-modify').length == 0) {
            showEditBtn = false;
        }

        if(showEditBtn == false) {
            bootstrapQ.alert({
                id: modalId,
                title: title,
                className: className,
                msg: html
            }, null, null, function () {
                //callback
                convertUtcTimeToLocalTime(modalId, true);
            });
        } else {
            bootstrapQ.confirm({
                id: modalId,
                title: title,
                className: className,
                msg: html,
                okbtn: '修改'
            }, function () {
                // edit
                console.log('edit');
                showRowModifyView($btn, data, uri);
            }, null, function () {
                //callback
                convertUtcTimeToLocalTime(modalId, true);
            });
        }
    } else {
        bootstrapQ.alert('出错啦，没有任何数据');
    }
}

function createRowInfo(data, uri, isParent, type) {
    if(isNull(data) || data.length == 0 || isNull(uri) || uri.length == 0) {
        return null;
    }
    if(isNull(isParent) || isParent != false) {
        isParent = true;
    }
    var oPath = originPath();
    var fPath = filePath();
    var noData = '无';
    var isOrigin = false;
    if(uri.indexOf(oPath) > -1) {
        isOrigin = true;
    }
    var uris = uri.split('/');
    var flag = false;
    var listType = null;
    if(!isNull(uris) && uris.length > 0) {
        if (!isNull(uris[1])) {
            listType = uris[1];
        }
        if (isOrigin == true) {
            if(!isNull(uris[2])) {
                listType = uris[2];
                if (listType == 'realProducts' || listType == 'introductions') {
                    flag = true;
                }
            } else {
                listType = null;
            }
        }
    }
    if(isParent == false && flag == false) {
        return null;
    }

    if(!isNull(type)) {
        if(type == 'image') {
            if(!isNull(data.norms)) {
                var norm = getOneImageNormInNorms(data.norms, '128_128');
                if(!isNull(norm)) {
                    return '<img src="'+ fPath + norm.uri +'" />';
                } else {
                    return noData;
                }
            }
        } else if(type == 'introductionContent') {
            data.sort(compare("index"));
            var hasIntroContent = false;
            for(var i in data) {
                if(!isNull(data[i].content) || !isNull(data[i].image)) {
                    hasIntroContent = true;
                }
            }
            if(hasIntroContent == false) {
                return null;
            }
        } else if(type == 'detail' && isOrigin == true && listType == 'realProducts') {
            if(isNull(data)) {
                return null;
            }
            var detail = data['detail'];
            if(isUndefined(detail)) {
                detail = data;
            } else if(isNull(detail)) {
                return null;
            }
            var str = '';
            for(var i in detail) {
                if(!isNull(detail[i])) {
                    var title = transStr(i, trans_table);
                    str += '<div><span class="label label-important" >'+ title +'</span><br>';
                    var values = detail[i];
                    for(var j in values) {
                        str += '<span style="margin-left: 10px">' + values[j] + '</span><br>';
                    }
                    str += '</div>';
                }
            }
            if(isNull(str)) {
                return null;
            } else {
                return str;
            }
        } else if(type == 'orderRealProduct' && isOrigin == true && listType == 'realProducts'){
            return null;
        }
    }


    var html = '<table class="table table-hover table-bordered table-info">';
    if(isParent == true) {
        html += '<thead>' +
        '<tr>' +
        '<th class="name">属性</th>' +
        '<th class="value">值</th>' +
        '</tr>' +
        '</thead>'
    }
    html += '<tbody>';
    // var html = '<dl class="dl-horizontal">';
    // var html = '<form class="form-horizontal">';
    var flag = '';
    for(var key in data) {
        if(isOrigin == true && listType == 'realProducts') {
            var hideKeys = ['id', 'created_at', 'updated_at', 'is_active', 'is_removed', 'hiyik_origin_uri', 'orderRealProduct'];
            if($.inArray(key, hideKeys) > -1 || key.indexOf('_id') > -1) {
                continue;
            }
        }
        var value = transValue(key, data[key]);
        var show = null;
        if(!isNull(type) && type == 'introductionContent') {
            if(!isNull(value.content) || !isNull(value.image)){
                var introValue = '';
                if(!isNull(value.content)) {
                    introValue += value.content;
                }
                if(!isNull(value.image)) {
                    var tempImage = createRowInfo(value.image, uri, false, 'image');
                    if(!isNull(introValue)) {
                        introValue += '<br />';
                    }
                    if(!isNull(tempImage)) {
                        introValue += tempImage;
                    }
                }
                if(isNull(introValue) || introValue.length == 0) {
                    continue;
                } else {
                    show = '<tr><td colspan="2">' + introValue + '</td></tr>';
                }
            } else {
                continue;
            }
        } else {
            if (typeof(value) == 'object' && !isNull(value)) {
                value = createRowInfo(value, uri, false, key);
                if (isNull(value)) {
                    continue;
                }
            }
            var lang = transStr(key, trans_table);
            if (key == 'created_at' || key == 'updated_at') {
                if (!isNull(value)) {
                    value = '<span class="time-utc">' + value + '</span>';
                }
            }
            if(isOrigin == true && listType == 'realProducts') {
                if(key == 'detail') {
                    lang = '不合格处';
                } else if(key == 'flannelSize') {
                    lang = '绒布宽度';
                } else if(key == 'originManage') {
                    lang = '生产溯源';
                    value = '<a href="'+ value +'" target="_blank">'+ value +'</a>';
                } else if(key == 'originHiyik') {
                    lang = 'HIYIK溯源';
                    value = '<a href="'+ value +'" target="_blank">'+ value +'</a>';
                } else if(key == 'produced') {
                    lang = '再次生产';
                    if(isNull(value)) {
                        continue;
                    }
                }
            }
            if (key == 'uri' && listType != 'getAll' && isOrigin == true) {
                if (listType == 'realProducts' || listType == 'shops') {
                    if (!isNull(value) && value.indexOf('href') == -1) {
                        value = '<a href="' + value + '" target="_blank">' + value + '</a>';
                    }
                    if (listType == 'realProducts') {
                        lang = '售卖地址';
                    } else if (listType == 'shops') {
                        lang = '店铺地址';
                    }
                }
            }
            if (isNull(value)) {
                value = noData;
            }

            show = '<tr><td class="name">' + lang + '</td>' +
                '<td>' + value + '</td></tr>';
            // show = '<dt>'+ lang +'</dt>' +
            //     '<dd>'+ value +'</dd>';
            // show = showControlGroup(lang, key, value);
        }
        if(key == 'created_at' || key == 'updated_at' || key == 'is_active' || key == 'is_removed') {
            flag += show;
        } else {
            html += show;
        }
    }
    html += flag;
    html += '</tbody></table>';
    // html += '</dl>';
    // html += '</form>';
    return html;
}

function showRowRelation($btn, data, uri, $dataTable) {
    if(isNull(data) || isNull(uri)) {
        return false;
    }
    var id = data.id;
    if(isNull(id)) {
        return false;
    }
    var oPath = originPath();
    var uris = uri.split('/');
    if(uri.indexOf(oPath) == -1 && uri.indexOf('/relation/') == -1) {
        if(!isNull(uri) && !isNull(uris[1])) {
            var type = uris[1];
            if (type == 'role') {
                $('#role-privilege-list').parent().trigger('click');
                ajaxData('get', 'role/relation/' + id, appendViewToContainer);
            } else if (type == 'manager') {
                $('#manager-role-list').parent().trigger('click');
                ajaxData('get', 'manager/relation/' + id, appendViewToContainer);
            } else if (type == 'core') {
                $('#core-param-list').parent().trigger('click');
                ajaxData('get', 'coreParams/' + id, appendViewToContainer);
            } else if (type == 'pattern') {
                $('#pattern-param-list').parent().trigger('click');
                ajaxData('get', 'patternParams/' + id, appendViewToContainer);
            }
        }
    }
}

function showRowModifyView($btn, data, uri, $dataTable) {
    if(isNull(data) || isNull(uri)) {
        console.log('data or uri is null');
        return false;
    }
    var id = data.id;
    if(isNull(id)) {
        console.log('id is null');
        return false;
    }
    var obj = getMenuAndEditUri(uri);
    if(isNull(obj)) {
        return false;
    }
    var type = obj.menuId;
    var menuEditLi = $('#' + type + '-edit');
    menuEditLi.parent().trigger('click');
    var url = obj.uri + '/' + id +'/edit';
    /*switch(type) {
        case 'patternDemiBack':
            url = 'pattern/' + id + '/demiBack/edit';
            break;
        case 'patternDemiBorder':
            url = 'pattern/' + id + '/demiBorder/edit';
            break;
        case 'patternDemiFront':
            url = 'pattern/' + id + '/demiFront/edit';
            break;
        case 'patternDemiFrame':
            url = 'pattern/' + id + '/demiFrame/edit';
            break;
        case 'patternDemiCore':
            url = 'pattern/' + id + '/demiCore/edit';
            break;
    }*/
    ajaxData('get', url, function (result, url) {
        var params = null;
        if(url.indexOf(originPath() + 'introductions') > -1) {
            params = {
                init: {
                    file: false
                }
            }
        }
        appendViewToContainer(result, params);
        $('#container a[type="submit"]').attr('url', obj.uri + '/' + id);//url => obj.uri
    }, [], url);
}

function getMenuAndEditUri(uri) {
    if(isNull(uri)) {
        console.log('uri is null');
        return null;
    }
    if(uri.indexOf('/relation/') > -1) {
        console.log('not modify data');
        return null;
    }
    var oPath = originPath();
    var uris = uri.split('/');
    var modifyViewUri = null;
    var menuId = null;
    if(uri.indexOf(oPath) > -1) {
        var str = 'tb-';
        if(!isNull(uris[2])) {
            var type = uris[2];
            var tempType = type.substring(0, type.lastIndexOf('s'));
            if(type == 'realProduct') {
                menuId = str + 'real-product';
            } else {
                menuId = str + tempType;
            }
            modifyViewUri = uri;
        } else {
            console.log('uri is invalid');
            return null;
        }
    } else {
        if(!isNull(uris[1])) {
            var type = uris[1];
            var tempType = type.substring(0, type.lastIndexOf('s'));
            if(type == 'shape') {
                modifyViewUri = 'shape';
            } else {
                modifyViewUri = uri;
            }
            menuId = tempType;
        } else {
            console.log('uri is invalid');
            return null;
        }
    }
    return {
        menuId: menuId,
        uri: modifyViewUri
    }
}

function deleteRowEvent($btn, data, uri, $dataTable) {
    if (isNull(data) || isNull(uri)) {
        console.log('data or uri is null');
        return false;
    }
    var id = data.id;
    if (isNull(id)) {
        console.log('id is null');
        return false;
    }
    var url = uri;
    bootstrapQ.confirm({
        'id': 'deleteConfirm',
        'msg': trans_admin.sure + trans_admin.delete + ' id=' + id + ' ' + trans_admin.entry
    }, function () {
        if(!isNull(url)) {
            var params = {};
            params.data = {};
            params.data.ids = [id];
            ajaxData('delete', url + '/del', function (result) {
                if(typeof(result) == 'String') {
                    $('#container').append(result);
                } else if(result.statusCode == 0) {
                    messageAlert({
                        'message': trans_admin.delete + ' id= ' + id + trans_admin.success,
                        'type': 'success'
                    });
                } else {
                    var message = result.message;
                    messageAlert({
                        'message': message,
                        'type': 'error'
                    })
                }
                $dataTable.ajax.reload(); //1.10之后
            }, [], params);
        }
    })
}

function recoverRowEvent($btn, data, uri, $dataTable) {
    if (isNull(data) || isNull(uri)) {
        console.log('data or uri is null');
        return false;
    }
    var id = data.id;
    if (isNull(id)) {
        console.log('id is null');
        return false;
    }
    var url = uri;
    bootstrapQ.confirm({
        'id': 'deleteConfirm',
        'msg': trans_admin.sure + trans_admin.recover + ' id=' + id + ' ' + trans_admin.entry
    }, function () {
        if(!isNull(url)) {
            var params = {};
            params.data = {};
            params.data.ids = [id];
            ajaxData('post', url + '/recover', function (result) {
                if(typeof(result) == 'String') {
                    $('#container').append(result);
                } else if(result.statusCode == 0) {
                    messageAlert({
                        'message': trans_admin.recover + ' id= ' + id + trans_admin.success,
                        'type': 'success'
                    });
                } else {
                    var message = result.message;
                    messageAlert({
                        'message': message,
                        'type': 'error'
                    })
                }
                $dataTable.ajax.reload(); //1.10之后
            }, [], params);
        }
    })
}

function bulkOperation(self) {
    var type = $(self).attr('data-type');
    var url = $(self).attr('data-url');
    if(isNull(url)) {
        return false;
    }
    var checkbox = $('tbody input[type="checkbox"]');
    var ids = [];
    var method = null;
    switch (type) {
        case 'delete':
            method = 'delete';
            url += '/del';
            break;
        case 'recover':
            method = 'post';
            url += '/recover';
            break;
        case 'refund':
            method = 'post';
        break;
    }
    checkbox.each(function () {
        var $this = $(this);
        if($this.parent().hasClass('checked')) {
            var id = getVal($this);
            ids.push(id);
        }
    });
    if(ids.length == 0) {
        bootstrapQ.alert(trans_admin.enterData);
    } else if(!isNull(method)) {
        bootstrapQ.confirm({
            'id': 'confirm',
            'msg': '确定批量操作数据？'
        }, function () {
            var params = {};
            params.data = {};
            params.data.ids = ids;
            ajaxData(method, url, handleBulkOperation, [], params);
        })
    }
}

function handleBulkOperation(result) {
    if(typeof(result) == 'String') {
        $('#container').append(result);
    } else if(result.statusCode == 0) {
        messageAlert({
            'message': result.message,
            'type': 'success'
        });
    } else {
        var message = result.message;
        messageAlert({
            'message': message,
            'type': 'error'
        })
    }
    $('#myTable .checker span').removeClass('checked');
    $('input[name="all"][type="checkbox"]').prop('checked', false);
    oTable.ajax.reload(); //1.10之后
}

function bulkExport(self) {
    loadingShow();
    var checkbox = $('tbody input[type="checkbox"]');
    var ids = [];
    checkbox.each(function () {
        var $this = $(this);
        if($this.parent().hasClass('checked')) {
            var id = getVal($this);
            ids.push(id);
        }
    });
    if(ids.length == 0) {
        bootstrapQ.alert(trans_admin.enterData);
        loadingHide();
    } else {
        bootstrapQ.confirm({
            'id': 'myExportModal',
            'msg': '确定批量导出选中的真实产品的二维码？'
        }, function () {
            loadingShow();
            downloadQR(ids, false);
        })
        loadingHide();
    }
}
function createAgainRealProduct(self) {
    loadingShow();
    var checkbox = $('tbody input[type="checkbox"]');
    var ids = [];
    checkbox.each(function () {
        var $this = $(this);
        if($this.parent().hasClass('checked')) {
            var id = getVal($this);
            ids.push(id);
        }
    });
    if(ids.length == 0) {
        bootstrapQ.alert(trans_admin.enterData);
        loadingHide();
    } else {
        bootstrapQ.confirm({
            'id': 'myCreateAgainModal',
            'msg': '确定批量重新添加生产并导出选中的真实产品的二维码？'
        }, function () {
            loadingShow();
            var params = {};
            params.data = {};
            params.data.ids = ids;
            var path = originPath();
            ajaxData('post', path + 'realProducts/createAgain', function (result, params) {
                if(isOk(result) && !isNull(result.data) && result.data.length > 0) {
                    var data = result.data;
                    data = JSON.stringify(data);
                    window.location.href = path + 'downloadQR?realProductIds=' + data;
                    oTable.ajax.reload();
                } else if(result.statusCode == ERRORS.NOT_FOUND['code']) {
                    bootstrapQ.alert('选中的真实产品中存在不是不合格状态或者已经重新生产的产品，请刷新列表，重新选择');
                } else {
                    var message = result.message;
                    if(isNull(message)) {
                        message = '服务器错误，请重试';
                    }
                    bootstrapQ.alert(message);
                }
                loadingHide();
            }, [], params);
        })
        loadingHide();
    }
}
function downloadQR(ids, isAgain) {
    if(isNull(ids) || ids.length == 0) {
        console.log('ids is null');
        bootstrapQ.alert('服务器错误，请重试');
        loadingHide();
        return false;
    }
    if(isNull(isAgain) || isAgain != true) {
        isAgain = false;
    }
    var path = originPath();
    var data = JSON.stringify(ids);
    ajaxData('get', path + 'downloadCheck?realProductIds=' + data, function (result) {
        if(isOk(result)) {
            window.location.href = path + 'downloadQR?realProductIds=' + data;
            oTable.ajax.reload();
            loadingHide();
        } else if(isAgain == true) {
            downloadQR(ids, false);
        } else {
            if (result.statusCode == ERRORS.INVALID_PARAMS['code']) {
                bootstrapQ.alert('请选择需要导出二维码的真实产品');
            } else if (result.statusCode == ERRORS.NOT_FOUND['code']) {
                bootstrapQ.alert('选中的真实产品中存在不是待生产状态的产品，请刷新列表，重新选择');
            } else {
                var message = result.message;
                if (isNull(message)) {
                    message = '服务器错误，请重试';
                }
                bootstrapQ.alert(message);
            }
            loadingHide();
        }
    }, [], null);
}