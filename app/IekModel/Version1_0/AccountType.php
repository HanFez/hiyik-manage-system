<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-5-4
 * Time: 下午5:15
 */

namespace App\IekModel\Version1_0;

use App\IekModel\Version1_0\IekModel;

class AccountType extends IekModel {
    protected $table = "tblAccountTypes";
    protected $guarded =[];

    public static function getByName($name) {
        $t = self::where(self::CONDITION)
            ->where(self::NAME, $name)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        return $t;
    }
}