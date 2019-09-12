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
class SearchWord extends IekModel {

    /**
     * @var  address_id   INT4
     * @var  city_id      INT4
     * @var  address      String
     * @var  created_at   timestamps
     * @var  updated_at   timestamps
     * @var  is_actived   bool
     * @var  is_removed   bool
     */
    // public $timestamps = false;
    // public $incrementing = false;
    public $primaryKey = 'id';
    protected $table = 'tblSearchWords';
    protected $fillable = ['id', 'word', 'hits','is_active', 'is_removed', 'updated_at', 'created_at'];

    public static function getByWord($word) {
        if(is_null($word)) {
            return null;
        }
        $row = self::where(self::WORD, $word)
            ->where(self::ACTIVE, true)
            ->where(self::REMOVED, false)
            ->first();
        return $row;
    }

}

?>
