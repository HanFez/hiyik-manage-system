<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/5/16
 * Time: 11:42 AM
 */

/**
 * params:
    result {
        statusCode: cannot be null,
        message: cannot be null,
        data: '' / inputName (If the status code other than 0 and 1, this field must have a value)
    }
 */
?>
@if(isset($result) && isset($result -> statusCode) && isset($result -> message))
    <?php
        $message = $result -> message;
        $message = isset($message) ? $message : '';
        $message = preg_replace('/\n|\r\n/', '', $message);
        $data = $result -> data;
    ?>
    @if(isset($data) && $result -> statusCode != 0)
        <script>
            $(function () {
                var inputName = '{{ $data }}';
                var $input = $('input[name="'+ inputName +'"]');
                if($input.length > 0) {
                    setInputMessage($input, 'error', '{{ $message }}');
                } else {
                    messageAlert({
                        type: 'error',
                        message: '{{ $message  }}',
                        sticky: true
                    })
                }
            })
        </script>
    @elseif($result -> statusCode == 0)

        @include('message.messageAlert',['type' => 'success','message' => $message])
    @else
        @include('message.messageAlert',['type' => 'error','message' => $message, 'sticky' => true])
    @endif
@endif
