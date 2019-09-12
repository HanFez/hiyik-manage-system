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
class Notify extends IekModel
{
    
    /**
     * @var  id                  INT8
     * @var  created_at          timestamps
     * @var  updated_at          timestamps
     * @var  action              String
     * @var  from_id             INT8
     * @var  to_id               INT8
     * @var  target_type         String
     * @var  target_id           INT8
     * @var  is_read             Boolean
     * @var  is_active           Boolean
     * @var  is_removed          Boolean
    */

    // public $timestamps = false;
    // public $incrementing = false;
    public $primaryKey = 'id';
    //protected $table = 'tblNotifies';

    public static function makeNotify($params,$model) {
        if(is_null($params->action)
            || is_null($params->fromId)
            || is_null($params->targetId)) {
            return;
        }
        $notify = new $model();
        $notify->action = $params->action;
        $notify->lang = $params->lang;
        $notify->from_id = $params->fromId;
        $notify->to_id = $params->toId;
        $notify->target_id = $params->targetId;
        if(property_exists($params, 'originId')) {
            $notify->origin_id = $params->originId;
        }
        $result = $notify->save();
        if($result) {
            return $notify;
        } else {
            return null;
        }
    }
}

?>
