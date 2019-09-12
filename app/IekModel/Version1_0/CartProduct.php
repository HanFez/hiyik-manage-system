<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/20
 * Time: 11:12
 */
namespace App\IekModel\Version1_0;

class CartProduct extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblCartProducts';

    public function products(){
        return $this->belongsTo('App\IekModel\Version1_0\Product\Product',self::PRODUCT_ID,self::ID)
            ->where(IekModel::CONDITION);
    }

    public function orderProduct(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderProduct',self::ID,self::CART_PID);
    }

    public function cart(){
        return $this->belongsTo(self::$NAME_SPACE.'\Cart',self::CART_ID,self::ID);
    }
}