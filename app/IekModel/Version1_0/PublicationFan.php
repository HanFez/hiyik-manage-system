<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-6-17
 * Time: 上午11:30
 */

namespace App\IekModel\Version1_0;


class PublicationFan extends IekModel
{
    protected $table = 'tblPublicationFans';
    const PID_NAME = 'publication_id';
    const FID_NAME = 'folder_id';


    /**Obtain the current active followed person on publication.
     * @param int $pid The publication id.
     * @param string $begin The begin date.
     * @param string $end The end date.
     * @return int|mixed
     */
    public static function getFansCount($pid, $begin = null, $end = null) {
        $count = 0;
        if(!is_null($pid)) {
            $condition = [
                [self::PID_NAME, $pid],
                [self::ACTIVE, true],
                [self::REMOVED, false],
            ];
            if(!is_null($begin)) {
                array_push($condition, [self::CREATED_AT, '>=', $begin]);
            }
            if(!is_null($end)) {
                array_push($condition, [self::CREATED_AT, '<', $end]);
            }
            $count = self::getCount($condition);
        }
        return $count;
    }

    /**Get collected publication count in a folder.
     * @param int $fid The folder id.
     * @return int|mixed
     */
    public static function getPublicationsCount($fid) {
        $count = 0;
        if(!is_null($fid)) {
            $condition = [
                [self::FID_NAME, $fid],
                [self::ACTIVE, true],
                [self::REMOVED, false],
            ];
            $count = self::getCount($condition);
        }
        return $count;
    }

    /**Get a followed relation.
     * @param int $pid The publication id.
     * @param int $fid The folder id.
     * @param bool $isActive Default active status.
     * @return array
     */
    public static function getRecord($pid, $fid, $isActive = true) {
        $result = null;
        if(!is_null($pid) && !is_null($fid)) {
            $condition = [
                [self::PID_NAME, '=', $pid],
                [self::FID_NAME, '=', $fid],
            ];
            if($isActive) {
                array_push($condition, [self::ACTIVE, '=', true]);
                array_push($condition, [self::REMOVED, '=', false]);
            }
            $result = self::where($condition)->get();
        }
        return $result;
    }

    public static function getFans($pid = null) {
        $persons = [];
        if(is_null($pid)) {
            return $persons;
        }
        $relations = self::where(self::PID_NAME, $pid)->get();
        foreach ($relations as $relation) {
            if(!is_null($relation->{self::FID_NAME})) {
                $person = $relation->folder->person;
                array_push($persons, $person);
            }
        }
        return $persons;
    }

    public static function getPublications($fid = null, $skip = 0, $take = null,
                                           $isDesc = false,
                                           $orderBy = self::CREATED_AT) {
        $desc = 'ASC';
        if($isDesc) {
            $desc = 'DESC';
        }
        $publications = null;
        if(is_null($fid)) {
            return $publications;
        }
        $condition = [
            [self::FID_NAME, '=', $fid],
            [self::ACTIVE, '=', true],
            [self::REMOVED, '=', false],
        ];
        if(!is_null($take)) {
            $relations = self::where($condition)->orderBy($orderBy, $desc)
                ->skip($skip)->take($take)->get();
        } else {
            $relations = self::where($condition)->orderBy($orderBy, $desc)->get();
        }
        $pub = null;
        foreach ($relations as $relation) {
            if(!is_null($relation->{self::PID_NAME})) {
                $profile = Publication::getProfile($relation->{self::PID_NAME});
                $publications[] = $profile;
            }
        }
        return $publications;
    }

    public function setData(array $data) {
        if(array_has($data, self::PID_NAME)) {
            $this->{self::PID_NAME} = $data[self::PID_NAME];
        }
        if(array_has($data, self::FID_NAME)) {
            $this->{self::FID_NAME} = $data[self::FID_NAME];
        }
        if(array_has($data, self::ACTIVE))  {
            $this->{self::ACTIVE} = $data[self::ACTIVE];
        } else {
            $this->{self::ACTIVE} = true;
        }
        if(array_has($data, self::REMOVED)) {
            $this->{self::REMOVED} = $data[self::REMOVED];
        } else {
            $this->{self::REMOVED} = false;
        }
    }

    public function isDuplicate() {
        $count = 0;
        if(!is_null($this->{self::PID_NAME}) && !is_null($this->{self::FID_NAME})) {
            $count = self::where([
                [self::PID_NAME, $this->{self::PID_NAME}],
                [self::FID_NAME, $this->{self::FID_NAME}],
                [self::ACTIVE, $this->{self::ACTIVE}],
                [self::REMOVED, $this->{self::REMOVED}],
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
                [self::FID_NAME, $this->{self::FID_NAME}],
                [self::ACTIVE, true],
                [self::REMOVED, false],
            ])->first();
            $this->{self::UPDATED_AT} = $temp->updated_at;
            $this->{self::CREATED_AT} = $temp->created_at;
            $err->setError('Exist');
            return $err;
        }
        if(is_null($this->{self::PID_NAME})
            || is_null($this->{self::FID_NAME})) {
            $err->setError('InvalidData');
            return $err;
        }
        // To check the integrity of database.
        if(!Publication::isExists($this->{self::PID_NAME})
            || !Folder::isExists($this->{self::FID_NAME})) {
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
        return $this->belongsTo(self::$NAME_SPACE.'\Folder', self::FID_NAME);
    }

    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Publication', self::PID_NAME);
    }

    public static function isFan($pid, $uid) {
        $condition = [
            [self::UID, $uid],
            [self::ACTIVE, true],
            [self::REMOVED, false],
        ];
        $folders = Folder::where($condition)->get();
        if(!is_null($folders)) {
            foreach ($folders as $folder) {
                $fanCondition = [
                    [self::PID, $pid],
                    [self::FID, $folder->{self::ID}],
                    [self::ACTIVE, true],
                    [self::REMOVED, false],
                ];
                $fan = self::where($fanCondition)->count();
                if($fan > 0) {
                    return true;
                }
            }
        }
        return false;
    }

}