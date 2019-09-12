<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/28
 * Time: 17:23
 */
namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;

class PersonProduct extends IekProductModel
{
    protected $table = 'tblPersonProducts';

    public function personNick(){
        return $this->belongsTo('App\IekModel\Version1_0\PersonNick',IekModel::UID,IekModel::UID);
    }
}
?>