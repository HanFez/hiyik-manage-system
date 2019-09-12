<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/9/17
 * Time: 2:35 PM
 */

namespace App\IekModel\Version1_0;


class ConnectWayParam extends IekModel
{
    protected $table="tblConnectWayParams";

    public function connectWay(){
        return $this->hasMany(self::$NAME_SPACE.'\ConnectWay',self::ID,'connect_way_id');
    }

    public function params(){
        return $this->hasMany(self::$NAME_SPACE.'\Param',self::ID,'param_id');
    }

}