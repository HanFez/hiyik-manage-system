<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/22
 * Time: 18:46
 */
namespace App\IekModel\Version1_0;
class PersonVoucher extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblPersonVouchers';

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','person_id','id');
    }

    public function voucher(){
        return $this->belongsTo(self::$NAME_SPACE.'\Voucher','voucher_id','id');
    }
}