<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/31
 * Time: 15:26
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class Thumb extends IekProductModel
{
    protected $table = 'tblThumbs';

    public function norm(){
        return $this->hasMany(self::$NAME_SPACE.'\ThumbNorm',IekModel::IID,IekModel::ID);
    }
}
?>