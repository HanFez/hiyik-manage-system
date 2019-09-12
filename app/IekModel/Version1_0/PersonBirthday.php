<?php

namespace App\IekModel\Version1_0;


class PersonBirthday extends IekModel
{
    protected $table = 'tblPersonBirthdays';

    public function birthday() {
        return $this->hasOne(self::$NAME_SPACE.'\Birthday', self::ID, 'birthday_id');
    }
}
