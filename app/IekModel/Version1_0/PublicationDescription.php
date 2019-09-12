<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\IekModel\Version1_0;


class PublicationDescription extends IekModel{
    protected $table = "tblPublicationDescriptions";
    protected $fillable = [
        self::ACTIVE, self::REMOVED,
        self::UPDATED_AT, self::CREATED_AT,
        self::ID, self::CONTENT_ID, self::PID
    ];

    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Publication', self::PID, self::ID);
    }

    public function description() {
        return $this->belongsTo(self::$NAME_SPACE.'\PlainStyle', self::CONTENT_ID, self::ID);
    }
    public static function insertRow($params) {
        $row = self::createRow($params);
        return $row;
    }
    public static function createRow($params) {
        $rec = new self;
        $rec->{self::PID} = $params->pid;
        $rec->{self::CONTENT_ID} = $params->newId;
        $rec->{self::INDEX} = $params->index;
        $rec->{self::ACTIVE} = true;
        $rec->{self::REMOVED} = false;
        $rec->save();
        return $rec;
    }

    public static function queryRow($pid, $contentId, $index) {
        $condition = [
            [self::ACTIVE, '=' ,true],
            [self::REMOVED, '=', false],
            [self::PID, '=', $pid],
            [self::CONTENT_ID, '=', $contentId],
            [self::INDEX, '=', $index],
        ];
        $row = self::where($condition)
            ->orderBy(self::CREATED, 'desc')
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
        $rows = self::where($condition)->get();
        if(!is_null($rows)) {
            foreach ($rows as $row) {
                array_push($ids, $row->id);
            }
        }
        return $ids;
    }

    public static function getContent($pid) {
        $rows = self::where(self::CONDITION)
            ->where(self::PID, $pid)->get();
        if(!is_null($rows) && !empty($rows) && !$rows->isEmpty()) {
            foreach ($rows as $row) {
                if(!is_null($row->{self::CONTENT_ID})) {
                    $content = Description::where(self::CONDITION)
                        ->where(self::ID, $row->{self::CONTENT_ID})
                        ->orderBy(self::CREATED, 'desc')
                        ->first();
                    $forbidden = DescriptionForbidden::checkForbidden($row->{self::CONTENT_ID});
                    //unset($row->{self::CONTENT_ID});
                    $row->{self::CONTENT} = $content;
                    $row->{self::FORBIDDEN} = $forbidden;
                }
            }
            return $rows;
        } else {
            return null;
        }
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
                $row->{self::CONTENT_ID} = $newId;
            }
            if(property_exists($params, self::INDEX)) {
                $row->{self::INDEX} = $params->index;
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