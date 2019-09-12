<?php

namespace App\IekModel\Version1_0;


class PersonSignature extends IekModel
{
    //
    protected $table="tblPersonSignatures";
    protected $guarded = [];
    protected $hidden = [];

    public function signature() {
        return $this->belongsTo(self::$NAME_SPACE.'\Signature', 'signature_id', 'id');
    }

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', self::UID, 'id');
    }
    public static function getActiveSignature($uid) {
        if(is_null($uid)) {
            return null;
        } else {
            $relation = self::with('signature')
                ->where(self::ACTIVE,true)
                ->where(self::UID, $uid)
                ->orderBy(self::UPDATED, 'desc')
                ->first();
            return $relation;
        }
    }
    public static function insertRelation($uid, $sigId) {
        self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->update([self::ACTIVE=>false]);
        $relation = new self();
        $relation->{self::UID} = $uid;
        $relation->{self::SIGNATURE_ID} = $sigId;
        $result = $relation->save();
        if($result) {
            return $relation;
        } else {
            return null;
        }
    }

    public static function getPerson($tid){
        $persons = self::where(IekModel::SIGNATURE_ID,$tid)
            ->where(IekModel::CONDITION)
            ->vaule(IekModel::UID);
        dd($persons);
        return $persons;
    }
}
