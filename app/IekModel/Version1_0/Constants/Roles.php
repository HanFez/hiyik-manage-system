<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-10-26
 * Time: 下午2:29
 */

namespace app\IekModel\Version1_0\Constants;


/**
 * Class Roles
 * @package app\IekModel\Version1_0\Constants
 * This is the role category define, the define should persist with database.
 */
class Roles extends IekConstant {
    const SUPER = ['id' => 1, 'name' => 'super'];
    const GUEST = ['id' => 2, 'name' => 'guest'];
    const NEWBIE = ['id' => 3, 'name' => 'newbie'];
    const MEDIUM = ['id' => 4, 'name' => 'medium'];
    const SENIOR = ['id' => 5, 'name' => 'senior'];
    const SIGNED = ['id' => 6, 'name' => 'signed'];
    const CELEBRITY = ['id' => 7, 'name' => 'celebrity'];
    const BIGGIE = ['id' => 8, 'name' => 'biggie'];

    const MARKET_PRIMARY = ['id' => 9, 'name' => 'marketPrimary'];
    const MARKET_MIDDLE = ['id' => 10, 'name' => 'marketMiddle'];
    const MARKET_ADVANCE = ['id' => 11, 'name' => 'marketAdvance'];
    const TECH_PRIMARY = ['id' => 12, 'name' => 'developPrimary'];
    const TECH_MIDDLE = ['id' => 13, 'name' => 'developMiddle'];
    const TECH_ADVANCE = ['id' => 14, 'name' => 'developAdvance'];
}