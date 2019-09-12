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
class PersonAvatar extends IekModel
{
    
    /**
    * @var  person_id           INT4
    * @var  address_id          INT4
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_actived          String
    * @var  is_removed          String
    */

    public $primaryKey = 'id';
    protected $table = 'tblPersonAvatars';
    protected $fillable = ['person_id', 'image_id', 'is_active', 'is_removed', 'updated_at', 'created_at','id'];
    protected $hidden = ['is_active', 'is_removed', 'updated_at', 'created_at'];

    public function avatar() {
        return $this->belongsTo(self::$NAME_SPACE.'\Avatar', 'image_id', 'id');
    }

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id', 'id');
    }

    public static function getPersonAvatars($uid) {
        if(is_null($uid)) {
            return null;
        }
        $relations = static::getRecords([['person_id', '=', $uid]]);
        $avatars = [];
        foreach($relations as $relation) {
            array_push($avatars, $relation->avatar);
        }
        return $avatars;
    }

    public static function getAvatarPersons($aid) {
        if(is_null($aid)) {
            return null;
        }
        $relations = static::getRecords([['avatar_id', '=', $aid]]);
        $persons = [];
        foreach($relations as $relation) {
            array_push($persons, $relation->person);
        }
        return $persons;
    }

    public static function setPersonAvatar($uid, $aid) {
        if(is_null($uid) || is_null($aid)) {
            return null;
        }
        $relation = self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->where(self::IID, $aid)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        if(!is_null($relation)) {
            return $relation;
        } else {
            self::where(self::CONDITION)
                ->where(self::UID, $uid)
                ->update([self::ACTIVE => false]);
            $relation = new self();
            $relation->person_id = $uid;
            $relation->image_id = $aid;
            $result = $relation->save();
            if($result) {
                return $relation;
            } else {
                return null;
            }
        }
    }
    public static function getActiveAvatar($uid) {
        $condition = [
            [self::ACTIVE, true],
            [self::REMOVED, false],
            [self::UID, $uid],
        ];
        $norms = null;
        $relation = self::where($condition)
            ->with(['avatar'=>function($query){
                $query->with('norms');
            }])
            ->where(self::ACTIVE,true)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        return $relation;
    }

    static function getActiveAvatarUris($uid) {
        $uris = [];
        if(!is_null($uid)) {
            $personAvatar = self::where(self::CONDITION)
                ->where(self::UID, $uid)
                ->orderBy(self::CREATED, 'desc')
                ->first();
            if(!is_null($personAvatar)) {
                $norms = AvatarNorm::where('image_id', $personAvatar->image_id)
                    ->where(self::ACTIVE, true)
                    ->where(self::REMOVED, false)
                    ->get();
                foreach ($norms as $norm) {
                    array_push($uris, $norm->uri);
                }
            }
        }
        return $uris;
    }
}

?>
