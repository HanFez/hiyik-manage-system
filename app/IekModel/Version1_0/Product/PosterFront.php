<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/28
 * Time: 15:23
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterFront extends IekProductModel
{
    protected $table = 'tblPosterFronts';

    public function material(){
        return $this->belongsTo(self::$NAME_SPACE.'\Material',IekModel::MATERIAL_ID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }

    public function shape(){
        return $this->belongsTo(self::$NAME_SPACE.'\Shape',IekModel::SHAPE_ID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }

    public function materialDefine(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterFrontMaterialDefine',IekModel::MATERIAL_ID,IekModel::MATERIAL_ID);
    }
}
?>