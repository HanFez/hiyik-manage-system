<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/31
 * Time: 16:22
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterLine extends IekProductModel
{
    protected $table = 'tblPosterLines';

    public function material(){
        return $this->belongsTo(self::$NAME_SPACE.'\Material',IekModel::MATERIAL_ID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }
}
?>