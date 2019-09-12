<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/8
 * Time: 17:46
 */

namespace App\IekModel\Version1_0;


class Mount extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblMounts';

    public static function checkExist($name){
        $count = self::where('name',$name)
            ->where(IekModel::CONDITION)
            ->count();
        return $count>0 ? true : false ;
    }
}