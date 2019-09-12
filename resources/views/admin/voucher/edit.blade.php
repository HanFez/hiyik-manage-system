<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/6/8
 * Time: 15:40
 */

$voucher = isset($voucher) ? $voucher : null;
//dd(json_decode(json_encode($voucher)));
?>
@include('admin.voucher.add', ['isModify' => true, 'data' => $voucher])
