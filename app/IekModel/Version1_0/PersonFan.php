<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-6-17
 * Time: 下午5:53
 */

namespace App\IekModel\Version1_0;


class PersonFan extends IekModel
{
    const TARGET_PERSON = 'target_person_id';
    protected $table = 'tblPersonFans';

    /**Obtain the current active followed person on target person.
     * @param int $uid The target person id.
     * @return int|mixed
     */
    public static function getFansCount($uid) {
        $count = 0;
        if(!is_null($uid)) {
            $condition = [
                [self::TARGET_PERSON, $uid],
                ['is_active', true],
                ['is_removed', false],
            ];
            $count = self::getCount($condition);
        }
        return $count;
    }
    public static function getFans($uid = null) {
        $persons = null;
        if(is_null($uid)) {
            return $persons;
        }
        $relations = self::where(self::TARGET_PERSON, $uid)
            ->where(self::CONDITION)->get();
        foreach ($relations as $relation) {
            if(!is_null($relation->{self::UID})) {
                $person = $relation->fan;
                $persons[] = $person;
            }
        }
        return $persons;
    }


    public static function getPersons($uid = null) {
        $persons = null;
        if(is_null($uid)) {
            return $persons;
        }
        $relations = self::where(self::UID, $uid)
            ->where(self::CONDITION)
            ->get();
        foreach ($relations as $relation) {
            if(!is_null($relation->{self::TARGET_PERSON})) {
                $person = $relation->person;
                $persons[] = $person;
            }
        }
        return $persons;
    }
    public static function getPersonCount($uid = null) {
        $count = 0;
        if(is_null($uid)) {
            return $count;
        }
        $count = self::where(self::UID, $uid)
            ->where(self::CONDITION)
            ->count();
        return $count;
    }

    public function isDuplicate() {
        $condition = [
            [self::UID, '=', $this->{self::UID}],
            [self::TARGET_PERSON, '=', $this->{self::TARGET_PERSON}],
            [self::ACTIVE, '=', $this->{self::ACTIVE}],
            [self::REMOVED, '=', $this->{self::REMOVED}],

        ];
        return self::isDuplicated($condition);
    }
    
    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'target_person_id');
    }

    public function fan() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id');
    }

    public static function isFan($uid, $fanId) {
        $condition = [
            [self::UID, '=', $fanId],
            [self::TARGET_PERSON, '=', $uid],
            [self::ACTIVE, '=', true],
            [self::REMOVED, '=', false],
        ];
        $count = self::where($condition)->count();
        if($count > 0) {
            return true;
        }
        return false;
    }
}