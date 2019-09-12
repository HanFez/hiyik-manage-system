<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 4/6/17
 * Time: 3:48 PM
 */

namespace App\IekModel\Version1_0;


class RefundRequest extends IekModel
{
    protected $table = "tblRefundRequests";
    public $primaryKey = 'id';

    public function refundRequestHandle(){
        return $this->belongsTo(self::$NAME_SPACE.'\RefundRequestHandle',self::ID,self::REFUND_REQUEST_ID);
    }

    public function order(){
        return $this->belongsTo(self::$NAME_SPACE.'\Order',self::ORDER_ID,self::ID);
    }

    public function reason(){
        return $this->belongsTo(self::$NAME_SPACE.'\Reason',self::REAID,self::ID);
    }

    public function orderPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderPay',self::ORDER_ID,self::ORDER_ID);
    }

    public function personOrder(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonOrder',self::ORDER_ID,self::ORDER_ID);
    }

    public function orderReturnPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderReturnPay',self::ORDER_ID,self::ORDER_ID)
            ->where(IekModel::CONDITION);
    }
}