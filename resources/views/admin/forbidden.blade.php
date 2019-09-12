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
        $('.btn[data="{{$result->targetId}}"][type="{{ $result->type }}"]').text('取消禁止').attr('data-type','unForbidden');
        var type = '{{$result->type}}';
        var data = '';
        if(type !== 'publication' && type !== 'person'){
            switch (type) {
                case 'description':
                case 'i-description':
                    data = 'seeTitle/';
                    break;
                case 'image':
                case 'i-image':
                    data = 'seeImage/';
                    break;
                case 'tag':
                case 'i-tag':
                    data = 'seeTag/';
                    break;
                case 'comment':
                    data = 'seeComment/';
                    break;
                case 'order-comment-text':
                    data = 'seeCommentText/';
                    break;
                case 'order-comment-image':
                    data = 'seeCommentImage/';
                    break;
                case 'message':
                    data = 'seeMessage/';
                    break;
                case 'nick':
                    data = 'seeNick/';
                    break;
                case 'avatar':
                    data = 'seeAvatar/';
                    break;
                case 'signature':
                    data = 'seeSignature/';
                    break;
                default:
                    break;
            }
            var seeReason = $('<a href="javascript:void(0);" class="seeReason" data="'+data+'{{$result->targetId}}" >查看原因</a>');
            var forbiddenButton = $('.btn[data="{{$result->targetId}}"][type="{{ $result->type }}"]');
            if(!forbiddenButton.parent().hasClass('group-right')) {
                seeReason.addClass('group-left');
            }
            forbiddenButton.after(seeReason);
        }
        $('.seeReason').on('click',function(){
            var url = $(this).attr('data');
            bootstrapQ.dialog({
                type: 'get',
                url: url,
                title: '被禁原因',
                //foot:false
            });
        });
    </script>
@endif
