<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/20
 * Time: 11:20
 */

namespace app\IekModel\Version1_0;


class IwallComment extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblIwallComments';

    public function iwall() {
        return $this->belongsTo(self::$NAME_SPACE.'\Iwall', 'iwall_id');
    }

    public function comment() {
        return $this->belongsTo(self::$NAME_SPACE.'\Comment', 'comment_id');
    }

    public static function getCommentCount($iid = null, $begin = null, $end = null, $isActive = true, $isRemoved =false) {
        if(is_null($iid)) {
            return self::count();
        }
        $condition = [
            ['iwall_id', $iid],
            [self::ACTIVE, '=', $isActive],
            [self::REMOVED, '=', $isRemoved],
        ];
        if(!is_null($begin)) {
            array_push($condition, [self::CREATED_AT, '>=', $begin]);
        }
        if(!is_null($end)) {
            array_push($condition, [self::CREATED_AT, '<', $end]);
        }
        return self::where($condition)->count();
    }
}

?>
