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
class PersonRecommend extends IekModel
{
    
    /**
    * @var  person_id           INT4
    * @var  publication_id      INT4
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  is_canceled         Boolean
    * @var  is_offical          Boolean
    */

    // public $timestamps = false;
    // public $incrementing = false;
    public $primaryKey = 'id';
    protected $table = 'tblPersonRecommends';

}

?>
