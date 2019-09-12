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
class ManageLogs extends IekModel
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
    protected $table = 'tblManageLogs';

    public function reason() {
        return $this->hasOne(self::$NAME_SPACE.'\Reason', self::ID, self::REAID);
    }

    public function operator(){
        return $this->hasOne(self::$NAME_SPACE.'\Employee', self::ID, 'operator_id');
    }

}

?>
