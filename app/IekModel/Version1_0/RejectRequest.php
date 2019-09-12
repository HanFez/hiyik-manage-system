<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/22
 * Time: 19:56
 */
namespace App\IekModel\Version1_0;
class RejectRequest extends IekModel
{
    protected $table = 'tblRejectRequests';
    public $primaryKey = 'id';

    public function order(){
        return $this->belongsTo(self::$NAME_SPACE.'\Order',self::ORDER_ID,self::ID);
    }

    public function rejectProducts(){
        return $this->hasMany(self::$NAME_SPACE.'\RejectProduct',self::REJECT_RID,self::ID);
    }

    public function rejectResultHandle(){
        return $this->belongsTo(self::$NAME_SPACE.'\RejectResultHandle',self::ID,self::REJECT_RID);
    }
}