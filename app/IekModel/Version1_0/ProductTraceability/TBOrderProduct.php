<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 3/6/18
 * Time: 5:49 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


class TBOrderProduct extends IekProductTraceabilityModel
{
    protected $table="tblTBOrderProducts";

    public function order() {
        return $this->hasMany(self::$NAME_SPACE.'\TBOrder', 'order_no', 'order_no')
            ->where(self::CONDITION);
    }

}