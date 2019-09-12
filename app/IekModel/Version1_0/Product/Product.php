<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 14:31
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class Product extends IekProductModel
{
    protected $table = 'tblProducts';

    public function productDefine(){
        return $this->belongsTo(self::$NAME_SPACE.'\ProductDefine',IekModel::PRODUCT_DID,IekModel::ID);
    }

    public function border(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterBorder',IekModel::ID,IekModel::PRODUCT_ID);
    }

    public function core(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterCore',IekModel::ID,IekModel::PRODUCT_ID);
    }

    public function frame(){
        return $this->hasMany(self::$NAME_SPACE.'\PosterFrame',IekModel::PRODUCT_ID,IekModel::ID);
    }

    public function front(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterFront',IekModel::ID,IekModel::PRODUCT_ID);
    }

    public function back(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterBack',IekModel::ID,IekModel::PRODUCT_ID);
    }

    public function backFacade(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterBackFacade',IekModel::ID,IekModel::PRODUCT_ID);
    }

    public function postMaker(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterMaker',IekModel::ID,IekModel::PRODUCT_ID);
    }

    public function show(){
        return $this->belongsTo(self::$NAME_SPACE.'\PosterShow',IekModel::ID,IekModel::PRODUCT_ID);
    }

    public function productThumb(){
        return $this->belongsTo(self::$NAME_SPACE.'\ProductThumb',IekModel::ID,IekModel::PRODUCT_ID);
    }

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonProduct',IekModel::ID,IekModel::PRODUCT_ID);
    }

}
?>