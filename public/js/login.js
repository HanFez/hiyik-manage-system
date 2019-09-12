/**
 * Created by xj on 10/27/16 9:43 AM.
 */

function parseGetViewLoginCallback(result) {
    $('#login-box').append(result);
    matrixLogin();
    $('#login-submit').on('click', submitLogin);
    $('#recover-submit').on('click', submitRecover);
    $('#login-form input').on('keypress', function (event) {
        event = eventUtil.getEvent(event);
        var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
        if (keyCode == 13) {
            eventUtil.preventDefault(event);
            submitLogin();
        }
    })
}
function submitLogin() {
    var form = $('#login-form');
    removeInputMessage(form);
    var data = getFormValue(form);
    // console.log(data)
    if(data != false) {
        var params = {};
        params.data = data;
        ajaxData('post', 'admin/login', parsePostLoginCallback, [], params);
    }
}
function parsePostLoginCallback(result) {
    var form = $('#login-form');
    removeInputMessage(form);
    form.append(result);
}
function submitRecover() {

}