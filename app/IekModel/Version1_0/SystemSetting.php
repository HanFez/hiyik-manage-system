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
class SystemSetting extends IekModel
{
    const CACHE_KEY = [
        'category',
        'interval',
        'sort',
        'categorySort',
        'nickLimit',
        'passwordLimit',
        'titleLimit',
        'descriptionLimit',
        'commentLimit',
        'personTagsLimit',
        'personCustomTagsLimit',
        'publicationTagsLimit',
        'publicationCustomTagsLimit',
        'customItemLimit',
    ];
    /**
     * @var  id                  INT8
     * @var  name                String
     * @var  description         String
     * @var  is_active           Boolean
     * @var  is_removed          Boolean
     * @var  created_at          timestamps
     * @var  updated_at          timestamps
     * @var  content             String : jsonb
     */

    public $primaryKey = 'id';
    protected $table = 'tblSystemSettings';

    public static function isExist($sid){
        $count = self::where(self::ID,$sid)
            ->count();
        return $count == 0 ? false : true;
    }

    public static function checkSystemSettingName($name,$id){
        $nameCheck = self::where(self::NAME,$name)->get();
        if(!$nameCheck->isEmpty() && $nameCheck[0]->id != $id){
            return false;
        }
        return true;
    }

}

?>
