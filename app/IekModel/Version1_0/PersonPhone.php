<?php

namespace App\IekModel\Version1_0;


class PersonPhone extends IekModel {

    protected $table = "tblPersonPhones";
    protected $guarded = [];

    public function phone() {
        return $this->belongsTo(self::$NAME_SPACE.'\Phone', 'phone_id', 'id');
    }

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id', 'id');
    }

    public static function getPhones($uid) {
        $relations = self::with('phone')
            ->where(self::CONDITION)
            ->where(self::UID, $uid)
            ->orderBy(self::CREATED, 'desc')
            ->get()
            ->toArray();
        if(empty($relations)) {
            return null;
        }
        return $relations;
    }

    public static function getBindPhone($uid) {
        $relations = self::with('phone')
            ->where(self::CONDITION)
            ->where(self::UID, $uid)
            //->where('is_bind', true)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        if($relations === null) {
            return null;
        }
        return $relations;
    }
    public static function hasUsed($phoneId) {
        $relation = self::where(self::CONDITION)
            ->where(self::PHONE_ID, $phoneId)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        if(is_null($relation)) {
            return false;
        } else {
            return true;
        }
    }

    public static function insertRelation($uid, $phoneId) {
        self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->update(['is_bind' => false]);
        $relation = new self();
        $relation->{self::UID} = $uid;
        $relation->{self::PHONE_ID} = $phoneId;
        $relation->is_bind = true;
        $result = $relation->save();
        if($result) {
            return $relation;
        } else {
            return null;
        }
    }

    public static function hasBound($phoneNum) {
        $bind = self::whereHas('phone', function($query) use ($phoneNum) {
            $query->where(self::CONDITION)
                ->where('phone', $phoneNum);
            })
            ->where('is_bind', true)
            ->where(self::CONDITION)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        if(!is_null($bind)) {
            return true;
        } else {
            return false;
        }
    }
}
