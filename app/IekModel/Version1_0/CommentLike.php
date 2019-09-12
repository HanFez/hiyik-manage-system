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
class CommentLike extends IekModel
{
    const CID_NAME = 'comment_id';
    const UID_NAME = 'person_id';

    protected $table = 'tblCommentLikes';

    public static function getLikeCount($cid) {
        $count = 0;
        if(!is_null($cid)) {
            $count = self::where([
                [self::CID_NAME, $cid],
                ['is_active', true],
                ['is_removed', false],
            ])->count();
        }
        return $count;
    }

    public static function getRecord($cid, $uid) {
        $result = null;
        if(!is_null($cid) && !is_null($uid)) {
            $result = self::where([
                [self::CID_NAME, '=', $cid],
                [self::UID_NAME, '=', $uid],
            ])->get();
        }
        if(is_bool($result)) {
            $result = null;
        }
        return $result;
    }
    public static function getLikes($cid = null) {
        $persons = [];
        if(is_null($cid)) {
            return $persons;
        }
        $relations = self::where(self::CID_NAME, $cid)->get();
        foreach ($relations as $relation) {
            if(!is_null($relation->{self::UID_NAME})) {
                $person = new \stdClass();
                $person->id = $relation->person->id;
                $person->nick = $relation->person->getNick();
                array_push($persons, $person);
            }
        }
        return $persons;
    }
    public function setData(array $data) {
        if(array_has($data, self::CID_NAME)) {
            $this->{self::CID_NAME} = $data[self::CID_NAME];
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
        if(!is_null($this->{self::CID_NAME}) && !is_null($this->{self::UID_NAME})) {
            $count = self::where([
                [self::CID_NAME, $this->{self::CID_NAME}],
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
                [self::CID_NAME, $this->{self::CID_NAME}],
                [self::UID_NAME, $this->{self::UID_NAME}],
                ['is_active', true],
                ['is_removed', false],
            ])->first();
            $this->updated_at = $temp->updated_at;
            $this->created_at = $temp->created_at;
            $err->setError('Exist');
            return $err;
        }
        if(is_null($this->{self::CID_NAME})
            || is_null($this->{self::UID_NAME})) {
            $err->setError('InvalidData');
            return $err;
        }
        // To check the integrity of database.
        if(!Comment::isExists($this->{self::CID_NAME})
            || !Person::isExists($this->{self::UID_NAME})) {
            $err->setError('InvalidData');
            return $err;
        }
        $result = $this->save();
        if(!$result) {
            $err->setError('Failed');
        }
        return $err;
    }
    
    

    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Comment', self::CID_NAME, 'id');
    }

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', self::UID_NAME, 'id');
    }
}