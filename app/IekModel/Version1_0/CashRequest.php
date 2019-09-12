<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/1/10
 * Time: 16:17
 */
namespace App\IekModel\Version1_0;

class CashRequest extends IekModel
{
    protected $table = 'tblCashRequests';

    public function thirdAccount(){
        return $this->belongsTo(self::$NAME_SPACE.'\ThirdPayAccount',self::THIRD_ACCOUNT_ID,self::ID);
    }

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person',self::UID,self::ID);
    }

    public function cashRequestPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\CashRequestPay',self::ID,self::REQUEST_ID);
    }

    public function cashAudit(){
        return $this->belongsTo(self::$NAME_SPACE.'\CashAudit',self::ID,self::CASH_REQUEST_ID);
    }
}
?>