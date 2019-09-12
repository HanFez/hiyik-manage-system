<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 12:02
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class MaterialSection extends IekProductModel
{
    protected $table = 'tblMaterialSections';

    public function material(){
        return $this->belongsTo(self::$NAME_SPACE.'\Material',IekModel::MATERIAL_ID,IekModel::ID);
    }
}
?>