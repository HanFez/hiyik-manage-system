<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/12/6
 * Time: 15:03
 */
namespace App\IekModel\Version1_0;

class RejectShipFeePay extends IekModel
{
    protected $table = 'tblRejectShipFeePay';

    public function pay(){
        return $this->belongsTo(self::$NAME_SPACE.'\ShipFeeReturnPay',self::PAY_ID,self::ID);
    }
}
?>