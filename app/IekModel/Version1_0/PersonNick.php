<?php

namespace App\IekModel\Version1_0;

use Illuminate\Database\Eloquent\Model;

class PersonNick extends IekModel
{
    //
    protected $table="tblPersonNicks";
    protected $guarded = [];

    public function nick() {
        return $this->belongsTo(self::$NAME_SPACE.'\Nick', 'nick_id', 'id');
    }

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id', 'id');
    }

    public static function getPersonActiveNick($uid) {
        if(is_null($uid)) {
            return null;
        }
        $nick = null;
        $relations = static::getRecords([['person_id', '=', $uid]]);
        foreach ($relations as $relation) {
            if(!is_null($relation->{self::ACTIVE})
                && $relation->{self::ACTIVE}
                && !$relation->{self::REMOVED}) {
                $tempNick = $relation->nick;
                if(isset($tempNick)
                    && !is_null($tempNick->{self::ACTIVE})
                    && !$tempNick->{self::REMOVED}) {
                    $nick = $tempNick;
                    break;
                }

            }
        }
        return $nick;
    }
    public static function getActiveNick($uid) {
        $nick = null;
        if(!is_null($uid)) {
            $personNick = PersonNick::with('nick')
                ->where(self::ACTIVE,true)
                ->where(self::UID, $uid)
                ->orderBy(self::CREATED)
                ->first();
        }
        return $personNick;
    }

    public static function hasUsed($nickId) {
        $relation = self::where(self::CONDITION)
            ->where(self::NICK_ID, $nickId)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        if(is_null($relation)) {
            return false;
        } else {
            return true;
        }
    }

    public static function insertRelation($uid, $nickId) {
        self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->update([self::ACTIVE=>false]);
        $relation = new self();
        $relation->{self::UID} = $uid;
        $relation->{self::NICK_ID} = $nickId;
        $result = $relation->save();
        if($result) {
            return $relation;
        } else {
            return null;
        }
    }
}
