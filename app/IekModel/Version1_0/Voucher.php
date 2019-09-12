<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/22
 * Time: 18:50
 */
namespace App\IekModel\Version1_0;
class Voucher extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblVouchers';

    public function expiration(){
        return $this->hasMany(self::$NAME_SPACE.'\ExpirationType',self::VOUCHER_ID,self::ID)
            ->where(IekModel::CONDITION);
    }

    public function voucherLimitRelation(){
        return $this->hasMany(self::$NAME_SPACE.'\VoucherLimitRelation',self::VOUCHER_ID,self::ID)
            ->where(IekModel::CONDITION);
    }

    public function voucherGetDate(){
        return $this->hasOne(self::$NAME_SPACE.'\VoucherGetDate',self::VOUCHER_ID,self::ID)
            ->where(IekModel::CONDITION);
    }
}