<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-5-4
 * Time: 下午5:15
 */

namespace App\IekModel\Version1_0;


use App\IekModel\Version1_0\IekModel;

class AuthoringTool extends IekModel {
    const AUTHORING_TOOLS = [
        '1' => ['name' => 'Photoshop',
            'description' => 'Photoshop',
            'is_active' => true,
            'is_removed' => false,
            'is_official' => true,
        ],
        '2' => ['name' => '3D Studio Max',
            'description' => '3D Studio Max',
            'is_active' => true,
            'is_removed' => false,
            'is_official' => true,
        ],
        '3' => ['name' => 'AutoCAD',
            'description' => 'AutoCAD',
            'is_active' => true,
            'is_removed' => false,
            'is_official' => true,
        ],
        '4' => ['name' => 'CorelDraw',
            'description' => 'CorelDraw',
            'is_active' => true,
            'is_removed' => false,
            'is_official' => true,
        ],
        '5' => ['name' => 'Flash',
            'description' => 'Flash',
            'is_active' => true,
            'is_removed' => false,
            'is_official' => true,
        ],
        '6' => ['name' => 'Illustrator',
            'description' => 'Illustrator',
            'is_active' => true,
            'is_removed' => false,
            'is_official' => true,
        ],
        '7' => ['name' => 'Fireworks',
            'description' => 'Fireworks',
            'is_active' => true,
            'is_removed' => false,
            'is_official' => true,
        ],
    ];
    protected $table = "tblAuthoringTools";
    protected $fillable=[
        'name','description','is_active','is_removed','is_official'
    ];
}