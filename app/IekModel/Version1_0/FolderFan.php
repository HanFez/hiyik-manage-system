<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-6-17
 * Time: 上午11:30
 */

namespace App\IekModel\Version1_0;


class FolderFan extends IekModel
{
    protected $table = 'tblFolderFans';


    /**Obtain the current active followed person on publication.
     * @param int $fid The folder id.
     * @return int|mixed
     */
    public static function getFansCount($fid) {
        $count = 0;
        if(!is_null($fid)) {
            $condition = [
                [self::FID, $fid],
                [self::ACTIVE, true],
                [self::REMOVED, false],
            ];
            $count = self::getCount($condition);
        }
        return $count;
    }

    /**Get collected publication count in a folder.
     * @param int $uid The person id.
     * @return int|mixed
     */
    public static function getFollowedCount($uid) {
        $count = 0;
        if(!is_null($uid)) {
            $condition = [
                [self::UID, $uid],
                [self::ACTIVE, true],
                [self::REMOVED, false],
            ];
            $count = self::getCount($condition);
        }
        return $count;
    }

    /**Get a followed relation.
     * @param int $uid The person id.
     * @param int $fid The folder id.
     * @param boolean $isActive The status user action
     * @param boolean $isRemoved The status manager action
     * @return array
     */
    public static function getRecord($uid, $fid, $isActive = true, $isRemoved=false) {
        $result = null;
        if(!is_null($uid) && !is_null($fid)) {
            $condition = [
                [self::UID, '=', $uid],
                [self::FID, '=', $fid],
            ];
            array_push($condition, [self::ACTIVE, '=', $isActive]);
            array_push($condition, [self::REMOVED, '=', $isRemoved]);
            $result = self::where($condition)->get();
        }
        return $result;
    }

    public static function getFans($fid = null) {
        $persons = [];
        if(is_null($fid)) {
            return $persons;
        }
        $relations = self::where(self::FID, $fid)->get();
        foreach ($relations as $relation) {
            if(!is_null($relation->{self::FID})) {
                $person = $relation->folder->person;
                array_push($persons, $person);
            }
        }
        return $persons;
    }

    public static function getFollowedFolders($uid = null, $skip = 0, $take = null,
                                           $isDesc = false,
                                           $orderBy = self::CREATED_AT) {
        $desc = 'ASC';
        if($isDesc) {
            $desc = 'DESC';
        }
        $folders = [];
        if(is_null($uid)) {
            return $folders;
        }
        $condition = [
            [self::UID, '=', $uid],
            [self::ACTIVE, '=', true],
            [self::REMOVED, '=', false],
        ];
        if(!is_null($take)) {
            $relations = self::with('folder')
                ->where($condition)->orderBy($orderBy, $desc)
                ->skip($skip)->take($take)->get();
        } else {
            $relations = self::with('folder')
                ->where($condition)->orderBy($orderBy, $desc)->get();
        }
        foreach ($relations as $relation) {
            if(!is_null($relation->{self::UID})) {
                $relation->folder->count = Folder::getContentCount($relation->folder->id);
                array_push($folders, $relation->folder);
            }
        }
        return $folders;
    }

    public function setData(array $data) {
        if(array_has($data, self::UID)) {
            $this->{self::UID} = $data[self::UID];
        }
        if(array_has($data, self::FID)) {
            $this->{self::FID} = $data[self::FID];
        }
        if(array_has($data, self::ACTIVE))  {
            $this->is_active = $data[self::ACTIVE];
        } else {
            $this->is_active = true;
        }
        if(array_has($data, self::REMOVED)) {
            $this->is_removed = $data[self::REMOVED];
        } else {
            $this->is_removed = false;
        }
    }

    public function isDuplicate() {
        $count = 0;
        if(!is_null($this->{self::UID}) && !is_null($this->{self::FID})) {
            $count = self::where([
                [self::UID, $this->{self::UID}],
                [self::FID, $this->{self::FID}],
                [self::ACTIVE, $this->is_active],
                [self::REMOVED, $this->is_removed],
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
                [self::UID, $this->{self::UID}],
                [self::FID, $this->{self::FID}],
                [self::ACTIVE, true],
                [self::REMOVED, false],
            ])->first();
            $this->updated_at = $temp->updated_at;
            $this->created_at = $temp->created_at;
            $err->setError('Exist');
            return $err;
        }
        if(is_null($this->{self::UID})
            || is_null($this->{self::FID})) {
            $err->setError('InvalidData');
            return $err;
        }
        // To check the integrity of database.
        if(!Publication::isExists($this->{self::UID})
            || !Folder::isExists($this->{self::FID})) {
            $err->setError('InvalidData');
            return $err;
        }
        $result = $this->save();
        if(!$result) {
            $err->setError('Failed');
        }
        return $err;
    }

    public function getFolderOwner() {
        $owner = $this->folder->person;
        return $owner;
    }
    
    public function getFolderOwnerId() {
        $owner = $this->getFolderOwner();
        if(!is_null($owner)) {
            return $owner->id;
        }
        return null;
    }
    public function folder() {
        return $this->belongsTo(self::$NAME_SPACE.'\Folder', 'folder_id');
    }

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\person', 'person_id');
    }

}