<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 16-8-24
 * Time: 下午9:36
 */

namespace App\IekModel\Version1_0;



class PublicationCover extends IekModel {

    protected $table = 'tblPublicationCovers';

    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Publication', self::PID, self::ID);
    }

    public function cover() {
        return $this->belongsTo(self::$NAME_SPACE.'\Images', self::IID, self::ID);
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
        $rec->{self::IID} = $params->newId;
        $rec->{self::ACTIVE} = true;
        $rec->{self::REMOVED} = false;
        $rec->save();
        return $rec;
    }

    public static function queryRow($pid, $contentId = null) {
        $condition = [
            [self::ACTIVE, '=' ,true],
            [self::REMOVED, '=', false],
            [self::PID, '=', $pid],

        ];
        if(!is_null($contentId)) {
            array_push($condition,[self::IID, '=', $contentId]);
        }
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
        $relation = self::where($condition)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        if(!is_null($relation)) {
            return $relation->{self::IID};
        }
        return null;
    }
    public static function getCoverImageId($pid) {
        $relation = self::where(self::CONDITION)
            ->where(self::PID, $pid)
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