<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/25
 * Time: 10:58
 */
namespace App\IekModel\Version1_0;

class OrderProductVoucher extends IekModel
{
    protected  $table = 'tblOrderProductVouchers';

    public function personVoucher(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonVoucher','person_voucher_id','id');
    }
}
?>