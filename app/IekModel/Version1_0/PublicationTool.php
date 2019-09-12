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
class PublicationTool extends IekModel {

    protected $table = 'tblPublicationTools';
    protected $fillable = [
        'publication_id', 'tool_id', 'is_active', 'is_removed'
    ];

}

?>
