<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 14:10
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterBorderMaterialDefine extends IekProductModel
{
    protected $table = 'tblPosterBorderMaterialDefines';

    public function facade(){
        return $this->belongsTo(self::$NAME_SPACE.'\Facade',IekModel::FACADE_ID,IekModel::ID);
    }
}
?>