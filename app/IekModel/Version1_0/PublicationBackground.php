<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 16-8-24
 * Time: 下午9:39
 */

namespace App\IekModel\Version1_0;


class PublicationBackground extends IekModel {
    protected $table = 'tblPublicationBackgrounds';

    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Publication', self::PID, self::ID);
    }

    public function background() {
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
        $rec->{self::ACTIVE} = true;
        $rec->{self::REMOVED} = false;
        $rec->save();
        return $rec;
    }

    public static function deleteRows($pid) {
        $condition = self::CONDITION;
        $condition[] = [self::PID, '=', $pid];
        self::where($condition)->update([self::ACTIVE => false]);
    }

    public static function updateRow($params) {
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
    public static function queryRow($pid, $contentId = null) {
        $condition = [
            [self::ACTIVE, '=' ,true],
            [self::REMOVED, '=', false],
            [self::PID, '=', $pid],

        ];
        if(!is_null($contentId)) {
            array_push($condition, [self::CONTENT_ID, '=', $contentId]);
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
            return $relation->{self::CONTENT_ID};
        }
        return null;
    }

    public static function getContent($pid) {
        $rows = self::where(self::CONDITION)
            ->where(self::PID, $pid)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        if(!is_null($rows)) {
            if(!is_null($rows->{self::CONTENT_ID})) {
                $content = Description::where(self::CONDITION)
                    ->where(self::ID, $rows->{self::CONTENT_ID})
                    ->orderBy(self::CREATED, 'desc')
                    ->first();
                //unset($rows->{self::CONTENT_ID});
                $rows->{self::CONTENT} = $content;
            }
        }
        return $rows;
    }
}