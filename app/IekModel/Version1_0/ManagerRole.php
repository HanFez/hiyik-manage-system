<?php

namespace App\IekModel\Version1_0;



class ManagerRole extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblManagerRoles';
    protected $guarded = [];

    public function roles() {
        return $this->belongsTo(self::$NAME_SPACE . '\Role', 'role_id', 'id');
    }
}
