<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 9:25
 */

namespace App\IekModel\Version1_0;


class ViewRewardPay extends IekModel
{
    protected $table = 'viewRewardPay';

    public function rewardThirdPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\RewardThirdPay',self::PAY_ID,self::ID);
    }

    public function rewardWealthPay(){
        return $this->belongsTo(self::$NAME_SPACE.'\RewardWealthPay',self::PAY_ID,self::ID);
    }

    public function fromPerson(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person',self::UID,self::ID);
    }

    public function toPerson(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person',self::TO_ID,self::ID);
    }
}