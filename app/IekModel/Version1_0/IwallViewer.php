<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/20
 * Time: 11:14
 */

namespace App\IekModel\Version1_0;


class IwallViewer extends IekModel
{
    const IID_NAME = 'iwall_id';
    const UID_NAME = 'viewer_id';

    protected $table = 'tblIwallViewers';
    protected $fillable = [self::IID_NAME, self::UID_NAME];


    /** To obtain someone's view record count. If not specify person, then get all.
     * @param null $uid
     * @return mixed
     */
    public static function getViewerCount($uid = null) {
        if(is_null($uid)) {
            return self::count();
        }
        return self::where(self::UID_NAME, '=', $uid)->count();
    }

    /** To obtain someone's view record count in a interval,
     * If not specify person, then get all in a interval.
     * @param $uid
     * @param $begin
     * @param $end
     * @return mixed
     */
    public static function getViewerIntervalCount($uid, $begin, $end) {
        $tempDate = new \DateTime($begin);
        $beginDate = $tempDate->setTime(0, 0, 0);//->getTimestamp();
        $tempDate = new \DateTime($end);
        $endDate = $tempDate->setTime(23, 59, 59);//->getTimestamp();
        if(is_null($uid)) {
            return self::where(self::CREATED_AT, '<', $endDate)
                ->where(self::CREATED_AT, '>=', $beginDate)
                ->count();
        } else {
            return self::where(self::UID_NAME, '=', $uid)
                ->where(self::CREATED_AT, '<', $endDate)
                ->where(self::CREATED_AT, '>=', $beginDate)
                ->count();
        }
    }
    /** Just obtain the count of viewer viewed some publication
     * @param $pid   String    The id of publication.
     * @return mixed integer   The count value.
     */
    public static function getViewCount($iid = null) {
        if(is_null($iid)) {
            return self::count();
        }
        return self::where(self::IID_NAME, '=', $iid)->count();
    }

    public static function getViewIntervalCount($iid, $begin = null, $end = null, $isActive = true, $isRemoved = false) {
        if(is_null($iid)) {
            return 0;
        }
        $condition = [
            ['iwall_id', $iid],
            [self::ACTIVE, '=', $isActive],
            [self::REMOVED, '=', $isRemoved],
        ];
        if(!is_null($begin)) {
            array_push($condition, [self::CREATED_AT, '>=', $begin]);
        }
        if(!is_null($end)) {
            array_push($condition, [self::CREATED_AT, '<', $end]);
        }
        return self::where($condition)
            ->count();
    }
    /** To get person list that viewed publication.
     * @param null $pid
     * @return array
     */
    public static function getViewers($iid = null) {
        $persons = [];
        if(is_null($iid)) {
            return $persons;
        }
        $relations = self::where(self::IID_NAME, $iid)->get();
        foreach ($relations as $relation) {
            if(!is_null($relation->{self::UID_NAME})) {
                $person = $relation->viewer;
                array_push($persons, $person);
            }
        }
        return $persons;
    }

    /** To set model attributes.
     * @param array $attributes
     */
    public function setData(array $attributes) {
        if(array_has($attributes, self::IID_NAME)) {
            $this->{self::IID_NAME} = $attributes[self::IID_NAME];
        }
        if(array_has($attributes, self::UID_NAME)) {
            $this->{self::UID_NAME} = $attributes[self::UID_NAME];
        }
        if(array_has($attributes, self::ACTIVE)) {
            $this->{self::ACTIVE} = $attributes[self::ACTIVE];
        } else {
            $this->{self::ACTIVE} = true;
        }
        if(array_has($attributes, self::REMOVED)) {
            $this->{self::REMOVED} = $attributes[self::REMOVED];
        } else {
            $this->{self::REMOVED} = false;
        }
    }

    /** Save the record into database, if exists, not save.
     * @return bool
     */
    public function saveRecord() {
        if($this->isDuplicate()) {
            return false;
        }
        if(is_null($this->{self::IID_NAME})) {
            return false;
        }
        return $this->save();
    }

    /** Check existence of record.
     * @return bool
     */
    public function isDuplicate() {
        $count = 0;
        if(!is_null($this->{self::IID_NAME}) && !is_null($this->{self::UID_NAME})) {
            $count = self::where([
                [self::IID_NAME, $this->{self::IID_NAME}],
                [self::UID_NAME, $this->{self::UID_NAME}],
            ])->count();
        }
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /** Check the existence of publication according to publication id.
     *  To maintain the integrity of database.
     * @return bool
     */
    public function isIwallExists() {
        $iwall = $this->iwall;
        if(is_null($iwall)) {
            return true;
        }
        return false;
    }

    /** Check the existence of person according to person id.
     *  To maintain the integrity of database.
     * @return bool
     */
    public function isPersonExists() {
        $person = $this->person;
        if(is_null($person)) {
            return true;
        }
        return false;
    }

    public function iwall() {
        return $this->belongsTo(self::$NAME_SPACE.'\Iwall', self::IID_NAME, 'id');
    }

    public function viewer() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', self::UID_NAME, 'id');
    }

}