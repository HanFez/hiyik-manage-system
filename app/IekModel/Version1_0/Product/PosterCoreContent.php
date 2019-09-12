<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/31
 * Time: 16:26
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterCoreContent extends IekProductModel
{
    protected $table = 'tblPosterCoreContents';

    public function content(){
        return $this->belongsTo(self::$NAME_SPACE.'\CoreContent',IekModel::CONTENT_ID,IekModel::ID);
    }
}
?>