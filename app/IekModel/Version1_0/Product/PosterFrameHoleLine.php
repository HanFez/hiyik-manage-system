<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/31
 * Time: 15:16
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterFrameHoleLine extends IekProductModel
{
    protected $table = 'tblPosterFrameHoleLines';

    public function material(){
        return $this->belongsTo(self::$NAME_SPACE.'\Material',IekModel::MATERIAL_ID,IekModel::ID);
    }
}
?>