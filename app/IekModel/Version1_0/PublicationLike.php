<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-6-22
 * Time: 下午1:25
 */

namespace App\IekModel\Version1_0;

use App\IekModel\Version1_0\Publication;
use App\IekModel\Version1_0\Person;
class PublicationLike extends IekModel
{
    const PID_NAME = 'publication_id';
    const UID_NAME = 'person_id';

    protected $table = 'tblPublicationLikes';

    public static function getLikeCount($pid, $begin = null, $end = null) {
        $count = 0;
        if(!is_null($pid)) {
            $condition = [
                [self::PID_NAME, $pid],
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

    public static function getRecord($pid, $uid) {
        $result = null;
        if(!is_null($pid) && !is_null($uid)) {
            $result = self::where([
                [self::PID_NAME, '=', $pid],
                [self::UID_NAME, '=', $uid],
            ])->get();
        }
        if(is_bool($result)) {
            $result = null;
        }
        return $result;
    }
    public static function getLikes($pid = null) {
        $persons = [];
        if(is_null($pid)) {
            return $persons;
        }
        $relations = self::where(self::PID_NAME, $pid)->get();
        foreach ($relations as $relation) {
            if(!is_null($relation->{self::UID_NAME})) {
                $person = $relation->person;
                array_push($persons, $person);
            }
        }
        return $persons;
    }
    public function setData(array $data) {
        if(array_has($data, self::PID_NAME)) {
            $this->{self::PID_NAME} = $data[self::PID_NAME];
        }
        if(array_has($data, self::UID_NAME)) {
            $this->{self::UID_NAME} = $data[self::UID_NAME];
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
        if(!is_null($this->{self::PID_NAME}) && !is_null($this->{self::UID_NAME})) {
            $count = self::where([
                [self::PID_NAME, $this->{self::PID_NAME}],
                [self::UID_NAME, $this->{self::UID_NAME}],
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
                [self::PID_NAME, $this->{self::PID_NAME}],
                [self::UID_NAME, $this->{self::UID_NAME}],
                ['is_active', true],
                ['is_removed', false],
            ])->first();
            $this->updated_at = $temp->updated_at;
            $this->created_at = $temp->created_at;
            $err->setError('Exist');
            return $err;
        }
        if(is_null($this->{self::PID_NAME}) 
            || is_null($this->{self::UID_NAME})) {
            $err->setError('InvalidData');
            return $err;
        }
        // To check the integrity of database.
        if(!Publication::isExists($this->{self::PID_NAME})
            || !Person::isExists($this->{self::UID_NAME})) {
            $err->setError('InvalidData');
            return $err;
        }
        $this->save();
        return $err;
    }
    
    

    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Publication', self::PID_NAME, 'id');
    }

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', self::UID_NAME, 'id');
    }
}