<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/28
 * Time: 15:10
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterBorder extends IekProductModel
{
    protected $table = 'tblPosterBorders';

    public function shape(){
        return $this->belongsTo(self::$NAME_SPACE.'\Shape',IekModel::SHAPE_ID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }

    public function line(){
        return $this->hasMany(self::$NAME_SPACE.'\PosterLine',IekModel::BORDER_ID,IekModel::ID);
    }

    public function material(){
        return $this->belongsTo(self::$NAME_SPACE.'\Material',IekModel::MATERIAL_ID,IekModel::ID);
    }

    public function materialDefine(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterBorderMaterialDefine',IekModel::MATERIAL_ID,IekModel::MATERIAL_ID);
    }
}
?>