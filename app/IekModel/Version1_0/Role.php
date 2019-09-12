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
class Role extends IekModel
{
    /**
    * @var  role_id             INT4
    * @var  name                String
    * @var  description         String
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_actived          Boolean
    * @var  is_removed          Boolean
    * @var  memo                String
    */

    public $primaryKey = 'id';
    protected $table = 'tblRoles';
    protected $guarded = [];

    public function isExist($id){
        $count = self::where(self::ID,$id)
            ->count();
        return $count == 0 ? false : true;
    }

    public function rolePrivilege(){
        return $this->hasMany(self::$NAME_SPACE.'\RolePrivilege',self::ROLE_ID,self::ID);
    }
}

?>
