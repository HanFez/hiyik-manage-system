<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/28
 * Time: 17:23
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class ProductThumb extends IekProductModel
{
    protected $table = 'tblProductThumbs';

    public function thumb(){
        return $this->belongsTo(self::$NAME_SPACE.'\Thumb',IekModel::THUMB_ID,IekModel::ID);
    }
}
?>