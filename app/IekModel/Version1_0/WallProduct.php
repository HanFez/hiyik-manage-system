<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/5/15
 * Time: 15:49
 */
namespace App\IekModel\Version1_0;

class WallProduct extends IekModel
{
    protected $table = 'tblWallProducts';

    public function product(){
        return $this->belongsTo('App\IekModel\Version1_0\Product\Product',IekModel::PRODUCT_ID,IekModel::ID);
    }
}
?>