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
class PersonAddress extends IekModel
{
    
    /**
    * @var  person_id           INT4
    * @var  address_id          INT4
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_actived          String
    * @var  is_removed          String
    */

    // public $timestamps = false;
    // public $incrementing = false;
    public $primaryKey = 'id';
    protected $table = 'tblPersonAddresses';
    protected $guarded = [];

    public function address() {
        return $this->belongsTo(self::$NAME_SPACE.'\Address', 'address_id', 'id');
    }

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', self::UID, self::ID);
    }
    public static function getActiveCity($uid) {
        if(is_null($uid)) {
            return null;
        } else {
            $relation = self::with('address.city')
                ->where(self::UID, $uid)
                ->where(self::ACTIVE, true)
                ->where(self::REMOVED, false)
                ->orderBy(self::UPDATED, 'desc')
                ->first();
            if(!is_null($relation)
                && !is_null($relation->address)
                && !is_null($relation->address->city)) {
                return $relation->address->city->merge_name;
            } else {
                return null;
            }
        }
    }

    public static function getActiveAddresses($uid) {
        if(is_null($uid)) {
            return null;
        } else {
            $relations = self::with('address')
                ->where(self::CONDITION)
                ->where(self::UID, $uid)
                ->orderBy(self::CREATED, 'desc')
                ->get()->each(function($item, $key) {
                    $address = $item->address->address;
                    unset($item->address);
                    $item->address = $address;
                });
            if(!$relations->isEmpty()) {
                return null;
            } else {
                return $relations;
            }
        }
    }
}

?>
