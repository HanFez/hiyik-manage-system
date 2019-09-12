<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/14
 * Time: 15:03
 */
namespace App\IekModel\Version1_0;

class ExpirationType extends IekModel
{
    protected $table = 'tblExpirationTypes';
    public $primaryKey = 'id';

    public function voucher(){
        return $this->belongsTo(self::$NAME_SPACE.'\Voucher','id','voucher_id');
    }
}