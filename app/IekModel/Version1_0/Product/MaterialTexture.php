<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 12:03
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class MaterialTexture extends IekProductModel
{
    protected $table = 'tblMaterialTextures';

    public function material(){
        return $this->belongsTo(self::$NAME_SPACE.'\Material',IekModel::MATERIAL_ID,IekModel::ID);
    }

    public function texture(){
        return $this->belongsTo(self::$NAME_SPACE.'\Texture',IekModel::TEXTURE_ID,IekModel::ID);
    }
}
?>