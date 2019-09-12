<?php

/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-22
 * Time: 下午2:05
 */
namespace App\IekModel\Version1_0\ModelInterface;
interface InterfaceFan {
    public function follow();
    public function isFollowed();
    public function fans();
    public function fanCount();
    public function cancel();
}