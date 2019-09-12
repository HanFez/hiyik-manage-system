<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/28
 * Time: 17:17
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PosterMaker extends IekProductModel
{
    protected $table = 'tblPosterMakers';

    public function maker(){
        return $this->belongsTo('App\IekModel\Version1_0\PersonNick',IekModel::UID,IekModel::UID);
    }
}
?>