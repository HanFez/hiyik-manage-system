<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/19
 * Time: 11:27
 */
namespace App\IekModel\Version1_0;

class ViewPurchasePay extends IekModel
{
    protected $table = 'viewPurchasePay';

    public function purchaseThirdPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\PurchaseThirdPay',self::PAY_ID,self::ID);
    }

    public function purchaseWealthPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\PurchaseWealthPay',self::PAY_ID,self::ID);
    }

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person',self::UID,self::ID);
    }
}