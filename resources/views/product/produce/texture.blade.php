<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 11:51
 */
$path = \App\IekModel\Version1_0\Constants\Path::FILE_PATH;
$action = isset($action)?$action:null;
$texture = isset($texture)?$texture:null;
?>
<div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>@if($action == 'edit'){{"修改纹理图"}}@else{{"添加纹理图"}}@endif</h5>
    </div>
    <div class="widget-content nopadding">
        <form class="form-horizontal" id="form-texture">
            <div class="control-group">
                <label class="control-label">上传图片：</label>
                <div class="controls">
                    <input type="file" id="texture" onchange="showPreview(this)" />
                </div>
            </div>
            @if($action == 'edit')
                <div class="control-group">
                    <label class="control-label">图片名称：</label>
                    <div class="controls">
                        <input type="text" id="fileName" value="{{$texture->file_name}}">
                    </div>
                </div>
            @endif
            <div class="control-group">
                <label class="control-label">图片内容：</label>
                <div class="controls" style="width: 200px;height: 200px;">
                    <img src="{{$action == 'edit'?$path.$texture->uri:''}}" id="texture-img" data-id="{{$action == 'edit'?$texture->id:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">物理宽度(mm)：</label>
                <div class="controls">
                    <input type="number" id="width" value="{{$action == 'edit'?$texture->phy_width:''}}">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">物理高度(mm)：</label>
                <div class="controls">
                    <input type="number" id="height" value="{{$action == 'edit'?$texture->phy_height:''}}">
                </div>
            </div>
            <div class="form-actions">
                <a type="submit" class="btn btn-success" id="save-texture">保存</a>
            </div>
        </form>
    </div>
</div>
<script>
    var form = $('#form-texture');
    function showPreview(source) {
        var file = source.files[0];
        if(window.FileReader) {
            var fr = new FileReader();
            var texture_img = document.getElementById('texture-img');
            fr.onloadend = function(e) {
                texture_img.src = e.target.result;
            };
            fr.readAsDataURL(file);
            //texture_img.style.display = 'block';
        }
        var formData =new FormData();
        formData.append('file',file);
        $.ajax({
            url: "new_pro/uploadTexture",//传向后台服务器文件
            type: 'post',//传递方法
            data: formData,//传递的数据
            dataType : 'json',//传递数据的格式
            async: false, //这是重要的一步，防止重复提交的
            cache: false, //设置为false，上传文件不需要缓存。
            contentType: false,//设置为false,因为是构造的FormData对象,所以这里设置为false。
            processData: false,//设置为false,因为data值是FormData对象，不需要对数据做处理。
            success: function (responseStr) {
                console.log(responseStr);
                if(!isNull(responseStr)){
                    if(responseStr.statusCode == 0 ){
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
                    message : '上传出错',
                    type : 'error'
                });
            }
        });
    }
    $('#save-texture').unbind('click').bind('click',function(){
        var id = $('#texture-img').attr('data-id');
        var width = $('#width').val();
        var height = $('#height').val();
        var files = $('#texture')[0].files[0];
        var formData = new FormData();
        formData.append('files',files);
        formData.append('width',width);
        formData.append('height',height);
        if('{{$action}}' == 'edit'){
            var fileName = $('#fileName').val();
            var params = {};
            params.fileName = fileName;
            params.width = width;
            params.height = height;
            $.ajax({
                url: 'new_pro/updateTexture/'+id,
                type:'put',
                data: params,
                cache: false,
                success: function(data) {
                    if(data.statusCode == 0) {
                        messageAlert({
                            message : data.message,
                            type : 'success'
                        });
                    }else{
                        messageAlert({
                            message : data.message,
                            type : 'error'
                        });
                    }
                },
                error: function () {
                    messageAlert({
                        message : data.message,
                        type : 'error'
                    });
                }
            });
        }else{
            $.ajax({
            url: 'new_pro/createTexture',
            type:'post',
            data: formData,
            cache: false,
            dataType : 'json',//传递数据的格式
            contentType: false,    //不可缺
            processData: false,    //不可缺,
            success: function(data) {
                if(data.statusCode == 0) {
                    messageAlert({
                        message : data.message,
                        type : 'success'
                    });
                }else{
                    messageAlert({
                        message : data.message,
                        type : 'error'
                    });
                }
            },
            error: function () {
                messageAlert({
                    message : data.message,
                    type : 'error'
                });
            }
        });}
    });
</script>