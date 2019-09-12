/**
 * Created by Administrator on 2017/3/2.
 */
/**
 * Upload image files ajax function.
 * @param data
 * @param url
 * @param handleDataCallback
 * @param params
 * @param errorHandleCallback
 */
function ajaxImageData(data, url, handleDataCallback, params, errorHandleCallback) {
    // var postData = filePostData(md5, file, fileName);
    $.ajax({
        url: url,
        type:'POST',
        data: data,
        cache: false,
        contentType: false,    //不可缺
        processData: false,    //不可缺,
        success: function(data) {
            if(handleDataCallback) {
                handleDataCallback(data, params);
            }
        },
        error: function () {
            if(errorHandleCallback) {
                errorHandleCallback(params);
            }
        }
    });
}
/**
 * upload file post data
 * @param md5
 * @param file
 * @param fileName
 * @returns {*}
 */
function filePostData(file, fileName) {
    var formData = new FormData();//构造空对象，用append 方法赋值.
    formData.append("_token", $('#token').val());
    if(file) {
        if (fileName) {
            formData.append("fileName", file, fileName);
        } else {
            formData.append("fileName", file);
        }
    }
    return formData;
}