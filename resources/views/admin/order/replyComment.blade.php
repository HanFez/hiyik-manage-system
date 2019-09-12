<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/13
 * Time: 17:00
 */
?>
<div class="control-group">
    <div class="control-label">
        <label>回复内容：</label>
    </div>
    <div class="controls">
        <textarea name="text" id="text" rows="3" cols="10"></textarea>
    </div>
</div>
<div class="control-group">
    <div class="control-label">
        <label>回复图片：</label>
    </div>
    <div class="controls">
        <input type="file" name="image" onchange="replyImg(this)">
    </div>
    <img src="" id="reply-img" width="200" height="200">
</div>
<script>
    function replyImg(source) {
        var file = source.files[0];
        if (window.FileReader) {
            var fr = new FileReader();
            var reply_img = document.getElementById('reply-img');
            fr.onloadend = function (e) {
                reply_img.src = e.target.result;
            };
            fr.readAsDataURL(file);
        }
        var formData = new FormData();
        formData.append('image',file);
    }
</script>