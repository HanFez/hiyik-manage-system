<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 10/31/16
 * Time: 2:43 PM
 */
?>
@if($result->statusCode == 10014)
    @include('message.messageAlert',['type'=>'error','message'=>$result->message])
@elseif($result->statusCode == 10008)
    @include('message.messageAlert',['type'=>'error','message'=>'未登录'])
    <script>
        alert('请登录');
    </script>
@elseif($result->statusCode == -1)
    @include('message.messageAlert',['type'=>'error','message'=>'未知错误'])
@elseif($result->statusCode == 0 || $result->statusCode == 6)
    <script>
        messageAlert({
            'message': '操作成功',
            'type': 'success'
        });
        $('.btn[data="{{$result->targetId}}"][type="{{ $result->type }}"]').text('禁止').attr('data-type','forbidden');
        $('.btn[data="{{$result->targetId}}"][type="{{ $result->type }}"]').next('a').remove();
    </script>
@endif
