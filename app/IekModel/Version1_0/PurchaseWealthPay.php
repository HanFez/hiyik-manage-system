<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/21
 * Time: 17:31
 */

namespace App\IekModel\Version1_0;


class PurchaseWealthPay extends IekModel
{
    protected $table = 'tblPurchaseWealthPay';
    public $primaryKey = 'id';

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','person_id','id');
    }
}