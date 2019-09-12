<?php

namespace App\IekModel\Version1_0;


class Sex extends IekModel
{
    protected $table = 'tblSexes';

    public static function checkExist($name){
        $count = self::where(self::CONDITION)
            ->where(self::NAME,$name)
            ->count();
        return $count>0 ? true : false;
    }
}
