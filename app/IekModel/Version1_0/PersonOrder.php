<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/27
 * Time: 17:20
 */
namespace App\IekModel\Version1_0;

class PersonOrder extends IekModel
{
    protected $table = 'tblPersonOrders';
    public $primaryKey = 'id';

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','person_id','id');
    }
}