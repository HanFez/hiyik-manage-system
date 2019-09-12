<?php

namespace App\IekModel\Version1_0;


class Name extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblNames';

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }
}
