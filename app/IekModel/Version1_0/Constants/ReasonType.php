<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-8-10
 * Time: 下午4:45
 */

namespace App\IekModel\Version1_0\Constants;


class ReasonType extends IekConstant {
    const UNOFFICIAL = 'unOfficial';
    const UNFORBIDDEN = 'unForbidden';
    const OFFICIAL = 'official';
    const FORBIDDEN = 'forbidden';
    const GAG = 'gag';
    const REJECT = 'reject';
    const REFUND = 'refund';
}