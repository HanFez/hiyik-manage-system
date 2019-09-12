<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/14
 * Time: 11:47
 */
namespace App\IekModel\Version1_0;

class IwallCover extends IekModel
{
    protected $table = 'tblIwallCovers';

    public function iwall(){
        return $this->belongsTo(self::$NAME_SPACE.'\Iwall','iwall_id','id');
    }

    public function cover(){
        return $this->belongsTo(self::$NAME_SPACE.'\Images','image_id','id');
    }

    public function iwallPerson(){
        return $this->belongsTo(self::$NAME_SPACE.'\IwallPerson','iwall_id','iwall_id');
    }
}