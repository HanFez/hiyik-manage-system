<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/22
 * Time: 17:37
 */
namespace App\IekModel\Version1_0;
class OrderStatus extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblOrderStatus';

    public function status(){
        return $this->belongsTo(self::$NAME_SPACE.'\Status','status_id','id');
    }

    public function reason(){
        return $this->belongsTo(self::$NAME_SPACE.'\Reason','reason_id','id');
    }

    public function personOrder(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonOrder','order_id','order_id');
    }

    public function orderStatusHandle(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderStatus','order_status_id','id');
    }

}