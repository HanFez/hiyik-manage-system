<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/28
 * Time: 15:13
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterCore extends IekProductModel
{
    protected $table = 'tblPosterCores';

    public function material(){
        return $this->belongsTo(self::$NAME_SPACE.'\Material',IekModel::MATERIAL_ID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }

    public function shape(){
        return $this->belongsTo(self::$NAME_SPACE.'\Shape',IekModel::SHAPE_ID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }

    public function handle(){
        return $this->belongsTo(self::$NAME_SPACE.'\Handle',IekModel::HANDLE_ID,IekModel::ID);
    }

    public function coreHandle(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterCoreHandleDefine',IekModel::HANDLE_ID,IekModel::HANDLE_ID);
    }

    public function coreContent(){
        return $this->hasMany(self::$NAME_SPACE.'\PosterCoreContent',IekModel::CORE_ID,IekModel::ID);
    }

    public function materialDefine(){
         return $this->belongsTo(self::$NAME_SPACE.'\PosterCoreMaterialDefine',IekModel::MATERIAL_ID,IekModel::MATERIAL_ID);
    }
}
?>