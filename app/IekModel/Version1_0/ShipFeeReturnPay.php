<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/21
 * Time: 18:06
 */

namespace App\IekModel\Version1_0;


class ShipFeeReturnPay extends IekModel
{
    protected $table = 'tblShipFeeReturnPay';
    public $primaryKey = 'id';

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person',self::UID,self::ID);
    }
}