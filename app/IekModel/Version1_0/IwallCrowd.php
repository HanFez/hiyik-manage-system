<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/16
 * Time: 14:55
 */
namespace App\IekModel\Version1_0;

class IwallCrowd extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblIwallCrowds';

    public function crowd(){
        return $this->belongsTo(self::$NAME_SPACE.'\Crowd','crowd_id','id');
    }
}