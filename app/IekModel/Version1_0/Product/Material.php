<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 12:01
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class Material extends IekProductModel
{
    protected $table = 'tblMaterials';

    public function texture(){
        return $this->belongsTo(self::$NAME_SPACE.'\MaterialTexture',IekModel::ID,IekModel::MATERIAL_ID)
            ->where(IekModel::CONDITION);
    }

    public function section(){
        return $this->belongsTo(self::$NAME_SPACE.'\MaterialSection',IekModel::ID,IekModel::MATERIAL_ID)
            ->where(IekModel::CONDITION);
    }
}
?>