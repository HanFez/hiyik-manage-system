<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 3/6/18
 * Time: 5:48 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


class TBOrder extends IekProductTraceabilityModel
{
    protected $table="tblTBOrders";

    public function orderProducts() {
        return $this->hasMany(self::$NAME_SPACE.'\TBOrderProduct', 'order_no', 'order_no')
            ->where(self::CONDITION);
    }

    public function orderRealProducts() {
        return $this->hasMany(self::$NAME_SPACE.'\TBOrderRealProduct', 'order_no', 'order_no')
            ->where(self::CONDITION);
    }
    public function ships() {
        return $this->hasMany(self::$NAME_SPACE.'\TBOrderShip', 'order_no', 'order_no')
            ->where(self::CONDITION);
    }

}