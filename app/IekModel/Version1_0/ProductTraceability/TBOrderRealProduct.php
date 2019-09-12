<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 3/6/18
 * Time: 5:49 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


class TBOrderRealProduct extends IekProductTraceabilityModel
{
    protected $table="tblTBOrderRealProducts";

    public function order() {
        return $this->hasOne(self::$NAME_SPACE.'\TBOrder', 'order_no', 'order_no')
            ->where(self::CONDITION);
    }

    public function realProduct() {
        return $this->hasOne(self::$NAME_SPACE.'\RealProduct', 'user_no', 'real_product_no')
            ->where(self::CONDITION);
    }

}