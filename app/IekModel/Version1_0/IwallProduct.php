<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/16
 * Time: 14:33
 */
namespace App\IekModel\Version1_0;

class IwallProduct extends IekModel
{
    protected $table = 'tblIwallProducts';

    public function products(){
        return $this->belongsTo(self::$NAME_SPACE.'\Product','product_id','id');
    }
}