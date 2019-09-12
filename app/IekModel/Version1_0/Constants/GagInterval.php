<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-8-10
 * Time: 下午4:45
 */

namespace App\IekModel\Version1_0\Constants;


class GagInterval extends IekConstant {
    const DAY_1 = 24 * 60;
    const DAY_3 = self::DAY_1 * 3;
    const WEEK = self::DAY_1 * 7;
    const FOREVER = -1;
}