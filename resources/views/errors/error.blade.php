<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/29/16
 * Time: 3:50 PM
 */
use App\IekModel\Version1_0\IekModel;
$status = isset($status) ? $status : 'unKnown';
$error = IekModel::strTrans('error', 'message');
$title = $error.' '.IekModel::strTrans($status, 'message');

if($status === 'unKnown') {
    $message = IekModel::strTrans('unKnownError', 'message');
} else {
    switch ($status) {
        case 404:
            $message = IekModel::strTrans('notFound', 'message');
            break;
        default:
            $message = IekModel::strTrans('notAllowed', 'message');
            break;
    }
}
?>

<script>
    $(document).ready(function () {
        var status = '{{ $status }}';
        var message = '{{ $message }}';
        var html = '<div class="error_ex">' +
                '<h1>'+ status +'</h1>' +
                '<h3>'+ message +'</h3>' +
                '</div>';
        bootstrapQ.alert({
            'id': 'myError',
            'className': 'dialog-errors',
            'title': '{{ $title }}',
            'msg': html,
            'foot': false
        })
        loadingHide();
    })
</script>
