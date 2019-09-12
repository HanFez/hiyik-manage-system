<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/12/4
 * Time: 15:28
 */
namespace App\IekModel\Version1_0;

class RejectResultHandle extends IekModel
{
    protected $table = 'tblRejectResultHandles';

    public function reject(){
        return $this->belongsTo(self::$NAME_SPACE.'\Reject',self::REJECT_ID,self::ID);
    }

    public function rejectRequest(){
        return $this->belongsTo(self::$NAME_SPACE.'\RejectRequest',self::REJECT_RID,self::ID);
    }
}
?>