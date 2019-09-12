<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/6/10
 * Time: 15:53
 */
namespace App\IekModel\Version1_0;

class OrderCommentContent extends IekModel
{
    protected $table = 'tblOrderCommentContents';

    public function text(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderCommentText',IekModel::CONTENT_ID,IekModel::ID);
    }

    public function image(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderCommentImage',IekModel::CONTENT_ID,IekModel::ID);
    }

    public function comment(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderComment',IekModel::CID,IekModel::ID);
    }
}
?>