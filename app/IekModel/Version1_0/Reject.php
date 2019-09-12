<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/22
 * Time: 19:59
 */
namespace App\IekModel\Version1_0;

class Reject extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblRejects';

    public function backShip(){
        return $this->belongsTo(self::$NAME_SPACE.'\Ship',self::BACK_SHIP_ID,self::ID);
    }

    public function rejectResultHandle(){
        return $this->belongsTo(self::$NAME_SPACE.'\RejectResultHandle',self::ID,self::REJECT_ID);
    }

    public function rejectShipFeePay(){
        return $this->belongsTo(self::$NAME_SPACE.'\RejectShipFeePay',self::ID,self::REJECT_ID);
    }
}