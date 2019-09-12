<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/5/8
 * Time: 10:00
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterBackFacade extends IekProductModel
{
    protected $table = 'tblPosterBackFacades';

    public function materialDefine(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterBackFacadeMaterialDefine',IekModel::MATERIAL_ID,IekModel::MATERIAL_ID);
    }

    public function material(){
        return $this->belongsTo(self::$NAME_SPACE.'\Material',IekModel::MATERIAL_ID,IekModel::ID);
    }
}
?>