<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/9/16
 * Time: 11:18 AM
 */


$icon = 'icon-pencil';
$action = 'edit';
?>
@include('layout/modify')
<script>
    $('#container a[type="submit"]').unbind('click').on('click', function () {
        var url = $(this).attr('url');
        var form = $('#container form');
        removeInputMessage(form);
        var data = getFormValue(form);
        if(data != false) {
            var params = {};
            params.data = data;
            ajaxData('put', url, parseAddEmployeeCallback, [], params);
        }
    })
</script>
