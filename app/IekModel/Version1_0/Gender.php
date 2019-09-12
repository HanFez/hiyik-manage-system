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
class Gender extends IekModel
{
    const GENDERS = [
        '1' => ['name' => 'menfolk',
            'male' => '10',
            'female' => '0',
            'uri' => 'iekAome/settings/genders/male.png',
            'is_active' => true,
            'is_removed' => false,
        ],
        '2' => ['name' => 'malegod',
            'male' => '9',
            'female' => '1',
            'uri' => 'iekAome/settings/genders/male.jpg',
            'is_active' => true,
            'is_removed' => false,
        ],
        '3' => ['name' => 'shota',
            'male' => '8',
            'female' => '2',
            'uri' => 'iekAome/settings/genders/male.jpg',
            'is_active' => true,
            'is_removed' => false,
        ],
        '4' => ['name' => 'malelover',
            'male' => '7',
            'female' => '3',
            'uri' => 'iekAome/settings/genders/male.jpg',
            'is_active' => true,
            'is_removed' => false,
        ],
        '5' => ['name' => 'flowery',
            'male' => '6',
            'female' => '4',
            'uri' => 'iekAome/settings/genders/male.jpg',
            'is_active' => true,
            'is_removed' => false,
        ],
        '6' => ['name' => 'Mx',
            'male' => '5',
            'female' => '5',
            'uri' => 'iekAome/settings/genders/female.jpg',
            'is_active' => true,
            'is_removed' => false,
        ],
        '7' => ['name' => 'cowgirl',
            'male' => '4',
            'female' => '6',
            'uri' => 'iekAome/settings/genders/female.jpg',
            'is_active' => true,
            'is_removed' => false,
        ],
        '8' => ['name' => 'tsundere',
            'male' => '3',
            'female' => '7',
            'uri' => 'iekAome/settings/genders/female.jpg',
            'is_active' => true,
            'is_removed' => false,
        ],
        '9' => ['name' => 'moegirl',
            'male' => '2',
            'female' => '8',
            'uri' => 'iekAome/settings/genders/female.jpg',
            'is_active' => true,
            'is_removed' => false,
        ],
        '10' => ['name' => 'goddess',
            'male' => '1',
            'female' => '9',
            'uri' => 'iekAome/settings/genders/female.jpg',
            'is_active' => true,
            'is_removed' => false,
        ],
        '11' => ['name' => 'girl',
            'male' => '0',
            'female' => '10',
            'uri' => 'iekAome/settings/genders/female.jpg',
            'is_active' => true,
            'is_removed' => false,
        ],
    ];

    /**
    * @var  gender_id       INT4
    * @var  name            String
    * @var  description     String
    * @var  updated_at      timestamps
    * @var  created_at      timestamps
    * @var  url             String      icon's url
    * @var  is_active       Boolean
    * @var  is_removed      Boolean
    * @var  male_percent    INT4
    * @var  female_percent  INT4
    */

    public $primaryKey = 'id';
    protected $table = 'tblGenders';

}

?>
