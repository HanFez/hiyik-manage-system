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
class CommentComment extends IekModel
{
    
    /**
    * @var  id                  INT8
    * @var  comment_id          INT8
    * @var  target_comment_id   INT8
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_active           Boolean
    * @var  is_removed          Boolean
    */

    public $primaryKey = 'id';
    protected $table = 'tblCommentComments';

    public function targetComment() {
        return $this->belongsTo(self::$NAME_SPACE.'\Comment', 'target_comment_id');
    }

    public function comment() {
        return $this->belongsTo(self::$NAME_SPACE.'\Comment', 'comment_id');
    }

}

?>
