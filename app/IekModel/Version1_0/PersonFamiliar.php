<?php

namespace App\IekModel\Version1_0;


class PersonFamiliar extends IekModel {
    protected $table="tblPersonFamiliars";

    public function familiar() {
        return $this->belongsTo(self::$NAME_SPACE.'\Tag', self::TAG_ID, self::ID);
    }
    public static function getPersonFamiliars($uid) {
        $familiarIds = self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->pluck(self::TAG_ID)
            ->toArray();
        if(!is_null($familiarIds) && !empty($familiarIds)) {
            return $familiarIds;
        } else {
            return null;
        }
    }

    public static function insertPersonFamiliars($uid, $familiars) {
        $values = [];
        $date = date(self::DATETIME_FORMAT);
        foreach ($familiars as $familiar) {
            $values[] = [self::UID => $uid, self::TAG_ID => $familiar,
                self::ACTIVE => true, self::REMOVED => false,
                self::CREATED => $date, self::UPDATED => $date,
            ];
        }
        if(!empty($values)) {
            return self::insert($values);
        }
        return true;
    }

    public static function deletePersonFamiliars($uid, $familiars) {
        return self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->whereIn(self::TAG_ID, $familiars)
            ->update([self::ACTIVE => false]);
    }
}
