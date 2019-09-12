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

namespace App\IekModel\Version1_0\Notify;

/**
* @author       Rich
*/
class Publication extends IekNotifyModel
{
    
    /**
     * @var  id                  INT8
     * @var  created_at          timestamps
     * @var  updated_at          timestamps
     * @var  action              String
     * @var  from_id             INT8
     * @var  to_id               INT8
     * @var  publication_id      INT8
     * @var  is_read             Boolean
     * @var  is_active           Boolean
     * @var  is_removed          Boolean
    */

    public $primaryKey = 'id';
    protected $table = 'tblPublications';

}

?>
