<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/6/7
 * Time: 17:44
 */
namespace App\IekModel\Version1_0;

class OrderReplyContent extends IekModel
{
    protected $table = 'tblOrderReplyContents';

    public function text(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderCommentText',IekModel::CONTENT_ID,IekModel::ID);
    }

    public function image(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderCommentImage',IekModel::CONTENT_ID,IekModel::ID);
    }
}
?>