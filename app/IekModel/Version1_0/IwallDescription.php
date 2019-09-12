<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/14
 * Time: 10:35
 */
namespace App\IekModel\Version1_0;

class IwallDescription extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblIwallDescriptions';

    public function iwall(){
        return $this->belongsTo(self::$NAME_SPACE.'\Iwall','iwall_id','id');
    }

    public function description(){
        return $this->belongsTo(self::$NAME_SPACE.'\PlainStyle','content_id','id');
    }

    public function iwallPerson(){
        return $this->belongsTo(self::$NAME_SPACE.'\IwallPerson','iwall_id','iwall_id');
    }
}