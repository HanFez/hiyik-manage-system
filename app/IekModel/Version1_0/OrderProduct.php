<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/23
 * Time: 10:43
 */
namespace App\IekModel\Version1_0;

class OrderProduct extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblOrderProducts';

    public function order(){
        return $this->belongsTo(self::$NAME_SPACE.'\Order',self::ORDER_ID,self::ID);
    }

    public function cartProduct(){
        return $this->belongsTo(self::$NAME_SPACE.'\CartProduct',self::CART_PID,self::ID);
    }

    public function orderProductVoucher(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderProductVoucher',self::ID,self::ORDER_PID);
    }

    public function orderComment(){
        return $this->hasOne(self::$NAME_SPACE.'\OrderComment',self::CART_PID,self::CART_PID);
    }

    public function products(){
        return $this->belongsTo('App\IekModel\Version1_0\Product\Product',self::PRODUCT_ID,self::ID)
            ->where(IekModel::CONDITION);
    }
}