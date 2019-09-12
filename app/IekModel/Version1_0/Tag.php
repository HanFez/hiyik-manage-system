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
class Tag extends IekModel
{
    const CUSTOM_ID = 49;
    const CHILD_LEVEL = 1;
    /**
    * @var  id                  INT4
    * @var  name                String
    * @var  description         String
    * @var  parent_id           INT4
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_active           Boolean
    * @var  is_removed          Boolean
    * @var  is_official         Boolean
    */

    //public $incrementing = false;
    public $primaryKey = 'id';
    protected $table = 'tblTags';
    
    protected $fillable = [
        'id','name', 'description', 'is_active', 'is_removed', 'parent_id', 'level', 'is_official'
    ];

    public function publicationTag(){
        return $this->belongsTo(self::$NAME_SPACE.'\PublicationTag',self::ID,self::TAG_ID)
            ->where(IekModel::CONDITION);
    }

    public function personFamiliar(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonFamiliar',self::ID,self::TAG_ID)
            ->where(IekModel::CONDITION);
    }

    public function personFavor(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonFavor',self::ID,self::TAG_ID)
            ->where(IekModel::CONDITION);
    }

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', self::ROW_ID, self::ID);
    }

    public function iwallTag(){
        return $this->belongsTo(self::$NAME_SPACE.'\IwallTag',self::ID,self::TAG_ID)
            ->where(IekModel::CONDITION);
    }

    public static function checkNameRepeat($name,$id){
        $nameRepeat = Tag::where(self::NAME,$name)->get();
        if(!$nameRepeat->isEmpty() && $nameRepeat[0]->id != $id){
            return false;
        }
        return true;
    }

    public static function checkTag($id){
        $tag = self::where(self::ID,$id)
            ->get();
        return $tag->isEmpty() ? false : true;
    }
}

?>
