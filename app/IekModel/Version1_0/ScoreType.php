<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-8-10
 * Time: 下午5:12
 */

namespace App\IekModel\Version1_0;


class ScoreType extends IekModel
{
    const LIVELY = 'lively';
    const CHARM = 'charm';
    const CREATIVE = 'creative';
    const BUY = 'buy';
    protected $table = 'tblScoreTypes';

    public static function getIdByName($name) {
        $scoreType = self::where(self::NAME, $name)
            ->where(self::ACTIVE, true)
            ->where(self::REMOVED, false)
            ->first();
        if(!is_null($scoreType)) {
            return $scoreType->{self::ID};
        }
        return null;
    }

}