<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/5/15
 * Time: 14:55
 */
namespace App\IekModel\Version1_0;

class IwallWall extends IekModel
{
    protected $table = 'tblIwallWalls';

    public function wall(){
        return $this->belongsTo(self::$NAME_SPACE.'\Wall',IekModel::WALL_ID,IekModel::ID);
    }

    public function wallProduct(){
        return $this->belongsTo(self::$NAME_SPACE.'\WallProduct',IekModel::WALL_ID,IekModel::WALL_ID);
    }
}
?>