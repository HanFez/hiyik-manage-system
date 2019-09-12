/**
 * Created by xj on 12/29/17 5:34 PM.
 */

function showUnqualifiedResult() {
    var detail = getUnqualifiedResult();
    var html = '';
    if(!isNull(detail)) {
        for(var i in detail) {
            if(!isNull(detail[i]) && detail[i].length != 0) {
                var title = transStr(i, trans_table);
                html += '<div><span class="label label-important" >'+ title +'</span><br>';
                var values = detail[i];
                for(var j in values) {
                    html += '<span style="margin-left: 10px">' + values[j] + '</span><br>';
                }
                html += '</div>';
                // for(var j in detail[i]) {
                //     html += detail[i][j] + '<br>';
                //     // html += '<span class="label label-inverse">' + detail[i][j] + '</span><br>';
                // }
            }
        }
    }
    if(isNull(html)) {
        html = '无';
    }
    var $result = $('#unqualifiedResult');
    $result.find('.span11').html(html);
}

function getUnqualifiedResult() {
    var $unqualifiedPart = $('#unqualifiedPart');
    var $parts = $unqualifiedPart.find('.collapse');
    var detail = {};
    $parts.each(function () {
        var $this = $(this);
        var type = $this.attr('name');
        detail[type] = [];
        $this.find('input:checkbox:checked').each(function () {
            var val = $(this).val();
            if(!isNull(val)) {
                detail[type].push(val.trim());
            }
        })
        $this.find('input:text').each(function () {
            var val = $(this).val();
            if(!isNull(val)) {
                detail[type].push(val.trim());
            }
        })
        if(isNull(detail[type]) || detail[type].length == 0) {
            detail[type] = null;
        }
    });
    $unqualifiedPart.find('.other input:text').each(function () {
        var type = 'other';
        detail[type] = [];
        var val = $(this).val();
        if(!isNull(val)) {
            detail[type].push(val.trim());
        }
        if(isNull(detail[type]) || detail[type].length == 0) {
            detail[type] = null;
        }
    });
    if(isNull(detail) || detail.length == 0) {
        return null;
    } else {
        var flag = true;
        for(var i in detail) {
            if(!isNull(detail[i]) && detail[i].length > 0) {
                flag = false;
            }
        }
        if(flag == true) {
            return null;
        } else {
            return detail;
        }
    }
}

function submitCheckResult(event,btn) {
    eventUtil.preventDefault(event);
    loadingShow();
    var status = $('#check input:radio:checked').val();
    var detail = null;
    if(status == 'fail'){
        detail = getUnqualifiedResult();
        if(isNull(detail)) {
            bootstrapQ.alert('请选择或输入不合格原因');
            loadingHide();
            return false;
        }
    }
    var checker = $('#checker').val();
    if(checker.length == 0){
        bootstrapQ.alert('请输入质检员编号');
        loadingHide();
        return false;
    }
    var params = {};
    var data = {};
    data.status = status;
    data.detail = detail;
    data.checker = checker;
    params.data = data;
    ajaxData('post',originPath()+'check/'+btn.attr('data'),function(data){
        if(data.statusCode == 0){
            bootstrapQ.alert('质检完成！',function () {
                location.reload();
            });
        }else {
            bootstrapQ.alert('质检失败，请刷新重试。连续失败请联系技术人员');
        }
        loadingHide();
    },'',params);
}

function addOrderMsg(data, hiyikOriginUri) {
    if(!isNull(data)){
        var table = $('<table class="table table-hover table-bordered table-info"></table>');
        var orderNoTr = $('<tr></tr>');
        var orderNoTd = $('<td>订单号</td>');
        var orderNoDataTd = $('<td>'+data.order_no+'</td>');
        orderNoTr.append(orderNoTd,orderNoDataTd);
        var receiveNameTr = $('<tr></tr>');
        var receiveNameTd = $('<td>收货姓名</td>');
        var receiveNameDataTd = $('<td>'+data.receive_name+'</td>');
        receiveNameTr.append(receiveNameTd,receiveNameDataTd);
        var receivePhoneTr = $('<tr></tr>');
        var receivePhoneTd = $('<td>收货手机</td>');
        var receivePhoneDataTd = $('<td>'+data.receive_phone+'</td>');
        receivePhoneTr.append(receivePhoneTd,receivePhoneDataTd);
        var receiveCallTr = $('<tr></tr>');
        var receiveCallTd = $('<td>收货电话</td>');
        var receiveCallDataTd = $('<td>'+data.receive_call+'</td>');
        receiveCallTr.append(receiveCallTd,receiveCallDataTd);
        var receiveAddressTr = $('<tr></tr>');
        var receiveAddressTd = $('<td>收货地址</td>');
        var receiveAddressDataTd = $('<td>'+data.receive_address+'</td>');
        receiveAddressTr.append(receiveAddressTd,receiveAddressDataTd);

        var shipTr = $('<tr></tr>');
        var shipTd = $('<td>快递信息</td>');
        var shipDataTd = $('<td></td>');
        var shipTable = $('<table class="table table-hover table-bordered table-info"></table>');
        var shipTableHead = $('<thead><tr><th>快递公司</th><th>快递单号</th><th>真实产品编号</th></tr></thead>');
        shipTable.append(shipTableHead);
        for(var i = 0;i < data.ships.length;i++ ){
            var shipTableTr = $('<tr><td>'+data.ships[i].ship_company+'</td><td>'+data.ships[i].ship_no+'</td><td>'+data.ships[i].real_product+'</td></tr>');
            shipTable.append(shipTableTr);
        }
        shipDataTd.append(shipTable);
        shipTr.append(shipTd,shipDataTd);
        // var shipNoTr = $('<tr></tr>');
        // var shipNoTd = $('<td>快递编号</td>');
        // var shipNoDataTd = $('<td>'+data.ship_no+'</td>');
        // shipNoTr.append(shipNoTd,shipNoDataTd);
        // var shipCompanyTr = $('<tr></tr>');
        // var shipCompanyTd = $('<td>快递公司</td>');
        // var shipCompanyDataTd = $('<td>'+data.ship_company+'</td>');
        // shipCompanyTr.append(shipCompanyTd,shipCompanyDataTd);
        var realProductNoTr = $('<tr></tr>');
        var realProductNoTd = $('<td>所属全部真实产品编号</td>');
        var realProductNoDataTd = $('<td></td>');
        var realProductNoUl = $('<ul style="padding: 0; margin: 0; list-style: none"></ul>');
        for(var i = 0;i < data.orderRealProducts.length;i++ ){
            var realProductNoLi = $('<li style="padding: 0; margin: 0; list-style: none"><a href="'+ hiyikOriginUri + data.orderRealProducts[i].realProduct.no+'" target="_blank">'+data.orderRealProducts[i].real_product_no+'</a></li>');
            realProductNoUl.append(realProductNoLi);
        }
        realProductNoDataTd.append(realProductNoUl);
        realProductNoTr.append(realProductNoTd,realProductNoDataTd);
        var realProductNumTr = $('<tr></tr>');
        var realProductNumTd = $('<td>订单产品数量</td>');
        var realProductNumDataTd = $('<td>'+data.orderRealProducts.length+'</td>');
        realProductNumTr.append(realProductNumTd,realProductNumDataTd);

        var memo = data.memo;
        var $memo = $('<tr></tr>').append('<td>订单备注</td>').append('<td>' + memo + '</td>');

        table.append(orderNoTr,receiveNameTr,receiveAddressTr,receivePhoneTr,receiveCallTr,realProductNoTr,realProductNumTr, $memo, shipTr);
        $('#collapseOrder .widget-content').html(table);
    }
}