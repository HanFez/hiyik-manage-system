<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/23
 * Time: 17:09
 */
namespace App\IekModel\Version1_0;

class OrderCommentImage extends IekModel
{
    protected $table = 'tblOrderCommentImages';
    public $primaryKey = 'id';

    public function norms(){
        return $this->hasMany(self::$NAME_SPACE.'\OrderCommentImageNorm','image_id','id');
    }

    public static function isHashExist($hash) {
        if(is_null($hash)) {
            return false;
        }
        $count = self::where(self::HASH, $hash)->count();
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getImageByHash($hash) {
        if(is_null($hash)) {
            return null;
        }
        return self::where(self::HASH, $hash)
//            ->where(self::CONDITION)
            ->orderBy(self::CREATED, 'desc')
            ->first();
    }

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }

    public function commentContent(){
        return $this->belongsTo(self::$NAME_SPACE.'\OrderCommentContent',IekModel::ID,IekModel::CONTENT_ID);
    }
}