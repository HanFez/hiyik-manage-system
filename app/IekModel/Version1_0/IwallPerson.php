<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/6/1
 * Time: 19:50
 */
namespace App\IekModel\Version1_0;

class IwallPerson extends IekModel
{
    protected $table = 'tblIwallPersons';
    public $primaryKey = 'id';

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','person_id','id');
    }
}