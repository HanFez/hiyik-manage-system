<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/17
 * Time: 19:07
 */
?>
@if($result->statusCode == 30001)
    @include('message.notice',['status'=>'danger','message'=>'无效的 Iwall ID'])
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
        $('#iwall-official').text('推荐').attr('data-type','official');
    </script>
@endif
