<?php

namespace App\IekModel\Version1_0;


class PersonGender extends IekModel {

    protected $table = "tblPersonGenders";
    protected $guarded = [];
    protected $hidden = ['is_active', 'is_removed', 'is_publish', 'updated_at', 'created_at'];

    public function gender() {
        return $this->belongsTo(self::$NAME_SPACE.'\Gender', 'gender_id', 'id');
    }

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id', 'id');
    }
    public static function getActiveGender($uid) {
        if(is_null($uid)) {
            return null;
        } else {
            $personGender = self::where(self::UID, $uid)
                ->where(self::ACTIVE, true)
                ->where(self::REMOVED, false)
                ->orderBy(self::UPDATED, 'desc')
                ->first();
            if(!is_null($personGender)) {
                return $personGender->gender;
            } else {
                return null;
            }
        }
    }
    public static function insertRelation($uid, $genderId) {
        self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->update([self::ACTIVE=>false]);
        $relation = new self();
        $relation->{self::UID} = $uid;
        $relation->{self::GENDER_ID} = $genderId;
        $result = $relation->save();
        if($result) {
            return $relation;
        } else {
            return null;
        }
    }
}
