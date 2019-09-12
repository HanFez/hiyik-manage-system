<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/13
 * Time: 9:51
 */
namespace App\IekModel\Version1_0;

class Status extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblStatus';

    public static function addExist($name){
        $res = self::where(self::CONDITION)
            ->where(self::NAME,$name)
            ->count();
        return $res>0 ? true : false;
    }
    public static function updateExist($name,$id){
        $res = self::where(self::CONDITION)
            ->where(self::NAME,$name)
            ->get();
        foreach($res as $re){
            if(!is_null($re) && $re->id != $id){
                return false;
            }else{
                return true;
            }
        }
    }
}