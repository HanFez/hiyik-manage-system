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

use App\Events\TagHitEvent;
use App\IekModel\EventArguments\EventArguments;
/**
* @author       Rich
*/
class PublicationTag extends IekModel
{

    /**
    * @var  tag_id              INT4
    * @var  publication_id      INT4
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_canceled         Boolean
    */

    protected $table = 'tblPublicationTags';
    protected $fillable=[
        self::ACTIVE, self::REMOVED, self::TAG_ID, self::PID,
    ];

    public function tag() {
        return $this->belongsTo(self::$NAME_SPACE.'\Tag', 'tag_id', 'id');
    }

    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Publication', self::PID, 'id');
    }

    public static function insertRow($params) {
        $newId = $params->newId;
        $row = null;

        $row = self::queryRow($params->pid, $newId);
        if(!is_null($row)) {
            //$row->{self::ACTIVE} = false;
            $row->save();
        } else {
            $row = self::createRow($params);
        }
        return $row;
    }
    public static function createRow($params) {
        $rec = new self;
        $rec->{self::PID} = $params->pid;
        $rec->{self::TAG_ID} = $params->newId;
        $rec->{self::ACTIVE} = true;
        $rec->{self::REMOVED} = false;
        $rec->save();
        $args = new EventArguments(null, [$params->newId]);
        event(new TagHitEvent($args));

        return $rec;
    }

    public static function queryRow($pid, $contentId) {
        $condition = [
            [self::ACTIVE, '=' ,true],
            [self::REMOVED, '=', false],
            [self::PID, '=', $pid],
            [self::TAG_ID, '=', $contentId],
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
        $rows = self::where($condition)
            ->orderBy(self::CREATED, 'desc')
            ->get();
        if(!is_null($rows)) {
            foreach ($rows as $row) {
                array_push($ids, $row->{self::TAG_ID});
            }
        }
        return $ids;
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
        $row = self::updateRow($params);
        return $row;
    }
}

?>
