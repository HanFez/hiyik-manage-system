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

use Illuminate\Support\Facades\DB;

/**
 * @author       Rich
 */
class City extends IekModel {

    public $primaryKey = 'id';
    protected $table = 'tblCities';

    static function searchCity($city) {
        $sql = <<<sql
                SELECT DISTINCT person_id FROM "tblPersonAddresses" 
                    INNER JOIN "tblAddresses" ON "tblAddresses".id = "tblPersonAddresses".address_id 
                        INNER JOIN "tblCities" ON "tblCities".id= "tblAddresses".city_id 
                            WHERE REPLACE("tblCities".merge_name,',','') LIKE'%$city%'
                                AND "tblPersonAddresses".is_active=true
sql;

        $rows = DB::select(DB::raw($sql));
        return $rows;
    }

}

?>
