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
class Province extends IekModel
{
    
    /**
    * @var  province_id         INT4
    * @var  country_id          INT4
    * @var  created_at          timestamps
    * @var  updated_at          timestamps
    * @var  name                String
    * @var  memo                String
    */

    // public $timestamps = false;
    // public $incrementing = false;
    public $primaryKey = 'province_id';
    protected $table = 'tblProvinces';

}

?>
