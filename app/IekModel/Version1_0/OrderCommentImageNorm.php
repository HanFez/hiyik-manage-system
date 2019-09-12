<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/23
 * Time: 17:12
 */
namespace App\IekModel\Version1_0;

class OrderCommentImageNorm extends IekModel
{
    protected $table = 'tblOrderCommentImageNorms';
    public $primaryKey = 'id';

    public static function isImageExists($image_id) {
        if(is_null($image_id)) {
            return false;
        }
        $count = self::where(self::IID, $image_id)
            ->where(self::CONDITION)
            ->count();
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}