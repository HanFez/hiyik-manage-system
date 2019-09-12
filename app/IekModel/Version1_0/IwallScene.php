<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/16
 * Time: 14:48
 */
namespace App\IekModel\Version1_0;

class IwallScene extends IekModel
{
    protected $table = 'tblIwallScenes';
    public $primaryKey = 'id';

    public function scene(){
        return $this->belongsTo(self::$NAME_SPACE.'\Scene','scene_id','id');
    }
}