<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/4/21
 * Time: 12:32
 */
namespace App\IekModel\Utils;


interface SmsSender {
    public function send($to, $content);
    public function initialInterface($appKey, $secret, $sessionKey = null);
    public function isReady();
}
