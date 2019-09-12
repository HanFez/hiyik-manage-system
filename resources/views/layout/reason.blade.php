<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/22/16
 * Time: 4:14 PM
 */
?>

<div id="form-reason" class="hide">
    <form class="form-horizontal">
        @yield('content')
        <div class="control-group hide">
            <label class="control-label">
                被禁内容 :
            </label>
            <div class="controls">
                <div class="content">

                </div>
            </div>
        </div>
        <div class="control-group hide">
            <label class="control-label">
                内容中的敏感词 :
            </label>
            <div class="controls">
                <input type="text" name="filters" placeholder="输入内容中的敏感词,以逗号隔开" required="required">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">
                原因 :
            </label>
            <div class="controls">
                @foreach($reasons as $reason)
                    @if(!is_null($reason->reason) && $reason->reason != '')
                        <label class="hide">
                            <input type="radio" name="reason-id" style="opacity: 0;" value="{{$reason->id}}" data-type="{{$reason->type}}">
                            {{$reason->reason}}
                        </label>
                    @endif
                @endforeach
                <label>
                    <input type="radio" name="reason-id" style="opacity: 0;" value="other" data-type="">
                    其他
                </label>
                <label class="hide">
                    <textarea name="other"></textarea>
                </label>
            </div>
        </div>
    </form>
</div>
