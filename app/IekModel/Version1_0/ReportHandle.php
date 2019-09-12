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
class ReportHandle extends IekModel {

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
    protected $table = 'tblReportHandles';

    public function reportHandle() {
        return $this->belongsTo(self::$NAME_SPACE.'\Report', 'report_id', 'id');
    }

    public function reportOperator() {
        return $this->hasOne(self::$NAME_SPACE.'\Employee', 'id', 'operator_id');
    }

    public function reportReply() {
        return $this->hasOne(self::$NAME_SPACE.'\ManageReply', 'id', 'reply_id');
    }
}

?>
