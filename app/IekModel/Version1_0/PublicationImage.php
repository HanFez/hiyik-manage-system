<?php

namespace App\IekModel\Version1_0;


class PublicationImage extends IekModel {

    protected $table = "tblPublicationImages";
//    protected $fillable = [
//        'image_id','publication_id','is_active','is_removed', 'can_sell', 'is_cover'
//    ];

    public function image() {
        return $this->belongsTo(self::$NAME_SPACE.'\Images', self::IID, 'id');
    }

    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Publication', self::PID, 'id');
    }

    public function imageTitle() {
        return $this->belongsTo(self::$NAME_SPACE.'\PlainStyle', self::TID, 'id');
    }

    public static function insertRow($params) {
        $oldId = property_exists($params,'oldId')?$params->oldId:null;
        $newId = $params->newId;
        $index = property_exists($params,'index')?$params->index:null;
        $row = null;
        if(is_null($oldId)) {
            $row = self::createRow($params);
        } else {
            $row = self::queryRow($params->pid, $oldId, $params->canSell, $params->titleId, $index);
            if(!is_null($row)) {
                $row->{self::ACTIVE} = false;
                $row->save();
            } else {
                $row = self::createRow($params);
            }
        }

        return $row;
    }
    public static function createRow($params) {
        $rec = new self;
        $rec->{self::PID} = $params->pid;
        $rec->{self::IID} = $params->newId;
        $rec->{self::INDEX} = $params->index;
        $rec->{self::TID} = $params->titleId;
        $rec->{self::CAN_SELL} = $params->canSell;
        $rec->{self::ACTIVE} = true;
        $rec->{self::REMOVED} = false;
        $rec->save();
        return $rec;
    }

    public static function queryRow($pid, $contentId, $canSell, $titleId, $index) {
        $condition = [
            [self::ACTIVE, '=' ,true],
            [self::REMOVED, '=', false],
            [self::PID, '=', $pid],
            [self::IID, '=', $contentId],
            [self::CAN_SELL, '=', $canSell],
        ];
        $query = self::where($condition);
        if(is_null($titleId)) {
            $query = $query->whereNull(self::TID);
        } else {
            $query = $query->where(self::TID, $titleId);
        }
        if(is_null($index)) {
            $query = $query->whereNull(self::INDEX);
        } else {
            $query = $query->where(self::INDEX, $index);
        }
        $row = $query->orderBy(self::CREATED, 'desc')
            ->first();
        return $row;
    }

    public static function getIds($pid) {
        $ids = [];
        $condition = [
            [self::ACTIVE, '=' ,true],
            [self::REMOVED, '=', false],
            [self::PID, '=', $pid],
        ];
        $ids = self::where($condition)->get()->pluck(self::ID);
        return $ids;
    }

    public static function getContent($pid) {

    }

    public static function getSellImageId($pid) {
        $relation = self::where(self::CONDITION)
            ->where(self::PID, $pid)
            ->where(self::CAN_SELL, true)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        $imageId = null;
        if(!is_null($relation)) {
            $imageId = $relation->image_id;
        }
        return $imageId;
    }
    public static function deleteRows($pid) {
        $condition = self::CONDITION;
        $condition[] = [self::PID, '=', $pid];
        self::where($condition)->update([self::ACTIVE => false]);
    }

    public static function updateRow($params) {
        $relationId = property_exists($params,'relationId')?$params->relationId:null;
        $newId = $params->newId;
        $row = self::where(self::CONDITION)
            ->where(self::ID, $relationId)
            ->first();
        if(!is_null($row)) {
            if(!is_null($newId)) {
                $row->{self::IID} = $newId;
            }
            if(property_exists($params, self::INDEX)) {
                $row->{self::INDEX} = $params->index;
            }
            if(property_exists($params, 'canSell')) {
                $row->{self::CAN_SELL} = $params->canSell;
            }
            if(property_exists($params, 'titleId')) {
                $row->{self::TID} = $params->titleId;
            }
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
        $row = self::updateRow($params);
        return $row;
    }
}
