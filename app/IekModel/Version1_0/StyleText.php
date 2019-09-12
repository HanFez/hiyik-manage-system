<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/15
 * Time: 10:52
 */
namespace App\IekModel\Version1_0;

class StyleText extends IekModel
{
    protected $table = 'tblStyleTexts';

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', self::ROW_ID, self::ID);
    }
}