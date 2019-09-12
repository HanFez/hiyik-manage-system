<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-20
 * Time: 下午2:00
 */

namespace App\IekModel\Version1_0;


class ScoreRule extends IekModel {
    const TABLE_NAME = "table_name";
    protected $table = "tblScoreRules";

    public static function getIdByAction($action) {
        $rule = self::where(self::ACTION, $action)
            ->where(self::ACTIVE, true)
            ->where(self::REMOVED, false)
            ->first();
        if(!is_null($rule)) {
            return $rule->id;
        }
        return null;
    }

    public static function getRule($action, $scoreTypeId) {
        $rule = self::where(self::ACTION, $action)
            ->where(self::SCORE_TID, $scoreTypeId)
            ->where(self::ACTIVE, true)
            ->where(self::REMOVED, false)
            ->first();
        if(!is_null($rule)) {
            return $rule;
        }
        return null;
    }
    public static function getRules($scoreTypeId) {
        $rules = self::where(self::SCORE_TID, $scoreTypeId)
            ->where(self::ACTIVE, true)
            ->where(self::REMOVED, false)
            ->orderBy(self::ID)
            ->get();
        return $rules;
    }
}