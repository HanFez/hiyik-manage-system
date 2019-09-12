<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/21
 * Time: 17:39
 */

namespace App\IekModel\Version1_0;


class OrderReturnWealthPay extends IekModel
{
    protected $table = 'tblOrderReturnWealthPay';
    public $primaryKey = 'id';

    public function fromPerson(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','person_id','id');
    }
}