<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/9/17
 * Time: 2:32 PM
 */

namespace App\IekModel\Version1_0;


class ConnectWay extends IekModel
{
    protected $table = "tblConnectWays";


    public function pathConnectWay(){
        return $this->hasMany(self::$NAME_SPACE.'\PathConnectWay','connect_way_id',self::ID);
    }

    public function connectWayParams(){
        return $this->hasMany(self::$NAME_SPACE.'\ConnectWayParam','connect_way_id',self::ID);
    }

}