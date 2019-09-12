<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/14
 * Time: 10:37
 */
namespace App\IekModel\Version1_0;

class IwallTag extends IekModel
{
    protected $table = 'tblIwallTags';

    public function iwall(){
        return $this->belongsTo(self::$NAME_SPACE.'\Iwall','iwall_id','id');
    }

    public function iwallPerson(){
        return $this->belongsTo(self::$NAME_SPACE.'\IwallPerson','iwall_id','iwall_id');
    }

    public function tags(){
        return $this->belongsTo(self::$NAME_SPACE.'\Tag','tag_id','id');
    }
}