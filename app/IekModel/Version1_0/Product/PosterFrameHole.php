<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/31
 * Time: 15:15
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterFrameHole extends IekProductModel
{
    protected $table = 'tblPosterFrameHoles';

    public function shape(){
        return $this->belongsTo(self::$NAME_SPACE.'\Shape',IekModel::SHAPE_ID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }

    public function holeLine(){
        return $this->hasMany(self::$NAME_SPACE.'\PosterFrameHoleLine',IekModel::ID,IekModel::HOLE_ID);
    }
}
?>