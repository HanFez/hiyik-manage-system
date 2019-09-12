<?php
//
// +------------------------------------------------------------------------+
// | PHP Version 5                                                          |
// +------------------------------------------------------------------------+
// | Copyright (c) All rights reserved.                                     |
// +------------------------------------------------------------------------+
// | File:                                                                  |
// +------------------------------------------------------------------------+
// | Author:                                                                |
// +------------------------------------------------------------------------+
//
// $Id$
//

namespace App\IekModel\Version1_0;

/**
* @author       Rich
*/
class Phone extends IekModel
{
    
    /**
    * @var  phone               String
    * @var  person_id           INT8
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_actived          Boolean
    * @var  is_removed          Boolean
    */

    protected $table = 'tblPhones';
    protected $guarded = [];

    public function person() {
        return $this->hasOne(self::$NAME_SPACE.'\PersonPhone', 'phone_id', 'id')
                    ->select('person_id')
                    ->where('is_active',true)
                    ->where('is_removed',false);
    }

    public function bindPerson() {
        return $this->hasOne(self::$NAME_SPACE.'\PersonPhone', 'phone_id', self::ID)
            ->where(self::ACTIVE, true)
            ->where(self::REMOVED, false)
            ->where('is_bind', true)
            ->orderBy(self::CREATED, 'desc');
            //->first();
    }
    public static function hasExist($content) {
        $phone = self::where(self::CONDITION)
            ->where(self::PHONE, $content)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        return $phone;
    }

    public static function insertPhone($phoneNum) {
        $phone = self::hasExist($phoneNum);
        if(is_null($phone)) {
            $phone = new self();
            $phone->{self::PHONE} = $phoneNum;
            $result = $phone->save();
            if ($result) {
                return $phone;
            } else {
                return null;
            }
        } else {
            return $phone;
        }
    }
}

?>
