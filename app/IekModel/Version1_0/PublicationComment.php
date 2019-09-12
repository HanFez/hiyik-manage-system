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



/**
* @author       Rich
*/
class PublicationComment extends IekModel
{
    
    /**
    * @var  id                  INT8
    * @var  comment_id          INT8
    * @var  publication_id      INT8
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_canceled         Boolean
    * @var  is_active           Boolean
    * @var  is_removed          Boolean
    */
    const PID_NAME = 'publication_id';

    public $primaryKey = 'id';
    protected $table = 'tblPublicationComments';

    public function publication() {
        return $this->belongsTo(self::$NAME_SPACE.'\Publication', 'publication_id');
    }

    public function comment() {
        return $this->belongsTo(self::$NAME_SPACE.'\Comment', 'comment_id');
//            ->where(self::CONDITION)
//            ->orderBy(self::CREATED)
//            ->first();
    }

//    public function person() {
//        return $this->belongsTo('Person', 'person_id');
//    }
	 public static function getCommentCount($pid = null, $begin = null, $end = null, $isActive = true, $isRemoved =false) {
        if(is_null($pid)) {
            return self::count();
        }
        $condition = [
            [self::PID, '=', $pid],
            [self::ACTIVE, '=', $isActive],
            [self::REMOVED, '=', $isRemoved],
        ];
         if(!is_null($begin)) {
             array_push($condition, [self::CREATED_AT, '>=', $begin]);
         }
         if(!is_null($end)) {
             array_push($condition, [self::CREATED_AT, '<', $end]);
         }
        return self::where($condition)->count();
    }
}

?>
