<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2016/10/19
 * Time: 18:05
 */
namespace App\IekModel\Version1_0;

class Crowd extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblCrowds';

    public static function checkExist($name){
        $count = self::where(IekModel::CONDITION)
            ->where('name',$name)
            ->count();
        return $count>0 ? true : false;
    }
}