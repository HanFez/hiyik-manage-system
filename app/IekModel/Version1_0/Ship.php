<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/22
 * Time: 18:53
 */
namespace App\IekModel\Version1_0;

class Ship extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblShips';

    public function shipProvider(){
        return $this->belongsTo(self::$NAME_SPACE.'\ShipProvider',self::PROVIDER_ID,self::ID);
    }

    public function shipMessage(){
        return $this->belongsTo(self::$NAME_SPACE.'\ShipMessage',self::NO,self::SHIP_NO);
    }

    public function orderShip(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderShip',self::SHIP_ID,self::ID);
    }

    public function company(){
        return $this->belongsTo(self::$NAME_SPACE.'\Company','provider_id','id');
    }
}