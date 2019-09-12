<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/20
 * Time: 14:42
 */
namespace App\IekModel\Version1_0;

class OrderPay extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblOrderPay';

    public function pay(){
        return $this->belongsTo(self::$NAME_SPACE.'\ViewPurchasePay',self::PAY_ID,self::PAY_ID);
    }

    public function thirdPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\PurchaseThirdPay',self::PAY_ID,self::ID);
    }

    public function wealthPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\PurchaseWealthPay',self::PAY_ID,self::ID);
    }

    public function orderReturnPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderReturnPay',self::ORDER_ID,self::ORDER_ID);
    }
}