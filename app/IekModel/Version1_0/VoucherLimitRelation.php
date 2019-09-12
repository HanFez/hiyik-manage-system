<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/14
 * Time: 15:17
 */
namespace App\IekModel\Version1_0;

class VoucherLimitRelation extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblVoucherLimitRelations';

    public function voucherLimit(){
        return $this->belongsTo(self::$NAME_SPACE.'\VoucherLimit',self::VOUCHER_LID,self::ID);
    }
}