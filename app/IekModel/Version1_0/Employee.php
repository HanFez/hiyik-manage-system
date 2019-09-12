<?php

namespace App\IekModel\Version1_0;

class Employee extends IekModel {

    protected $table='tblEmployees';
    protected $guarded = [];
    public $incrementing = false;
    protected $primaryKey = 'id';

    public function manager() {
        return $this->hasOne(self::$NAME_SPACE . '\Manager', 'id', 'id');
    }
}
