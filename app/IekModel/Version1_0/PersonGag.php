<?php
//
// +------------------------------------------------------------------------+
// | PHP Version 5                                                          |
// +------------------------------------------------------------------------+
// | Copyright (c) All rights reserved.                                     |
// +------------------------------------------------------------------------+
// | File:                                                                  |
// +------------------------------------------------------------------------+
// | Author:                                                                |
// +------------------------------------------------------------------------+
//
// $Id$
//

namespace App\IekModel\Version1_0;
use Carbon\Carbon;
use App\IekModel\Version1_0\Constants\Gag;
use League\Flysystem\Exception;


/**
* @author       Rich
*/
class PersonGag extends IekModel
{
    
    /**
    * @var  person_id           INT4
    * @var  address_id          INT4
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_actived          String
    * @var  is_removed          String
    */

    public $primaryKey = 'id';
    protected $table = 'tblPersonGags';
    protected $fillable = ['person_id', 'is_active', 'is_removed', 'updated_at', 'created_at','id'];
    protected $hidden = [];

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id', 'id');
    }

    public function manageLogs(){
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs','row_id','id');
    }

    public static function getGags($personId, $isAll=false) {
        $gags = null;
        try {
            $query = PersonGag::where(self::UID, $personId);
            if(!$isAll) {
                $query = $query->where(self::CONDITION);
            }
            $gags = $query->get();
            if($gags->isEmpty()) {
                return null;
            } else {
                return $gags;
            }
        } catch (\Exception $ex) {
            return null;
        }
    }

    public static function isGagExpired($personId) {
        $gags = static::getGags($personId);
        if(is_null($gags)) {
            return true;
        }
        foreach ($gags as $gag) {
            if($gag->is_forever) {
                return false;
            }
            $begin = Carbon::createFromFormat(PersonGag::DATETIME_FORMAT, $gag->begin_at);
            $interval = $gag->expired;
            $end = $begin->addMinute($interval);
            $current = Carbon::createFromTimestampUTC(time());
            if($end->gte($current)) {
                return false;
            } else {
                return true;
            }
        }
        return true;
    }
    public function isExpired() {
        if($this->is_forever) {
            return false;
        }
        $begin = Carbon::createFromFormat(self::DATETIME_FORMAT, $this->begin_at);
        $interval = $this->expired;
        $end = $begin->addMinute($interval);
        $current = Carbon::createFromTimestampUTC(time());
        if($end->gte($current)) {
            return false;
        } else {
            return true;
        }
    }
    public static function checkGaged($uid, $type = Gag::ALL) {
        $gag = self::getGag($uid, $type);
        if(is_null($gag)) {
            return false;
        }
        if($gag->is_forever) {
            return $gag;
        }
        if($gag->getInterval() > 0) {
            return $gag->getGagInfo();
        } else {
            return false;
        }
    }

    public static function isGatForever($uid) {
        $gag = self::getGag($uid);
        if(is_null($gag)) {
            return false;
        }
        if($gag->is_forever) {
            return true;
        } else {
            return false;
        }
    }

    public function getInterval() {
        $currTime = time();
        $beginAt = $this->begin_at;
        $interval = $this->expired;
        $begin = Carbon::createFromFormat(self::DATETIME_FORMAT, $beginAt);
        if($begin->timestamp + $interval * 60 < $currTime) {
            return 0;
        } else {
            return (int)(ceil(($begin->timestamp + ($interval * 60) - $currTime) / 60));
        }
    }

    public static function getGagMinutes($uid) {
        $gag = self::getGag($uid);
        if(is_null($gag)) {
            return 0;
        }
        return $gag->getInterval();
    }

    public function getGagInfo() {
        $gag = clone $this;
        $minutes = $gag->getInterval();
        unset($gag->begin_at);
        unset($gag->expired);
        $gag->minutes = $minutes;
        return $gag;
    }

    public static function getGag($uid, $type = Gag::ALL) {
        $gagType = $type;
        if($type != Gag::ALL) {
            $gagType = Gag::ALL;
        }
        $gag = self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->where(self::TYPE, $gagType)
            ->orderBy(self::UPDATED, 'desc')
            ->first();

        return $gag;
    }
    public static function getPersonGagInfo($uid, $type = Gag::ALL) {
        $gag = self::getGag($uid, $type);
        if(!is_null($gag)) {
            return $gag->getGagInfo();
        } else {
            return null;
        }
    }
}

?>
