<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 14:17
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterFrameMaterialDefine extends IekProductModel
{
    protected $table = 'tblPosterFrameMaterialDefines';

    public function facade(){
        return $this->belongsTo(self::$NAME_SPACE.'\Facade',IekModel::FACADE_ID,IekModel::ID);
    }
}
?>