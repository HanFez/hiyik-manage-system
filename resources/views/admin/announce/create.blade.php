<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/22/16
 * Time: 3:42 PM
 */
?>
@if($result->statusCode == 10014)
    @include('message.messageAlert',['type'=>'error','message'=>$result->message])
@elseif($result->statusCode == 10008)
    @include('message.messageAlert',['type'=>'error','message'=>'未登录'])
    <script>
        bootstrapQ.alert('请登录');
    </script>
@elseif($result->statusCode == -1)
    @include('message.messageAlert',['type'=>'error','message'=>'未知错误'])
@elseif($result->statusCode == 0)
    <script>
        messageAlert({
            'message': '操作成功',
            'type': 'success'
        });
        $('#announceForm input').val('');
        CKEDITOR.instances.editor.setData('');
    </script>
@endif
