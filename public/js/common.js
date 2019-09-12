/**
 * Created by xj on 10/26/16 2:20 PM.
 */

function messageAlert(option){
    if(!isUndefined(option) && option.message != '') {
        if(isUndefined(option.title)) {
            option.title = '';
        }
        if(isUndefined(option.image)) {
            option.image = '';
        }
        if(isUndefined(option.type)) {
            option.type = '';
        }
        if(isUndefined(option.sticky)) {
            option.sticky = false;
        }
        $.gritter.add({
            title: option.title,
            text: option.message,
            image: option.image,
            class_name: option.type,
            sticky: option.sticky
        });
    }
}

function convertDateTime(time, type, format) {
    if(isNull(time) || isNull(type)) {
        return null;
    } else {
        type = type.toLowerCase();
    }
    if(typeof(format) == 'undefined') {
        format = 'yyyy-MM-dd hh:mm:ss';
    }
    var date = new Date(Date.parse(time.replace(/-/g, "/")));
    var gmtHours = date.getTimezoneOffset() / 60;
    var dateSeconds = Date.parse(date);
    var gmtSeconds = gmtHours * 60 * 60 * 1000;
    var milliSeconds = null;
    if(type == 'local') {
        milliSeconds = dateSeconds - gmtSeconds;
    } else if(type == 'utc' || type == 'gmt') {
        milliSeconds = dateSeconds + gmtSeconds;
    }
    if(isNull(milliSeconds)) {
        return time;
    } else {
        date.setTime(milliSeconds);
        if(date == 'local') {
            date = checkDay(date, format);
        } else {
            date = date.format(format);
        }
        return date;
    }
}

function checkDay(myDate, format){
    var oneDay = 24 * 60 * 60 * 1000;
    var today = new Date(),
        todayTime = today.getTime() % oneDay,     //获取从今天0点开始到现在的时间
        offset = myDate.getTime() - today.getTime(),    //获取要判断的日期和现在时间的偏差
        dateTime = offset + todayTime;    //获取要判断日期距离今天0点有多久
    var minutes = 10 * 60 * 1000;
    var myDateTime = myDate.format('hh:mm:ss');
    if(offset <= 0 && offset >= -minutes) {
        return '刚刚';
    } else if(dateTime >= 0 && dateTime < oneDay){
        return '今天 ' + myDateTime;
    } else if(dateTime >= -oneDay && dateTime < 0) {
        return '昨天 ' + myDateTime;
    } else{
        return myDate.format(format);
    }
}

function formatDate(time, format) {
    if(isNull(time)) {
        return null;
    }
    if(isNull(format)) {
        format = 'yyyy-MM-dd hh:mm:ss';
    }
    var date = new Date(Date.parse(time.replace(/-/g, "/")));
    date = date.format(format);
    return date;
}

/**
 * Compare time beginTime < endTime.
 * @param time1: yyyy-mm-dd
 * @param time2: yyyy-mm-dd
 * @returns {boolean}
 */
function compareDate(time1, time2) {
    if(isNull(time1) || isNull(time2)) {
        return false;
    }
    var date1 = new Date(time1.replace(/-/g,"\/"));
    var date2 = new Date(time2.replace(/-/g,"\/"));
    if(date1 > date2) {
        return false;
    } else {
        return true;
    }
}

/**
 * Judge time > now.
 * @param time
 * @returns {boolean}
 */
function judgeTime(time) {
    if(isNull(time)) {
        return false;
    }
    var date = new Date(time.replace(/-/g,"\/"));
    var now = new Date();
    if(now > date) {
        return false;
    } else {
        return true;
    }
}
/**
 * Create form control-group html.
 * @param name
 * @param filed
 * @returns {string}
 */
function createControlGroup(name, filed, content) {
    var html = '<div class="control-group">' +
        '<label class="control-label"><span class="text-important">*&nbsp;</span>'+ name +'：</label>' +
        '<div class="controls" name="'+ filed +'">';
    if(isNull(content)) {
        if(filed == 'currency'){
            html += '<input type="text" required="required" value="CNY">';
        }else{
            html += '<input type="text" required="required">';
        }
    } else {
        html += content;
    }
    html += '</div>' +
        '</div>';
    return html;
}
/**
 * Show form control-group html.
 * @param name
 * @param filed
 * @returns {string}
 */
function showControlGroup(name, filed, content) {
    if(filed == 'created_at' || filed == 'updated_at') {
        if(!isNull(content)) {
            content = '<span class="time-utc">'+ content +'</span>';
        }
    }
    if(isNull(content)) {
        content = '空';
    }
    var html = '<div class="control-group">' +
        '<label class="control-label" style="padding-top: 10px;">'+ name +'：</label>' +
        '<div class="controls" name="'+ filed +'">' +
        content;
    html += '</div>' +
        '</div>';
    return html;
}

/**
 * Take snake_case string to camelCase.
 * @param str
 * @returns {*}
 */
function snakeToCamelStr(str) {
    var re = /_(\w)/g;
    var isSnake = re.test(str);
    if(isSnake) {
        // console.log('str: '+ str);
        str = str.replace(re, function ($0, $1) {
            // console.log('$0: '+$0);
            // console.log('$1: ' +$1)
            return $1.toUpperCase();
        });
    }
    return str;
}
/**
 * Get controls-box form data.
 * @param controls
 * @returns {*}
 */
function getControlsData(controls) {
    var obj = {};
    var flag = true;
    var num = 0;
    if(!isNull(controls)) {
        controls.each(function () {
            var name = $(this).attr('name');
            if (!isNull(name)) {
                var val = null;
                if ($(this).find('>input').length > 0) {
                    val = $(this).find('>input').val();
                } else if ($(this).find('>select').length > 0) {
                    val = $(this).find('>select').select2('val');
                }
                obj[name] = val;
                if (name == 'price' && !isNumber(val)) {
                    setInputMessage($(this).find('input'), 'error', '价格中不能有字母');
                    flag = false;
                }
                num++;
            }
        })
    }
    if(num == 0) {
        obj = null;
    }
    if(flag) {
        return obj;
    } else {
        return false;
    }
}
function getFormValue(form) {
    var input = $('input', form);
    var data = {};
    var flag = true;
    input.each(function () {
        var $this = $(this);
        var type = $this.attr('type');
        var name = $this.attr('name');
        var val = getVal($this);
        if(isMeetFormat($this) == false) {
            flag = false;
        } else if(!isNull(type) && !isNull(name)) {
            if(isNull(val)) {
                val = null;
            }
            if (type == 'date') {
                if(type == 'date' && !isNull(val) && val.indexOf(':') > -1) {
                    data[name] = convertDateTime(val, 'utc');
                } else {
                    data[name] = val;
                }
            } else if (type == 'radio') {
                if ($this.prop('checked')) {
                    if(val == 'true') {
                        data[name] = true;
                    } else if(val == 'false') {
                        data[name] = false;
                    } else {
                        data[name] = val;
                    }
                } else if (isUndefined(data[name])) {
                    data[name] = '';
                }
            } else {
                data[name] = val;
            }
        }
    });

    var textarea = $('textarea', form);
    textarea.each(function () {
        var $this = $(this);
        var name = $this.attr('name');
        if(!isNull(name)) {
            var val = getVal($this);
            if(isMeetFormat($this) == false) {
                flag = false;
            } else {
                data[name] = val;
            }
        }
    });


    if(!flag) {
        return false;
    } else {
        return data;
    }
}
function isNumber(val) {
    val = parseFloat(val);
    if(isNaN(val)) {
        return false;
    } else {
        return true;
    }
}
function isMeetFormat(input) {
    var flag = true;
    if(!isUndefined(input) && input.length > 0) {
        var isRequired = input.attr('required') == 'required' ? true : false;
        var value = getVal(input);
        var name = '';
        var type = input.attr('type');
        var dataType = input.attr('data-type');
        var regex = '';
        if(!isUndefined(input.attr('name'))) {
            name = input.attr('name');
        }
        if(name == 'phone') {
            regex = /^0?1[3|4|5|8][0-9]\d{8}$/;
        } else if(name == 'mail') {
            regex = /^\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}$/;
        } else if(name == 'identifier') {
            regex = /^\d{17}[\d|x|X]$/;
        }
        if(isNull(value)) {
            if(isRequired && type != 'file') {
                setInputMessage(input, 'error', trans_error.notEmpty);
                flag = false;
            }
        } else if(!isNull(regex)) {
            var test = regex.test(value);
            if(test) {
                flag = true;
            }else {
                setInputMessage(input, 'error', trans_error.invalidFormat);
                flag = false;
            }
        } else if(name == 'price') {
            if(!isNumber(value)) {
                setInputMessage(input, 'error', '价格中不能有字母');
                flag = false;
            }
        } else if(type == 'number') {
            var min = Number(input.attr('min'));
            var max = Number(input.attr('max'));
            if(!isNaN(min) && value < min) {
                setInputMessage(input, 'error', '数值必须大于 ' + min);
                flag = false;
            }
            if(!isNaN(max) && value > max) {
                setInputMessage(input, 'error', '数值必须小于 ' + max);
                flag = false;
            }
        }
        if(isRequired) {
            if (dataType == 'number') {
                if (!isNumber(value)) {
                    setInputMessage(input, 'error', '请填写正确的数字类型，不能含字母或特殊字符');
                    flag = false;
                }
            } else if (dataType == 'int') {
                if (!isNumber(value)) {
                    setInputMessage(input, 'error', '请填写正确的整型，不能含小数，不能含字母或特殊字符');
                    flag = false;
                }
            }
        }
    }
    if(flag == false) {
        if(!input.hasClass('datepicker')) {
            input.focus();
        }
        return false;
    } else {
        return true;
    }
}

function getSelectVal(select) {
    if(!isUndefined(select) && select.length > 0) {
        if(!isUndefined($('option:selected', select))) {
            return $('option:selected', select).val().trim();
        }
    }
    return '';
}

/**
 * Show input help message.
 * @param input
 * @param type: 'error' | 'warning' | 'info' | 'success'
 * @param message
 */
function setInputMessage(input, type, message) {
    if(!isUndefined(input) && input.length > 0) {
        var controlGroup = input.parent().parent();
        if(controlGroup.hasClass('control-group')) {
            if(typeof(type) != 'undefined' && typeof(message) != 'undefined') {
                controlGroup.removeClass('error warning info success');
                $('.help-inline', controlGroup).remove();
                input.after('<span generated="true" class="help-inline">'+ message +'</span>');
                controlGroup.addClass(type);
            }
        }
        /*input.unbind('click').on('click', function () {
         controlGroup.removeClass('error warning info success');
         $('.help-inline', controlGroup).remove();
         })*/
    }
}

function removeInputMessage(form) {
    $('.help-inline', form).remove();
    $('.control-group', form).removeClass('error warning info success');
}

function getVal(input) {
    if(!isNull(input) && input.length > 0) {
        var val = input.val();
        if(!isNull(val)) {
            val = val.trim();
        } else {
            return '';
        }
        if(input.attr('data-type') == 'number') {
            val = Number(val);
        } else if(input.attr('data-type') == 'int') {
            if(parseInt(val) != val) {
                return 'NAN';
            } else {
                val = parseInt(val);
            }
        }
        return val;
    } else {
        return '';
    }
}

function isEmpty(input) {
    if(!isNull(input) && input.length > 0) {
        var value = getVal(input);
        if (value == '') {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function isMax(input, maxNum) {
    if(!isNull(input) && input.length > 0 && !isNull(maxNum)) {
        var value = getVal(input);
        if (value.length > maxNum) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function isUndefined(param) {
    if(typeof(param) == 'undefined') {
        return true;
    } else {
        return false;
    }
}

function isNull(param) {
    if(isUndefined(param) || param === '' || param === null) {
        return true;
    } else {
        return false;
    }
}

/**
 *
 * @param propertyName
 * @returns {Function}
 * example
 var data = [{
            name: "jiang",
            age: 25
        }, {
            name: "AAAAAAAAAAAAAA",
            age: 28
        }, {
            name: "CCCCCCCCc",
            id: 22,
            age: 22
        }];
 //使用方法
 data.sort(compare("age"));
 // console.log(data);
 */
function compare(propertyName) {
    return function (object1, object2) {
        var val1 = object1[propertyName];
        var val2 = object2[propertyName];
        if (!isNaN(Number(val1)) && !isNaN(Number(val2))) {
            val1 = Number(val1);
            val2 = Number(val2);
        }
        if (val1 < val2) {
            return -1;
        } else if (val1 > val2) {
            return 1;
        } else {
            return 0;
        }
    }
}

function createDialogLargeBox(content, className, id) {
    if(isNull(className)) {
        className = '';
    }
    if(isNull(id)) {
        id = '';
    }
    var dialog = '<div id="'+ id +'" class="dialog-lg">' +
        '<div class="dialog-body ' + className + '">' +
        '<a class="remove"><i class="icon-remove"></i></a>' +
        content +
        '</div>' +
        '</div>';
    $('body').append(dialog);
    var selector = '';
    if(id != '') {
        selector = '#' + id;
    } else {
        initPageElement(id);
        selector = '.dialog-lg';
    }
    $(selector).slideDown("slow", function () {
        $('body').css('overflow', 'hidden');
    });
    $(selector+ ', .dialog-lg .remove').on('click', function () {
        $('body').css('overflow', 'auto');
        $(selector).slideUp("slow", function () {
            $(selector).remove();
        })
    });
    $(selector+ ' .close[data-dismiss="alert"]').unbind('click').on('click', function () {
        $(this).parent().remove();
    });
    $('.dialog-body').on('click', function (event) {
        event.stopPropagation(event);
    })
}

function getTableColumns(table, removeColumns) {
    if(!isNull(table) && table.length > 0) {
        var ths = table.find('thead th');
        var columns = [];
        if(isUndefined(removeColumns)) {
            removeColumns = null;
        }
        ths.each(function (inx) {
            var thText =$(this).attr('data-value').trim();
            var thTextTrans =$(this).text().trim();
            var flag = true;
            for(var i in removeColumns) {
                if(thText == removeColumns[i].toLowerCase()) {
                    flag = false;
                    break;
                }
            }
            if(flag == true && inx != ths.length - 1) {
                columns.push({
                    name: thText,
                    transName: thTextTrans,
                    inx: inx,
                    type: 'text'
                });
            }
        })
        var tr = table.find('tbody tr').eq(0);
        for(var i in columns) {
            var column = columns[i];
            var value = tr.find('td').eq(column.inx).attr('data-value').trim();
            if(value == 'true' || value == 'false') {
                column.type = 'boole';
            }
        }
        return columns;
    }
}

function createFormBox(params, hiddenArray) {
    if(isNull(params)) {
        return null;
    }
    var form = '<form class="form-horizontal">';
    for(var i in params) {
        var obj = params[i];
        if(isUndefined(obj.name)) {
            return null;
        }
        if(!isNull(hiddenArray)) {
            var flag = false;
            for(var k in hiddenArray) {
                if(obj.name == hiddenArray[k]) {
                    flag = true;
                }
            }
            if(flag) {
                continue;
            }
        }
        if(isUndefined(obj.transName)) {
            obj.transName = obj.name;
        }
        if(isUndefined(obj.value)) {
            obj.value = '';
        }
        form += '<div class="control-group">' +
            '<label class="control-label">' + obj.transName + ':' +
            '</label>' +
            '<div class="controls">';
        if(obj.type == 'boole') {
            if(obj.value == '') {
                obj.value = 'true';
            }
            var labels = createRadioBox(obj.name, ['true', 'false'], obj.value);
            if(!isNull(labels)) {
                form += labels;
            } else {
                return null;
            }
        } else if(obj.name == 'id' || obj.name == 'parent_id') {
            form += '<input type="text" placeholder="' + obj.transName + '" name="' + obj.name + '" value="' + obj.value + '" disabled="disabled" >';
        } else {
            form += '<input type="text" placeholder="' + obj.transName + '" name="' + obj.name + '" required="required" value="' + obj.value + '" >';
        }
        form += '</div>' +
            '</div>';
    }
    form += '</form>';
    return form;
}

function createRadioBox(name, values, checkedValue, isInline) {
    if(!isNull(name) && !isNull(values)) {
        var labels = '';
        for(var i in values) {
            var value = values[i];
            var isChecked = false;
            if(!isNull(checkedValue) && value == checkedValue) {
                isChecked = true;
            }
            var label = createRadioLabel(name, value, isChecked, isInline);
            if(isNull(label)) {
                return null;
            } else {
                labels += label;
            }
        }
        return labels;
    } else {
        return null;
    }
}
function createRadioLabel(name, value, isChecked, isInline) {
    var className = '';
    if(isNull(isInline) || isInline == true) {
        className = 'label-inline';
    }
    if(!isNull(name) && !isNull(value)) {
        var radio = '<label class="'+ className +'">';
        if(!isNull(isChecked) && isChecked == true) {
            radio += '<input type="radio" name="'+ name +'" style="opacity: 0;" value="'+ value +'" checked="checked">';
        } else {
            radio += '<input type="radio" name="'+ name +'" style="opacity: 0;" value="'+ value +'">';
        }
        radio += value + '</label>';
        return radio;
    } else {
        return null;
    }
}

function ajaxData(method, url, parseDataCallBack, handleDataCallBack, params, errorCallback) {
    var data;
    if (method != 'get' && method != 'GET') {
        if (typeof(params) == 'undefined') {
            params = {};
            params.data = {};
        }
        if(typeof(params.data) == 'undefined') {
            params.data = {};
        }
        data = params.data;
        var token = $('#token').val();
        data._token = token;
    }
    data = $.toJSON(data);
    var ajax = $.ajax({
        type: method,
        url: url,
        data: data,
        headers: {'Content-type': 'application/json'},
        success: function (result) {
            var result = parseDataCallBack(result, params);
            for (var inx in handleDataCallBack) {
                if(typeof(handleDataCallBack[inx]) != 'undefined') {
                    handleDataCallBack[inx](result, params);
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if(errorCallback) {
                errorCallback(params);
            } else {
                ajaxData('get', 'errors/' + jqXHR.status, function (result) {
                    $('#container').append(result);
                }, [], null, function () {

                });
                /*if(jqXHR.status == 404 || jqXHR.status ==400 || jqXHR.status == 406 || jqXHR.status == 410 || jqXHR.status == 402 || jqXHR.status ==403 || jqXHR.status == 500){
                }*/
            }
        }
    });
    return ajax;
}

function getWindowSize() {
    var win = {};
    win.width = getWindowWidth();
    win.height = getWindowHeight();
    return win;
}
function getWindowWidth() {
    var winWidth = 0;
    //获取窗口宽度
    if (window.innerWidth)
        winWidth = window.innerWidth;
    else if ((document.body) && (document.body.clientWidth))
        winWidth = document.body.clientWidth;
    //通过深入Document内部对body进行检测，获取窗口大小
    if (document.documentElement && document.documentElement.clientWidth) {
        winWidth = document.documentElement.clientWidth;
    }
    return winWidth;
}
function getWindowHeight() {
    var winHeight = 0;
    //获取窗口高度
    if (window.innerHeight)
        winHeight = window.innerHeight;
    else if ((document.body) && (document.body.clientHeight))
        winHeight = document.body.clientHeight;
    //通过深入Document内部对body进行检测，获取窗口大小
    if (document.documentElement && document.documentElement.clientHeight) {
        winHeight = document.documentElement.clientHeight;
    }
    return winHeight;
}
function getScrollBarWidth() {
    var oP = document.createElement('p'),
        styles = {
            width: '100px',
            height: '100px',
            overflowY: 'scroll'
        }, i, scrollBarWidth;
    for (i in styles) oP.style[i] = styles[i];
    document.body.appendChild(oP);
    scrollBarWidth = oP.offsetWidth - oP.clientWidth;
    oP.remove();
    return scrollBarWidth;
}
/**
 *
 * @param urls
 * @param size: 1024_1024 | 512_512 | 400_300 | 256_256 | 128_128
 * @returns {*}
 */
function getOneImageNormInNorms(urls, size) {
    if(typeof (urls) == 'undefined' || urls == null  || urls.length == 0){
        return null;
    }
    var flag = true;
    for(var i in urls) {
        var obj = urls[i];
        if(!isNull(size)) {
            if(!isNull(obj.name)) {
                if (obj.name == size) {
                    return obj;
                }
            } else if(!isNull(obj.uri)) {
                if(obj.uri.indexOf('/' + size +'/') >= 0) {
                    return obj;
                }
            }
        }
        flag = false;
        continue;
    }
    if(flag) {
        return null;
    } else {
        return urls[0];
    }
}

function loadingShow() {
    $('#loading-box').show();
}

function loadingHide() {
    $('#loading-box').hide();
}

function saveSuccess() {
    messageAlert({
        message: '保存成功',
        type: 'success'
    })
}

function saveError() {
    messageAlert({
        message: '保存失败',
        type: 'error'
    })
    loadingHide();
}

function formNotCompleteNotice() {
    messageAlert({
        message: '请填写完所有的必填项',
        type: 'error'
    })
    loadingHide();
}

function transStr(str, transFile) {
    var lang = str;
    if(typeof(transFile) != 'undefined' && !isNull(transFile)) {
        lang = transFile[snakeToCamelStr(str)];
        if(isNull(lang)) {
            lang = str;
        }
    }
    if(str == 'productIntroductionCount') {
        lang = '产品引用此介绍的数量';
    }
    return lang;
}

function transValue(key, value) {
    if(value === true) {
        value = '是';
    } else if(value === false) {
        value = '否';
    }
    if(key == 'status') {
        if(value == 'waitProduct') {
            value = '<span class="label">待生产</span>';
        } else if(value == 'producing') {
            value = '<span class="label label-info">生产中</span>';
        } else if(value == 'pass') {
            value = '<span class="label label-success">合格</span>';
        } else if(value == 'fail') {
            value = '<span class="label label-important">不合格</span>';
        }
    } else if(key == 'type') {
        if(value == 'author') {
            value = '作者';
        } else if(value == 'publication') {
            value = '作品';
        } else if(value == 'scene') {
            value = '场景';
        } else if(value == 'craft') {
            value = '工艺';
        }
    }
    return value;
}

/**
 *
 * @returns {string}
 */
function filePath(){
    var path = '/rest/1_0/';
    return path;
}

function originPath() {
    var path = '/tb/';
    return path;
}