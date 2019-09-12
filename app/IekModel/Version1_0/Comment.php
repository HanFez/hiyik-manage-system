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
class Comment extends IekModel
{
    

    // public $timestamps = false;
    // public $incrementing = false;
    public $primaryKey = 'id';
    protected $table = 'tblComments';

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'author');
    }

    public function publicationComment() {
        return $this->hasOne(self::$NAME_SPACE.'\PublicationComment', 'comment_id');
    }

    public function commentComment() {
        return $this->hasOne(self::$NAME_SPACE.'\CommentComment', 'comment_id');
    }

    public function commentLikes() {
        return $this->hasMany(self::$NAME_SPACE.'\CommentLike', 'comment_id')->where(self::CONDITION);
    }

    public function commentForbidden() {
        return $this->hasOne(self::$NAME_SPACE.'\CommentForbidden', 'comment_id');
    }

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }

}

?>
