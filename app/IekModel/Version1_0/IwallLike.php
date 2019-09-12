<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/20
 * Time: 11:03
 */

namespace App\IekModel\Version1_0;


class IwallLike extends IekModel
{
    protected $table = 'tblIwallLikes';

    public static function getLikeCount($iid, $begin = null, $end = null) {
        $count = 0;
        if(!is_null($iid)) {
            $condition = [
                ['iwall_id', $iid],
                ['is_active', true],
                ['is_removed', false],
            ];
            if(!is_null($begin)) {
                array_push($condition, [self::CREATED_AT, '>=', $begin]);
            }
            if(!is_null($end)) {
                array_push($condition, [self::CREATED_AT, '<', $end]);
            }
            $count = self::where($condition)->count();
        }
        return $count;
    }

    public static function getRecord($iid, $uid) {
        $result = null;
        if(!is_null($iid) && !is_null($uid)) {
            $result = self::where([
                ['iwall_id', $iid],
                ['person_id', $uid],
            ])->get();
        }
        if(is_bool($result)) {
            $result = null;
        }
        return $result;
    }
    public static function getLikes($iid = null) {
        $persons = [];
        if(is_null($iid)) {
            return $persons;
        }
        $relations = self::where('iwall_id', $iid)->get();
        foreach ($relations as $relation) {
            if(!is_null($relation->{'person_id'})) {
                $person = $relation->person;
                array_push($persons, $person);
            }
        }
        return $persons;
    }
    public function setData(array $data) {
        if(array_has($data, 'iwall_id')) {
            $this->{'iwall_id'} = $data['iwall_id'];
        }
        if(array_has($data, 'person_id')) {
            $this->{'person_id'} = $data['person_id'];
        }
        if(array_has($data, 'is_active'))  {
            $this->is_active = $data['is_active'];
        } else {
            $this->is_active = true;
        }
        if(array_has($data, 'is_removed')) {
            $this->is_removed = $data['is_removed'];
        } else {
            $this->is_removed = false;
        }
    }

    public function isDuplicate() {
        $count = 0;
        if(!is_null($this->{'iwall_id'}) && !is_null($this->{'person_id'})) {
            $count = self::where([
                ['iwall_id', $this->{'iwall_id'}],
                ['person_id', $this->{'person_id'}],
                ['is_active', $this->is_active],
                ['is_removed', $this->is_removed],
            ])->count();
        }
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function saveRecord() {
        $err = new Error();
        if($this->isDuplicate()) {
            $temp = self::where([
                ['iwall_id', $this->{'iwall_id'}],
                ['person_id', $this->{'person_id'}],
                ['is_active', true],
                ['is_removed', false],
            ])->first();
            $this->updated_at = $temp->updated_at;
            $this->created_at = $temp->created_at;
            $err->setError('Exist');
            return $err;
        }
        if(is_null($this->{'iwall_id'})
            || is_null($this->{'person_id'})) {
            $err->setError('InvalidData');
            return $err;
        }
        // To check the integrity of database.
        if(!Iwall::isExists($this->{'iwall_id'})
            || !Person::isExists($this->{'person_id'})) {
            $err->setError('InvalidData');
            return $err;
        }
        $this->save();
        return $err;
    }

    public function iwall() {
        return $this->belongsTo(self::$NAME_SPACE.'\Iwall', 'iwall_id', 'id');
    }

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id', 'id');
    }
}