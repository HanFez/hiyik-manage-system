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
class Announce extends IekModel
{
    
    public $primaryKey = 'id';
    protected $table = 'tblAnnounces';

    public function announceReview() {
        return $this->hasOne(self::$NAME_SPACE.'\AnnounceReview', self::ANNOUNCE_ID, self::ID);
    }

    public function operator() {
        return $this->hasOne(self::$NAME_SPACE.'\Employee', self::ID, self::OID);
    }

    public static function isExist($aid){
        $count = self::where(self::ID,$aid)
            ->count();
        return $count == 0 ? false : true;
    }

}

?>
