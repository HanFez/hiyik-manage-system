/**
 * Created by xj on 11/17/16 3:48 PM.
 */
function getSystemActiveUrl(systemType, type, target, id, index) {
    if(!isNull(systemType) && !isNull(type) && !isNull(target)) {
        if(systemType == 'settings' || systemType == 'tags') {
            var url = systemType;
            if (systemType == 'settings') {
                if (target == 'child') {
                    url += '/child';
                }
                if (type == 'add' || type == 'modify') {
                    url += '/save';
                }
            } else if (systemType == 'tags') {
                if (type == 'add') {
                    url += '/create';
                }
                if(type != 'add') {
                 url += '/' + type;
                 }
            }

            if(systemType == 'settings' && (type == 'delete' || type == 'recover' || type == 'setDefault')) {
                url += '/'+type + '/' + id;
                if(!isNull(index)) {
                    url += '/' + index;
                }
            }
            return url;
        } else {
            return null;
        }
    } else {
        return null;
    }
}
function bindEventToButtonInSystemSetting(self, systemType) {
    var $this = $(self);
    if(!isUndefined($this.attr('data-type'))) {
        var type = $this.attr('data-type');
        var table = null;
        var index = null;
        var target = 'parent';
        var id = $this.attr('data');
        var parentId = null;
        if(type == 'add') {
            table = $this.prev();
            target = 'child';
        } else {
            table = $this.parent().parent().parent().parent();
            if(!isNull($this.attr('data-index'))) {
                index = parseInt($this.attr('data-index'));
                target = 'child';
            }
        }
        if(target == 'child' && systemType == 'setting') {
            if(!isNull($this.attr('data-id'))) {
                id = parseInt($this.attr('data-id'));
            }
        }else if(systemType == 'tags' && target == 'child'){
            id = $this.attr('data-id');
        }
        if(systemType != 'settings') {
            parentId = id;
        }
        var url = getSystemActiveUrl(systemType, type, target, id, index);
        if((isNull(table) || !table.hasClass('table')) && systemType != 'settings' && type == 'add') {
            table = $this.parent().find('table').eq(0);
        }
        if(!isNull(table) && table.hasClass('table') && !isNull(url)) {
            var columns = getTableColumns(table, ['is_official', 'is_default', 'is_active', 'is_removed', 'created_at', 'updated_at']);
            if(type == 'modify' || type == 'add') {
                //modify parent or child, add child
                addSettingContentOrEditSetting(systemType, table, type, target, parentId, id, index, columns);
            } else if(type == 'delete') {
                //delete
                deleteSetting(url, systemType, id);
            } else if(type == 'recover') {
                //recover
                recoverSetting(url, systemType, id);
            } else if(type == 'setDefault') {
                //set default
                setDefaultSettingContent(url, systemType);
            }
        }
    }
}
function addSettingContentOrEditSetting(systemType, table, type, target, parentId, id, index, columns) {
    if (type == 'modify') {
        var tr = table.find('tbody tr').eq(index);
        for (var i in columns) {
            var column = columns[i];
            column.value = tr.find('td').eq(column.inx).attr('data-value').trim();
        }
    } else if(type == 'add') {
        var trs = table.find('tbody tr');
        var tr = trs.eq(trs.length - 1);
        for (var i in columns) {
            var column = columns[i];
            if(column.name == 'id') {
                if(systemType == 'settings') {
                    var value = tr.find('td').eq(column.inx).attr('data-value').trim();
                    if (!isNull(value)) {
                        value = parseInt(value) + 1;
                        column.value = value;
                    }
                } else if(!isNull(parentId)) {
                    columns[i].name = 'parent_id';
                    columns[i].transName = trans_table.parentId;
                    columns[i].value = parentId;
                }
            }
            if(systemType != 'settings') {
                if(column.name == 'id' || (column.name == 'parent_id' && isNull(column.value))) {
                    columns.splice(i, 1);
                }
            }
        }
    }
    var url = getSystemActiveUrl(systemType, type, target);
    if(!isNull(url)) {
        var params = {};
        params.data = {};
        params.data.id = id;
        if (!isNull(index)) {
            params.data.index = index;
        }
        params.columns = columns;
        params.target = target;
        params.url = url;
        params.systemType = systemType;
        params.type = type;
        if (type == 'add' && target == 'parent') {
            dialogAddSystemSetting(params);
        } else {
            dialogEditForm(params);
        }
    }
}
function deleteSetting(url, systemType, id) {
    var params = {};
    params.data = {};
    if(systemType == 'tags') {
        params.data.id = id;
    }
    params.systemType = systemType;
    bootstrapQ.confirm({
        'id': 'deleteConfirm',
        'msg': '确定要删除此记录？'
    }, function () {
        ajaxData('delete', url, reloadSystemSetting, [], params);
    })
}
function recoverSetting(url, systemType, id) {
    var params = {};
    params.data = {};
    if(systemType == 'tags') {
        params.data.id = id;
    }
    params.systemType = systemType;
    bootstrapQ.confirm({
        'id': 'deleteConfirm',
        'msg': '确定要恢复此记录？'
    }, function () {
        ajaxData('post', url, reloadSystemSetting, [], params);
    })
}
function setDefaultSettingContent(url, systemType) {
    var params = {};
    params.data = {};
    /*if(systemType == 'tags') {
        params.data.id = id;
    }*/
    params.systemType = systemType;
    bootstrapQ.confirm({
        'id': 'deleteConfirm',
        'msg': '确定要设置此记录为默认值吗？'
    }, function () {
        ajaxData('post', url, reloadSystemSetting, [], params);
    })
}
function dialogAddSystemSetting(params) {
    //console.log(params)
    if(isNull(params) || isUndefined(params.columns)) {
        return false;
    }
    if(isUndefined(params.data)) {
        params.data = null;
    }
    var form = createFormBox(params.columns, ['is_forbidden']);
    bootstrapQ.confirm({
        'id': 'myEdit',
        'msg': form,
        'className': 'modal-lg'
    }, postAddSystemSetting, '', dialogAddSystemSettingCallback, params);
}
function dialogAddSystemSettingCallback(params) {
    if(params.systemType == 'tags' && params.target == 'parent') {
        $('#myEdit input[name="level"]').val(0).attr('disabled', 'disabled');
    }
    if(params.systemType == 'tags'){
        $('#myEdit input[name="is_forbidden"]').parents('.control-group').empty();
        $('#myEdit input[name="hits"]').val(0);
    }
    if(params.systemType == 'settings') {
        var content = /*'<form class="form-horizontal" id="add-field">' +*/
            '<div class="control-group">' +
            '<label class="control-label">第一条内容:</label>' +
            '<div class="controls">' +
            '<a class="btn btn-success" id="add-content">添加第一条内容的字段和值</a>' +
            '</div>' +
            '</div>' +

            '<div class="control-group">' +
            '<label class="control-label">内容是否有ID:</label>' +
            '<div class="controls">' +
            '<label><input type="radio" name="fieldId" value="true" style="opacity: 0" checked="checked" />有</label>' +
            '<label><input type="radio" name="fieldId" value="false" style="opacity: 0" />无</label>' +
            '</div>'/* +
         '</form>'*/;
        var form = $('#myEdit form');
        form.append(content);
        dialogFormShownCallback();
        $('#add-content').on('click', function () {
            var controlGroup = $('#myEdit form .control-group');
            controlGroup.removeClass('error warning info success');
            $('.help-inline', controlGroup).remove();
            var fieldForm = createAddFieldForm();
            $('#myEdit form').append(fieldForm);
            $('#myEdit form input').eq(-2).focus();
            $('#myEdit form a[name="delete"]').on('click', function () {
                var $this = $(this);
                var controlGroup = $this.parent().parent();
                controlGroup.remove();
            })
        })
    }
}
function postAddSystemSetting(params) {
    var request = {};
    request.data = {};
    var values = getFormValue($('#myEdit form'));
    var controlGroups = $('#myEdit form .control-group');
    var hasContent = false;
    if(values != false) {
        controlGroups.removeClass('error warning info success');
        $('.help-inline', controlGroups).remove();
        request.data = values;
        request.data.content = [];
        request.data.content[0] = {};
        if(values.fieldId == true) {
            request.data.content[0].id = 1;
        }
        delete values.id;
        delete values.fieldId;
        delete values.fieldName;
        delete values.fieldValue;
        var fields = $('#myEdit input[name="fieldName"]');
        fields.each(function () {
            var $this = $(this);
            var fieldValue = $this.parent().parent().find('input[name="fieldValue"]');
            var fieldName = getVal($this);
            hasContent = true;
            request.data.content[0][fieldName] = getVal(fieldValue);
        })
    }
    request.systemType = params.systemType;
    if(request.systemType == 'tags'){
        delete request.data.content;
    }
    if(!isUndefined(params.url) && values != false) {
        if(params.systemType == 'settings' && hasContent == false) {
            messageAlert({
                'message': '请添加第一条内容的字段名和值',
                'type': 'error'
            });
            return false;
        }
        ajaxData('post', params.url, reloadSystemSetting, [], request);
    }
    return false;
}
function createAddFieldForm() {
    var form = '<div class="control-group">' +
        '<label class="control-label">' +
        '<a class="btn btn-danger" name="delete" title="Delete">X</a>' +
        '<input type="text" placeholder="字段名" name="fieldName" required="required" value="" />:</label>' +
        '<div class="controls">' +
        '<input type="text" placeholder="字段值" name="fieldValue" checked="checked" required="required" value="" />' +
        '</div>';
    return form;
}
function reloadSystemSetting(result, params) {
    if(!isNull(result)) {
        $('body').append(result);
        $('#myEdit').modal('hide');
        if(!isNull(params.systemType)) {
            $('#system-' + params.systemType).trigger('click');
        } else {
            console.log('not has system type');
        }
    }
}
function dialogEditForm(params) {
    if(isNull(params) || isUndefined(params.columns)) {
        return false;
    }
    if(isUndefined(params.data)) {
        params.data = null;
    }
    var form = createFormBox(params.columns, ['is_forbidden']);
    bootstrapQ.confirm({
        'id': 'myEdit',
        'msg': form
    }, dialogFormOkButtonCallback, '', dialogFormCallback, params);
}
function dialogFormOkButtonCallback(params) {
    //ok
    var request = {};
    request.data = {};
    var values = getFormValue($('#myEdit form'));
    var controlGroups = $('#myEdit form .control-group');
    if(values != false) {
        controlGroups.removeClass('error warning info success');
        $('.help-inline', controlGroups).remove();
        if(params.systemType == 'settings') {
            if (!isNull(params.data)) {
                request.data = params.data;
            }
            request.data.content = values;
            if (!isUndefined(params.target) && params.target == 'child') {
                request.data.description = request.data.content.parentDescription;
                delete request.data.content.parentDescription;
            } else {
                request.data.description = request.data.content.description;
                delete request.data.content.description;
                delete request.data.content.id;
                request.data.name = request.data.content.name;
                delete request.data.content.name;
                delete request.data.content;
            }
            if (!isNull(request.data.content) && !isUndefined(request.data.content.id)) {
                request.data.content.id = parseInt(request.data.content.id);
            }
        } else {
            request.data = values;
        }
    }
    if(!isUndefined(params.url) && values != false) {
        request.systemType = params.systemType;
        ajaxData('post', params.url, reloadSystemSetting, [], request);
    }
    return false;
}
function dialogFormCallback(params) {
    //callback
    /*if(!isUndefined(params.target) && params.target == 'child' && params.systemType == 'settings') {
        $('#myEdit form').append('<div class="control-group"><label class="control-label">修改原因:</label><div class="controls"><input placeholder="修改原因" name="parentDescription" required="required" value="" type="text"></div></div>');
    }*/
    if(params.systemType == 'tags' && params.target == 'child') {
        $('#myEdit input[name="level"]').val(1).attr('disabled', 'disabled');
        $('#myEdit input[name="is_forbidden"]').parents('.control-group').empty();
        $('#myEdit input[name="hits"]').val(0);
        if(params.type == 'modify') {
            $('#myEdit input[name="parent_id"]').removeAttr('disabled');
            $('#myEdit input[name="is_forbidden"]').parents('.control-group').empty();
        }
    }
    dialogFormShownCallback();
}
function dialogFormShownCallback() {
    $('#myEdit').on('shown', function(){
        $('#myEdit input[type="checkbox"],#myEdit input[type="radio"]').uniform();
        if($('#myEdit input[type="text"]').eq(0).attr('name') != 'id' ) {
            $('#myEdit input[type="text"]').eq(0).focus();
        } else {
            $('#myEdit input[type="text"]').eq(1).focus();
        }
    })
}