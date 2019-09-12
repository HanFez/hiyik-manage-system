<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/20
 * Time: 14:36
 */
namespace App\IekModel\Version1_0;

class OrderShip extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblOrderShips';

    public function ship(){
        return $this->belongsTo(self::$NAME_SPACE.'\Ship','ship_id','id');
    }
}