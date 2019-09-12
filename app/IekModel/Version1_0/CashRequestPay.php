<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/1/15
 * Time: 11:14
 */
namespace App\IekModel\Version1_0;

class CashRequestPay extends IekModel
{
    protected $table = 'tblCashRequestPay';

    public function cashPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\CashPay',self::ID,'pay_id');
    }
}
?>