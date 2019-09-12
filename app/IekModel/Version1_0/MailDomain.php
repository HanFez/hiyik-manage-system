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
class MailDomain extends IekModel {


    /**
     * @var  mail_id      String
     * @var  person_id    INT4
     * @var  domain_id    INT4
     * @var  created_at   timestamps
     * @var  updated_at   timestamps
     * @var  is_active    Boolean
     * @var  is_removed   Boolean
     */
    protected $table = 'tblMailDomains';
    protected $guarded = [];

    public function mail() {
        return $this->belongsTo(self::$NAME_SPACE.'\Mail', 'mail_id', self::ID);
    }

    public function domain() {
        return $this->belongsTo(self::$NAME_SPACE.'\Domain', 'domain_id', self::ID);
    }

    public function bindPerson() {
        return $this->hasOne(self::$NAME_SPACE.'\PersonMail', 'mail_domain_id', self::ID)
            ->where(self::CONDITION)
            ->where('is_bind', true)
            ->orderBy(self::CREATED, 'desc');
    }
    public function persons() {
        return $this->hasMany(self::$NAME_SPACE.'\PersonMail', 'mail_domain_id', self::ID)
            ->where(self::CONDITION)
            ->orderBy(self::CREATED, 'desc');
    }
    static public function hasExist($mailId, $domainId) {

        $mail = self::where(self::CONDITION)
            ->where('mail_id', $mailId)
            ->where('domain_id', $domainId)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        return $mail;
    }

}

?>
