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
class Domain extends IekModel
{
    const DOMAINS = [
        'qq.com',
        'sina.com.cn',
        'hotmail.com',
        '163.com',
        '263.com',
    ];

    /**
    * @var  domain_id      INT4
    * @var  updated_at     timestamps
    * @var  created_at     timestamps
    * @var  domain         String
    */

    // public $timestamps = false;
    //public $incrementing = false;
    
    protected $table = 'tblDomains';
    protected $guarded = [];

    public static function hasExist($domain) {
        $domain = self::where(self::CONDITION)
            ->where('domain', $domain)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        return $domain;
    }

    public function maiDomain() {
        return $this->hasMany(self::$NAME_SPACE.'\MailDomain', 'domain_id', self::ID)
            ->where(self::CONDITION);
    }
}

?>
