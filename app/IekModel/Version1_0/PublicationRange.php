<?php

namespace App\IekModel\Version1_0;


class PublicationRange extends IekModel {

    protected $table = "tblPublicationRanges";
    protected $fillable = [
        self::ACTIVE, self::REMOVED, self::PID, self::RID,
    ];
    
    public function range() {
        return $this->belongsTo(self::$NAME_SPACE.'\Range', self::RID, self::ID);
    }
    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Publication', self::PID, self::ID);
    }
    public static function insertRow($params) {
        $oldId = property_exists($params,'oldId')?$params->oldId:null;
        $newId = $params->newId;
        $row = null;
        if(is_null($oldId)) {
            $row = self::queryRow($params->pid, $newId);
            if(!is_null($row)) {
                $row->save();
            } else {
                $row = self::createRow($params);
            }
        } else {
            if($oldId == $newId) {
                $row = self::queryRow($params->pid, $newId);
                if(!is_null($row)) {
                    $row->save();
                } else {
                    $row = self::createRow($params);
                }
            } else {
                $row = self::queryRow($params->pid, $oldId);
                if(!is_null($row)) {
                    $row->{self::ACTIVE} = false;
                    $row->save();
                }
                $row = self::createRow($params);
            }
        }
        return $row;
    }
    public static function createRow($params) {
        $rec = new self;
        $rec->{self::PID} = $params->pid;
        $rec->{self::RID} = $params->newId;
        $rec->{self::ACTIVE} = true;
        $rec->{self::REMOVED} = false;
        $rec->save();
        return $rec;
    }

    public static function queryRow($pid, $contentId) {
        $condition = [
            [self::ACTIVE, '=' ,true],
            [self::REMOVED, '=', false],
            [self::PID, '=', $pid],
            [self::RID, '=', $contentId],
        ];
        $row = self::where($condition)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        return $row;
    }

    public static function getIds($pid) {
        $condition = [
            [self::ACTIVE, '=' ,true],
            [self::REMOVED, '=', false],
            [self::PID, '=', $pid],
        ];
        $row = self::where($condition)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        if(!is_null($row)) {
            return $row->{self::RID};
        }
        return null;
    }
    
    public static function isVisible($pid, $uid) {
        $condition = [
            [self::ACTIVE, '=' ,true],
            [self::REMOVED, '=', false],
            [self::PID, '=', $pid],
        ];
        $relation = self::where($condition)->first();
        if(is_null($relation)) {
            return true;
        }
        $range = $relation->range;
        if(!$range->{self::ACTIVE} || $range->{self::REMOVED}) {
            return true;
        }
        $status = false;
        $ids = PublicationPerson::getPersonIds($pid);
        switch($range->id) {
            case Range::SELF:
                $status = PublicationPerson::isOwner($pid, $uid);
                break;
            case Range::FAN:

                if(is_null($ids)) {
                    $status = false;
                } else {
                    foreach ($ids as $id) {
                        if(PersonFan::isFan($id, $uid)) {
                            $status = true;
                            break;
                        }
                    }
                }
                break;
            case Range::FRIEND:
                foreach ($ids as $id) {
                    if(PersonFan::isFan($id, $uid) && PersonFan::isFan($uid, $id)) {
                        $status = true;
                        break;
                    }
                }
                break;
            case Range::SPECIFY:
                //TODO
                break;
            default:
                $status = true;
                break;
        }
        return $status;
    }
    public static function deleteRows($pid) {
        $condition = self::CONDITION;
        $condition[] = [self::PID, '=', $pid];
        self::where($condition)->update([self::ACTIVE => false]);
    }

    public static function updateRow($params) {
        $oldId = property_exists($params,'oldId')?$params->oldId:null;
        $newId = $params->newId;
        $row = self::queryRow($params->pid, $newId);
        if(!is_null($row)) {
            $row->save();
        }
        return $row;
    }

    public static function finalSave($params) {
        //self::deleteRows($params->pid);
        $row = self::insertRow($params);
        return $row;
    }

    public static function changeOrder($params) {
        self::updateRow($params);
    }
}
