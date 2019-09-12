<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/5/6
 * Time: 16:51
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class CoreContent extends IekProductModel
{
    protected $table = 'tblCoreContents';

    public function corePublication(){
        return $this->belongsTo(self::$NAME_SPACE.'\CorePublication',IekModel::ID,IekModel::CORE_CID);
    }

    public function image(){
        return $this->belongsTo('App\IekModel\Version1_0\Images',IekModel::IID,IekModel::ID);
    }
}
?>