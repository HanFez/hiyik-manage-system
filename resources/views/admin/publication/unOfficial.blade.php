<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 10/31/16
 * Time: 2:44 PM
 */
?>
@if($result->statusCode == 21001)
    @include('message.notice',['status'=>'danger','message'=>'无效的作品ID'])
@elseif($result->statusCode == 7)
    @include('message.notice',['status'=>'danger','message'=>$result->message])
@elseif($result->statusCode == 10008)
    @include('message.notice',['status'=>'danger','message'=>'未登录'])
    <script>
        alert('请登录');
    </script>
@elseif($result->statusCode == -1)
    @include('message.notice',['status'=>'danger','message'=>'未知错误'])
@elseif($result->statusCode == 0 || $result->statusCode == 6)
    <script>
        messageAlert({
            'message': '操作成功',
            'type': 'success'
        });
        $('#publication-official').text('推荐').attr('data-type','official');
    </script>
@endif