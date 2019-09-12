<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/21
 * Time: 17:43
 */

namespace App\IekModel\Version1_0;


class  RewardWealthPay extends IekModel
{
    protected $table = 'tblRewardWealthPay';
    public $primaryKey = 'id';

    public function fromPerson(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','person_id','id');
    }

    public function toPerson(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','to_id','id');
    }
}