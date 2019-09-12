<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/18
 * Time: 14:16
 */

?>
<div class="span4">
    <div class="control-group">
        <div class="control-label"><label>产品状态：</label></div>
        <div class="controls">
            @foreach($status as $st)
                @if($st->name == 'waitSend')
                    <input type="radio" name="status" value="{{$st->name}}" class="span1" checked onchange="write_reason()">合格
                @endif
                @if($st->name == 'waitProduct')
                    <input type="radio" name="status" value="{{$st->name}}" class="span1" onchange="write_reason()">不合格
                @endif
            @endforeach
        </div>
    </div>
    <div class="control-group">
        <div class="control-label"><label>状态理由：</label></div>
        <div class="controls">
            <textarea name="reason" id="reason" class="span11">验收合格</textarea>
        </div>
    </div>
</div>
<script>
    function write_reason(){
        var click = $('input[type="radio"]:checked');
        var text = click.val();
        if(text=='waitSend'){
            $('#reason').val('验收合格');
        }else{
            $('#reason').val('');
        }
    }
</script>