<?php

namespace App\IekModel\Version1_0;


class AdviceHandle extends IekModel
{
    protected $table = 'tblAdviceHandles';
    public $primaryKey = 'id';
    protected $guarded = [];

    public function adviceReply() {
        return $this->hasOne(self::$NAME_SPACE.'\ManageReply', self::ID, self::REPLY_ID);
    }

    public function adviceOperator() {
        return $this->hasOne(self::$NAME_SPACE.'\Employee', self::ID, self::OID);
    }
}
