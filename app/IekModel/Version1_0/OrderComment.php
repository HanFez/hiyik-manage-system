<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/22
 * Time: 19:44
 */
namespace App\IekModel\Version1_0;

class OrderComment extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblOrderComments';

    public function order(){
        return $this->belongsTo(self::$NAME_SPACE.'\Order',IekModel::ORDER_ID,IekModel::ID);
    }

    public function comment(){
        return $this->hasMany(self::$NAME_SPACE.'\OrderCommentContent',IekModel::CID,IekModel::ID);
    }

    public function reply(){
        return $this->hasMany(self::$NAME_SPACE.'\OrderReply',IekModel::CID,IekModel::ID);
    }
}