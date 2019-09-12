<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 16-5-4
 * Time: 上午12:30
 */

namespace App\IekModel\EventArguments;


/**
 * Class EventArguments
 * @package app\IekModel\EventArguments
 * DO NOT FORGET TO ADD EVENT INTO EventServiceProvider
 * A common event arguments class
 */
class EventArguments
{
    public $sender;
    public $data;
    public $callback;
    public $callbackArgs;
    public function __construct($sender, $data = null, callable $callback = null, $args = null) {
        $this->sender = $sender;
        $this->data = $data;
        $this->callback = $callback;
        $this->callbackArgs = $args;
    }
}