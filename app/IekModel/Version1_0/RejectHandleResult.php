<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/17
 * Time: 10:56
 */
namespace App\IekModel\Version1_0;

class RejectHandleResult extends IekModel
{
    protected $table = 'tblRejectHandleResults';

    public function reason(){
        return $this->belongsTo(self::$NAME_SPACE.'\Reason','reason_id','id');
    }
}