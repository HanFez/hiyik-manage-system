<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/27
 * Time: 14:53
 */
$action = isset($action) ? $action : null;
$facade = isset($facade) ? $facade : null;
$path = \App\IekModel\Version1_0\Constants\Path::FILE_PATH;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改外观图"}}@else{{"添加外观图"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-facade">
            <div class="control-group">
                <label class="control-label">上传图片：</label>
                <div class="controls">
                    <input type="file" id="facade" onchange="showPreview(this)" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">图片内容：</label>
                <div class="controls" style="width: 200px;height: 200px;">
                    <img src="{{$action == 'edit'?$path.$facade->uri:''}}" id="facade-id" data-id="{{$action == 'edit'?$facade->id:''}}">
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-facade');
    function showPreview(source) {
        var file = source.files[0];
        if(window.FileReader) {
            var fr = new FileReader();
            var facade_id = document.getElementById('facade-id');
            fr.onloadend = function(e) {
                facade_id.src = e.target.result;
            };
            fr.readAsDataURL(file);
            //texture_img.style.display = 'block';
        }
        var formData = new FormData();
        formData.append('facade',file);
        $.ajax({
            url: "new_pro/uploadFacade",//传向后台服务器文件
            type: 'post',    //传递方法
            data: formData,  //传递的数据
            dataType : 'json',  //传递数据的格式
            async:false, //这是重要的一步，防止重复提交的
            cache: false,  //设置为false，上传文件不需要缓存。
            contentType: false,//设置为false,因为是构造的FormData对象,所以这里设置为false。
            processData: false,//设置为false,因为data值是FormData对象，不需要对数据做处理。
            success: function (responseStr) {
                if(!isNull(responseStr)){
                    if(responseStr.statusCode == 0 ){
                        //form.find('img').attr('data-id', responseStr.data.id);
                        messageAlert({
                            message : responseStr.message,
                            type : 'success'
                        });
                    }else{
                        messageAlert({
                            message : responseStr.message,
                            type : 'error'
                        });
                    }
                }
            },
            error: function () {
                messageAlert({
                    message : "上传出错",
                    type : 'error'
                });
            }
        });
    }
</script>