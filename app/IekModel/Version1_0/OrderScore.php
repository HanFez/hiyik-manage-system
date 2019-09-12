<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/13
 * Time: 15:12
 */
namespace App\IekModel\Version1_0;

class OrderScore extends IekModel
{
    protected $table = 'tblOrderScores';
    public $primaryKey = 'id';

    public function reason(){
        return $this->belongsTo(self::$NAME_SPACE.'\Reason','reason_id','id');
    }
}