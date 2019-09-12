<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:19 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


class ProductIntroduction extends IekProductTraceabilityModel
{
    protected $table="tblProductIntroductions";

    public function introduction() {
        return $this->hasOne(self::$NAME_SPACE.'\Introduction', self::ID, 'introduction_id')
            ->where(self::CONDITION);
    }

    public static function createRecord($params){
        $introduction = new self();
        $introduction->introduction_id = $params['introductionId'];
        $introduction->product_id = $params['productId'];
        $introduction->save();
        return $introduction;
    }

}