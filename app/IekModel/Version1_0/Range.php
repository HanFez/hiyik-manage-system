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
class Range extends IekModel
{
    const SELF = 1;
    const FRIEND = 2;
    const FAN = 3;
    const GROUP = 4;
    const ALL = 5;
    const SPECIFY = 6;
    /**
    * @var  range_id            INT4
    * @var  name                String
    * @var  description         String
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_actived          Boolean
    * @var  is_removed          Boolean
    */

    public $primaryKey = 'id';
    protected $table = 'tblRanges';

}

?>
