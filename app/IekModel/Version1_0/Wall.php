<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/5/15
 * Time: 15:22
 */
namespace App\IekModel\Version1_0;

class Wall extends IekModel
{
    protected $table = 'tblWalls';

    public function wallProduct(){
        return $this->hasMany(self::$NAME_SPACE.'\WallProduct',IekModel::WALL_ID,IekModel::ID);
    }
}
?>