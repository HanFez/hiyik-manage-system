<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/17
 * Time: 14:41
 */
namespace App\IekModel\Version1_0;

class RejectProduct extends IekModel
{
    protected $table = 'tblRejectProducts';

    public function rejectRequestImages(){
        return $this->hasMany(self::$NAME_SPACE.'\RejectRequestImage',self::REJECT_PID,self::ID);
    }

    public function reason(){
        return $this->belongsTo(self::$NAME_SPACE.'\Reason',self::REAID,self::ID);
    }

    public function products(){
        return $this->belongsTo('App\IekModel\Version1_0\Product\Product',self::PRODUCT_ID,self::ID);
    }

    public function rejectHandle(){
        return $this->belongsTo(self::$NAME_SPACE.'\RejectHandle',self::ID,self::REJECT_PID);
    }
}