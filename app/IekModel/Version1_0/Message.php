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
class Message extends IekModel
{
    const CODE_LENGTH = 6;
    const MSG_UNKNOWN = 0;
    const MSG_TEXT = 1;
    const MSG_IMAGE = 2;
    const MSG_VOICE = 3;
    const MSG_VIDEO = 4;
    const MSG_FILE = 5;
    const MSG_LINK = 6;
    /**
    * @var  id           INT8
    * @var  message_type INT4
    * @var  created_at   timestamps
    * @var  updated_at   timestamps
    * @var  content      String
    * @var  is_active    Boolean
    * @var  is_removed   Boolean
    * @var  hash         String
    */

    public $primaryKey = 'id';
    protected $table = 'tblMessages';
    protected $guarded = [];

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }

    public static function insertMessage($content, $content_type=1, $hashType=null, $hashCode=null) {
        if(is_null($hashType)) {
            $type = self::HASH;
        } else {
            $type = $hashType;
        }
        if(is_null($hashCode)) {
            $hash = hash(self::HASH, $content);
        } else {
            $hash = $hashCode;
        }
        if(!is_null($content_type)) {
            if($content_type > self::MSG_LINK || $content_type < self::MSG_UNKNOWN) {
                $msgType = self::MSG_UNKNOWN;
            } else {
                $msgType = $content_type;
            }
        } else {
            $msgType = self::MSG_TEXT;
        }
        $message = self::firstOrCreate([
            'content' => $content,
            'content_type' => $msgType,
            'hash' => $hash,
            'hash_type' => $type]);
        return $message;
    }
}

?>
