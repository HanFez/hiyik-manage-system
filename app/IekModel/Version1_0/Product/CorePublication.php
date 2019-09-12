<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/5/6
 * Time: 16:55
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class CorePublication extends IekProductModel
{
    protected $table = 'tblCorePublications';

    public function title(){
        return $this->belongsTo('App\IekModel\Version1_0\PublicationTitle',IekModel::PID,IekModel::PID);
    }

    public function pubImg(){
        return $this->belongsTo('App\IekModel\Version1_0\PublicationImage',IekModel::PID,IekModel::PID);
    }
}
?>