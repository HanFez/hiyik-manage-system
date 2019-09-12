<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/16
 * Time: 14:59
 */
namespace App\IekModel\Version1_0;

class IwallSex extends IekModel
{
    protected $table = 'tblIwallSexes';

    public function sex(){
        return $this->belongsTo(self::$NAME_SPACE.'\Sex','sex_id','id');
    }
}