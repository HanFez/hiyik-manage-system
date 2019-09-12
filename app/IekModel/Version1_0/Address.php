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
class Address extends IekModel {

    public $primaryKey = 'id';
    protected $table = 'tblAddresses';
    protected $guarded = [];
    protected $hidden = ['is_active', 'is_removed', 'updated_at', 'created_at'];

    public function city() {
        return $this->belongsTo(self::$NAME_SPACE.'\City', self::CITY_ID, self::ID);
    }
}

?>
