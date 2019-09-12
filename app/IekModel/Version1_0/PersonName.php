<?php

namespace App\IekModel\Version1_0;


class PersonName extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblPersonNames';


    public function name() {
        return $this->belongsTo(self::$NAME_SPACE.'\Name', 'name_id');
    }
}
