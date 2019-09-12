<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 4/6/17
 * Time: 3:48 PM
 */

namespace App\IekModel\Version1_0;


class RefundRequestHandle extends IekModel
{
    protected $table = "tblRefundRequestHandles";
    public $primaryKey = 'id';

    public function handleResult(){
        return $this->belongsTo(self::$NAME_SPACE.'\HandleResult','handle_result_id','id');
    }

    public function money(){
        return $this->belongsTo(self::$NAME_SPACE.'\RefundHandleResultHandle',IekModel::ID,'refund_request_handle_id');
    }
}