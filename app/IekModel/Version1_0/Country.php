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
class Country extends IekModel
{
    
    /**
    * @var  id             String
    * @var  continent_id   String
    * @var  name           String
    * @var  iso3           String
    * @var  cia            String
    * @var  flag           String
    * @var  telephone      String
    * @var  internet       String
    * @var  updated_at     timestamps
    * @var  created_at     timestamps
    * @var  is_removed     Boolean
    */

    public $incrementing = false;
    public $primaryKey = 'id';
    protected $table = 'tblCountries';

}

?>
