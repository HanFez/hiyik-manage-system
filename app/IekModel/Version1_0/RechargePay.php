<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/21
 * Time: 18:06
 */

namespace App\IekModel\Version1_0;


class RechargePay extends IekModel
{
    protected $table = 'tblRechargePay';
    public $primaryKey = 'id';

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','person_id','id');
    }

    public function fromAccount(){
        return $this->belongsTo(self::$NAME_SPACE.'\ThirdPayAccount','from_account_id','id');
    }

    public function toAccount(){
        return $this->belongsTo(self::$NAME_SPACE.'\ThirdPayAccount','to_account_id','id');
    }
}