<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/5/16
 * Time: 3:11 PM
 */

$message = isset($message) ? $message : '';
$message = preg_replace('/\n|\r\n/', '', $message);

$title = isset($title) ? $title : '';
$title = preg_replace('/\n|\r\n/', '', $title);
?>
<script>
    /** Jquery gritter message alert.
     * params:
         position: center-center / top-left / top-right / bottom-left / bottom-right
         speed: 1000
         time: 3000
         title: ''
         message: cannot be null
         image: the image path
         type: '' / light / success / error
         sticky: false / true (Automatically disappear)
     * @type
    */
    $.gritter.options = {
        position: '{{ $position or "center-center" }}',
        fade_out_speed: '{{ $speed or 1000 }}',
        time: '{{ $time or 3000 }}'
    };
    $.gritter.add({
        title:	'{{ $title or '' }}',
        text:	'{{ $message or '' }}',
        image: 	'{{ $image or '' }}',
        class_name: '{{ $type or '' }}',
        sticky: '{{ $sticky or false }}'
    });
</script>
{{--@if(isset($type) && $type == 'success')--}}
    {{--<script>--}}
        {{--/*setTimeout(function() {--}}
            {{--$('.form-horizontal input, .form-horizontal textarea').val('');--}}
        {{--}, 1000);*/--}}
    {{--</script>--}}
{{--@endif--}}