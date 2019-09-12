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
class Report extends IekModel {
    
    /**
    * @var  id                  INT8
    * @var  person_id           INT4
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  reason              String
    * @var  is_active           Boolean
    * @var  is_removed          Boolean
    * @var  handled             Boolean
    * @var  handler             INT8
    * @var  target_id           INT8
    * @var  target_type         INT4
    * @var  memo                String
    */

    public $primaryKey = 'id';
    protected $table = 'tblReports';

    public function reportHandle() {
        return $this->hasOne(self::$NAME_SPACE.'\ReportHandle', 'report_id', 'id');
    }

    public function reportPublicationTitle() {
        return $this->belongsTo(self::$NAME_SPACE.'\PublicationTitle', 'target_id', 'id');
    }

    public function reportPerson() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'target_id', 'id');
    }

    public function reportComment() {
        return $this->belongsTo(self::$NAME_SPACE.'\Comment', 'target_id', 'id');
    }

    public function reportChatSession() {
        return $this->belongsTo(self::$NAME_SPACE.'\ChatSession', 'target_id', 'id');
    }

    public function reportInformer() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id', 'id');
    }

    public function message(){
        return $this->belongsToMany(self::$NAME_SPACE.'\Message','tblChatSessions','target_id as id','id as message_id');
    }

    public function isExist($id){
        $count = self::where(self::ID,$id)
            ->count();
        return $count == 0 ? false : true;
    }
}

?>
