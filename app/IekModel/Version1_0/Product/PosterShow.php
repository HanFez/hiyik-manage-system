<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/28
 * Time: 17:22
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterShow extends IekProductModel
{
    protected $table = 'tblPosterShows';

    public function show(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterShowDefine',IekModel::SHOW_ID,IekModel::ID);
    }

    public function material(){
        return $this->belongsTo(self::$NAME_SPACE.'\Material',IekModel::MATERIAL_ID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }
}
?>