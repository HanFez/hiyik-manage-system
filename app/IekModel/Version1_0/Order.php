<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/22
 * Time: 16:46
 */

namespace App\IekModel\Version1_0;


class Order extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblOrders';

    public function orderReceiveInformation(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderReceiveInformation',self::ID,self::ORDER_ID);
    }

    public function orderShip(){
        return $this->hasOne(self::$NAME_SPACE.'\OrderShip',self::ORDER_ID,self::ID);
    }

    public function orderPay(){
        return $this->hasOne(self::$NAME_SPACE.'\OrderPay',self::ORDER_ID,self::ID);
    }

    public function orderPersonVoucher(){
        return $this->hasMany(self::$NAME_SPACE.'\OrderPersonVoucher',self::ORDER_ID,self::ID);
    }

    public function orderProducts(){
        return $this->hasMany(self::$NAME_SPACE.'\OrderProduct',self::ORDER_ID,self::ID);
    }

    public function personOrder(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonOrder',self::ID,self::ORDER_ID);
    }

    public function orderStatus(){
        return $this->hasMany(self::$NAME_SPACE.'\OrderStatus',self::ORDER_ID,self::ID);
    }

    public function orderComment(){
        return $this->hasMany(self::$NAME_SPACE.'\OrderComment',self::ORDER_ID,self::ID);
    }

    public function score(){
        return $this->hasMany(self::$NAME_SPACE.'\OrderScore',self::ORDER_ID,self::ID);
    }

    public function refundRequest(){
        return $this->hasMany(self::$NAME_SPACE.'\RefundRequest',self::ORDER_ID,self::ID);
    }

    public function platformMemo(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderPlatformMemo',self::ID,self::ORDER_ID);
    }

    public function urge(){
        return $this->belongsTo(self::$NAME_SPACE.'\Urge',IekModel::ID,IekModel::ORDER_ID)->where(IekModel::CONDITION);
    }
}