<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/23
 * Time: 14:47
 */
namespace App\IekModel\Version1_0;

class OrderCommentText extends IekModel
{
    protected $table = 'tblOrderCommentTexts';
    public $primaryKey = 'id';

    public function commentContent(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderCommentContent',IekModel::ID,IekModel::CONTENT_ID);
    }

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }
}