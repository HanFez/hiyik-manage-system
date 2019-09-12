<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-11-21
 * Time: 下午2:58
 */

namespace app\IekModel\Version1_0\Constants;


class IekConstant {
    public static function getConstants() {
        $clz = new \ReflectionClass(static::class);
        $constants = $clz->getConstants();
        return $constants;
    }

}