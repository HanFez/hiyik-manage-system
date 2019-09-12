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
class AnnounceReview extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblAnnounceReviews';

    public function operator() {
        return $this->hasOne(self::$NAME_SPACE.'\Employee', self::ID, self::OID);
    }
}

?>
