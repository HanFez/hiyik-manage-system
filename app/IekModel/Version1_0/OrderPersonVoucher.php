<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/20
 * Time: 10:38
 */
namespace App\IekModel\Version1_0;

class OrderPersonVoucher extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblOrderPersonVouchers';

    public function personVoucher(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonVoucher','person_voucher_id','id');
    }
}