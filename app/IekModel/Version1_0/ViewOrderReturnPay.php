<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 9:24
 */

namespace App\IekModel\Version1_0;


class ViewOrderReturnPay extends IekModel
{
    protected $table = 'viewOrderReturnPay';

    public function orderReturnThirdPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderReturnThirdPay',self::PAY_ID,self::ID);
    }

    public function orderReturnWealthPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderReturnWealthPay',self::PAY_ID,self::ID);
    }

    public function fromPerson(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person',self::UID,self::ID);
    }
}