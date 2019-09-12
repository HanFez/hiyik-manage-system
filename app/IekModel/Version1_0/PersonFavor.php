<?php

namespace App\IekModel\Version1_0;

use Illuminate\Database\Eloquent\Model;

class PersonFavor extends IekModel
{
    //
    protected $table="tblPersonFavors";

    public function favor() {
        return $this->belongsTo(self::$NAME_SPACE.'\Tag', self::TAG_ID, self::ID);
//            ->where(self::CONDITION);
    }
    public static function getPersonFavors($uid) {
        $favorIds = self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->pluck(self::TAG_ID)
            ->toArray();
        if(!is_null($favorIds) && !empty($favorIds)) {
            return $favorIds;
        } else {
            return null;
        }
    }

    public static function insertPersonFavors($uid, $favors) {
        $values = [];
        $date = date(self::DATETIME_FORMAT);
        foreach ($favors as $favor) {
            $values[] = [self::UID => $uid, self::TAG_ID => $favor,
                self::ACTIVE => true, self::REMOVED => false,
                self::CREATED => $date, self::UPDATED => $date,
            ];
        }
        if(!empty($values)) {
            return self::insert($values);
        }
        return true;
    }

    public static function deletePersonFavors($uid, $favors) {
        return self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->whereIn(self::TAG_ID, $favors)
            ->update([self::ACTIVE => false]);
    }
}
