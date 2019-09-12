<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/11/27
 * Time: 15:57
 */
namespace App\IekModel\Version1_0;

class OrderReturnPay extends IekModel
{
    protected $table = 'tblOrderReturnPay';

    public static function isExist($id){
        $re = self::where(self::ORDER_ID,$id)
            ->count();
        return $re == 0 ? false : true;
    }

    public function wealthPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderReturnWealthPay',IekModel::PAY_ID,IekModel::ID);
    }

    public function thirdPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderReturnThirdPay',IekModel::PAY_ID,IekModel::ID);
    }
}
?>