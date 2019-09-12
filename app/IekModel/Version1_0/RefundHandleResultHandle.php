<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/9
 * Time: 16:02
 */
namespace App\IekModel\Version1_0;

class RefundHandleResultHandle extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblRefundHandleResultHandles';

    public function returnPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderReturnPay','return_pay_id',IekModel::ID);
    }
}