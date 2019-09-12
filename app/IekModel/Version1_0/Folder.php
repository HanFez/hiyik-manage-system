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
class Folder extends IekModel
{
    
    /**
    * @var  id             INT8
    * @var  updated_at     timestamps
    * @var  created_at     timestamps
    * @var  name           String
    * @var  description    String
    * @var  is_removed     Boolean
    * @var  is_active      Boolean
    * @var  folder_type_id INT8
    * @var  person_id      INT8
    * @var  range_id       INT8
    */

    const PERSON_ID = 'person_id';
    const RANGE_ID = 'range_id';
    const TYPE_ID = 'folder_type_id';
    public $primaryKey = 'id';
    protected $table = 'tblFolders';
    public $fillable = ['name', 'description', 'is_active', 'is_removed', 'person_id', 'range_id', 'folder_type_id'];

    public function folderType() {
        return $this->belongsTo(self::$NAME_SPACE.'\FolderType', self::TYPE_ID, 'id');
    }

    public function range() {
        return $this->belongsTo(self::$NAME_SPACE.'\Range', self::RANGE_ID, 'id');
    }

    public function folderTitle(){
      return $this->belongsTo(self::$NAME_SPACE.'\FolderTitle','id','folder_id');
    }

    public function folderDescription(){
        return $this->belongsTo(self::$NAME_SPACE.'\FolderDescription','id','folder_id');
    }

    public function personFolder(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonFolder','id','folder_id');
    }

    public static function getPersonFolderTotal($uid) {
        if(is_null($uid)) {
            return 0;
        }
        $condition = self::CONDITION;
        return self::where(self::UID, $uid)
            ->where($condition)
            ->count();
    }

    public static function getContentCount($fid) {
        $folder = self::findOrFail($fid);
        $count = 0;
        if(!is_null($folder)) {
            switch ($folder->{self::TYPE_ID}) {
                case FolderType::PUBLICATION:
                    break;
                case FolderType::COLLECT:
                    $count = PublicationFan::getPublicationsCount($fid);
                    break;
                case FolderType::IWALL:
                    break;
            }
        }
        return $count;
    }
}

?>
