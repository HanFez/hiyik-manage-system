<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/17
 * Time: 11:06
 */
namespace App\IekModel\Version1_0;

class RejectHandle extends IekModel
{
    protected $table = 'tblRejectHandles';
    public $primaryKey = 'id';

    public function rejectHandleResult(){
        return $this->belongsTo(self::$NAME_SPACE.'\RejectHandleResult','reject_handle_result_id','id');
    }
}