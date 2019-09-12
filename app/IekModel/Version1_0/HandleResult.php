<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/9
 * Time: 15:59
 */
namespace App\IekModel\Version1_0;

class HandleResult extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblHandleResults';

    public function reason(){
        return $this->belongsTo(self::$NAME_SPACE.'\Reason','reason_id','id');
    }
}