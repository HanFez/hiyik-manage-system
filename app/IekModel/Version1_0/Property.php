<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-5-5
 * Time: 上午9:15
 */

namespace App\IekModel\Version1_0;
use App\IekModel\Version1_0\IekModel;

class Property extends IekModel
{
    const PROPERTIES = [
        '1' => ['name' => 'name',
            'description' => 'name',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '2' => ['name' => 'phone',
            'description' => 'phone',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '3' => ['name' => 'QQ',
            'description' => 'QQ',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '4' => ['name' => 'weixin',
            'description' => 'weixin',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '5' => ['name' => 'sinaweibo',
            'description' => 'sina weibo',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '6' => ['name' => 'tencentweibo',
            'description' => 'tencent weibo',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '7' => ['name' => 'gender',
            'description' => 'gender',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '8' => ['name' => 'location',
            'description' => 'location',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '9' => ['name' => 'address',
            'description' => 'address',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '10' => ['name' => 'email',
            'description' => 'email',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '11' => ['name' => 'career',
            'description' => 'career',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
        '12' => ['name' => 'education',
            'description' => 'education',
            'target' => '',
            'is_active' => true,
            'is_removed' => false,
        ],
    ];
    protected $table = "tblProperties";

}