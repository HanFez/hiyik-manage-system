<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/29
 * Time: 15:57
 */
namespace App\IekModel\Version1_0;

class OrderReply extends IekModel
{
    protected $table = 'tblOrderReplies';
    public $primaryKey = 'id';

    public function replyContent(){
        return $this->hasMany(self::$NAME_SPACE.'\OrderReplyContent',IekModel::REPLY_ID,IekModel::ID);
    }
}