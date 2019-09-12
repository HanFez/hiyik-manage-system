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
class PersonImage extends IekModel {

    /**
     * @var  person_id           INT4
     * @var  account             INT4
     * @var  gender_id           INT4
     * @var  avatar_id           INT4     headImg
     * @var  created_at          timestamps
     * @var  updated_at          timestamps
     * @var  nick                String
     * @var  first_name          String
     * @var  middle_name         String
     * @var  last_name           String
     * @var  birthday            timestamps
     */
    // public $timestamps = false;
    // public $incrementing = false;

    protected $table = 'tblPersonImages';
    protected $fillable = [
        'created_at',
        'upadte_at',
        'is_active',
        'is_removed',
        'publication_id',
        'image_id',
        'person_id'
    ];

}

?>
