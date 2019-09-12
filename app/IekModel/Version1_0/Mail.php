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
class Mail extends IekModel {

    /**
     * @var  id           INT8
     * @var  mail         String
     * @var  created_at   timestamps
     * @var  updated_at   timestamps
     * @var  is_active    Boolean
     * @var  is_removed   Boolean
     */
    protected $table = 'tblMails';
    protected $guarded = [];

    public function mailDomain() {
        return $this->hasMany(self::$NAME_SPACE.'\MailDomain', 'mail_id', self::ID)
            ->where(self::CONDITION);
    }

    public static function hasExist($mail) {
        $mail = self::where(self::CONDITION)
            ->where('mail', $mail)
            ->orderBy(self::CREATED, 'desc')
            ->first();

        return $mail;
    }

}

?>
