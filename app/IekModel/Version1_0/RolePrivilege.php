<?php

namespace App\IekModel\Version1_0;


class RolePrivilege extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblRolePrivileges';
    protected $guarded = [];

    public function privilege() {
        return $this->belongsTo(self::$NAME_SPACE.'\Privilege', 'privilege_id', self::ID);
    }
}
