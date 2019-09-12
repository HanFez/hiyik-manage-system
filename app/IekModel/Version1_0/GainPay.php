<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/21
 * Time: 18:05
 */

namespace App\IekModel\Version1_0;


class GainPay extends IekModel
{
    protected $table = 'tblGainPay';
    public $primaryKey = 'id';

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','person_id','id');
    }
}