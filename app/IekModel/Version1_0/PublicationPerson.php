<?php

namespace App\IekModel\Version1_0;

use App\IekModel\Version1_0\IekModel;
use Illuminate\Support\Facades\DB;

class PublicationPerson extends IekModel {
    use TraitPersonPublication;
    protected $table = "tblPublicationPersons";

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id', 'id');
    }

    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Publication', 'publication_id', 'id');
    }

    public static function isOwner($pid, $uid) {
        $relation = self::where(self::PID, $pid)
            ->where(self::UID, $uid)
            ->where(self::ACTIVE, true)
            ->where(self::REMOVED, false)
            ->count();
        if(!is_null($relation) && isset($relation) && $relation > 0) {
            return true;
        }
        return false;
    }

    public static function getPersonIds($pid) {
        $condition = [
            [self::PID, '=', $pid],
            [self::ACTIVE, '=', true],
            [self::REMOVED, '=', false],
        ];
        $ids = self::where($condition)->get()->pluck(self::UID);
        return $ids;
    }

    public static function getPersons($pid) {
        $persons = null;
        $ids = self::getPersonIds($pid);
        if(!is_null($ids) && !empty($ids)) {
            $persons = array();
            foreach ($ids as $id) {
                $person = Person::personProfile($id);
                $persons[] = $person;
            }
        }
        return $persons;
    }
    public static function getPersonPublications($uid, $skip, $take, $order=null, $includeDraft=false) {
        if(is_null($uid)) {
            return null;
        }
        $ppTable = self::getDataTable();
        $pTable = Publication::getDataTable();
        //$condition = self::CONDITION;
        $condition = [
            [$ppTable.'.'.self::UID, '=', $uid],
            [$ppTable.'.'.self::ACTIVE, '=', true],
            [$ppTable.'.'.self::REMOVED, '=', false],
            [$pTable.'.'.self::ACTIVE, '=', true],
            [$pTable.'.'.self::REMOVED, '=', false],
        ];
        if(!$includeDraft) {
            $condition[] = [$pTable.'.'.self::PUBLISH, '=', true];
        }
        $relations = self::select($ppTable.'.'.self::PID/*,$ppTable.'.'.self::CREATED*/)
            ->join($pTable,
                $ppTable.'.'.self::PID,
                '=',
                $pTable.'.'.self::ID)
            ->where($condition)
//            ->orderBy(self::CREATED, 'desc')
//            ->skip($skip)
//            ->take($take)
            ->get();
        $pubIds = null;
        if(!is_null($relations)) {
            foreach ($relations as $relation) {
                $pub = $relation->{self::PID};
                $pubIds[] = $pub;
            }
        }
        if(!is_null($order)) {
            $pubIds = Publication::orderPersonPublications($pubIds, $order);
        }
        $pubs = null;
        if(!is_null($pubIds)) {
            $limit = $skip + $take;
            if($limit > count($pubIds)) {
                $limit = count($pubIds);
            }
            for ($inx = $skip; $inx < $limit; $inx++) {
                $pub = Publication::getProfile($pubIds[$inx]);
                $pubs[] = $pub;
            }
        }
        return $pubs;
    }

    public static function getPersonPublicationCount($uid, $order=null, $includeDraft=false) {
        $total = 0;
        if(is_null($uid)) {
            return $total;
        }
        $ppTable = self::getDataTable();
        $pTable = Publication::getDataTable();
        $joinColumn = self::ID;
        $condition = [
            [$ppTable.'.'.self::UID, '=', $uid],
            [$ppTable.'.'.self::ACTIVE, '=', true],
            [$ppTable.'.'.self::REMOVED, '=', false],
//            [$pTable.'.'.self::ACTIVE, '=', true],
//            [$pTable.'.'.self::REMOVED, '=', false],
        ];
        if(!is_null($order) && $order==Publication::ORDER_OFFICIAL) {
            $joinColumn = self::PID;
            $joinTable = PublicationOfficial::getDataTable();
            $pTable = $joinTable;
        } else {
            if (!$includeDraft) {
                $condition[] = [$pTable . '.' . self::PUBLISH, '=', true];
            }
        }
        $condition[] = [$pTable.'.'.self::ACTIVE, '=', true];
        $condition[] = [$pTable.'.'.self::REMOVED, '=', false];
        $total = self::select($ppTable.'.'.self::PID)
            ->join($pTable,
                $ppTable.'.'.self::PID,
                '=',
                $pTable.'.'.$joinColumn)
            ->where($condition)
            ->count();
        return $total;
    }

    public static function getPersonPublicationTotal($model, $uid) {
        $total = 0;
        $ids = implode(',', [$uid]);
        $query = self::getPersonOrderQuery($model, $ids);
        $tempOrdered = DB::select(DB::raw($query));
        if(!is_null($tempOrdered) && !empty($tempOrdered)) {
            $total = $tempOrdered[0]->total;
        }
        return $total;
    }
}
