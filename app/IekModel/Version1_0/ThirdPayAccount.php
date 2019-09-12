<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 10:11
 */

namespace App\IekModel\Version1_0;


class ThirdPayAccount extends IekModel
{
    protected $table = 'tblThirdPayAccounts';
    public $primaryKey = 'id';

    public static function isExistAccount($account){
        $result = self::where(IekModel::ACCOUNT,$account)->first();
        if(is_null($result)){
            $tpaccount = new self();
            $tpaccount->account = $account;
            $tpaccount->platform = 'ali';
            $tpaccount->save();
            return $tpaccount->id;
        }else{
            return $result->id;
        }
    }
}