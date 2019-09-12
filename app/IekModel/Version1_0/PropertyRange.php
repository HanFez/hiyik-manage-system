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
class PropertyRange extends IekModel
{
    
    /**
    * @var  property_id         INT4
    * @var  range_id            INT4
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  person_id           INT4
    * @var  is_canceled         Boolean
    */

    // public $timestamps = false;
    // public $incrementing = false;
    public $primaryKey = 'property_id';
    protected $table = 'tblPropertyRanges';

}

?>
