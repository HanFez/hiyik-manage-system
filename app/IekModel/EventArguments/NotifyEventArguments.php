<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 16-5-4
 * Time: 上午12:30
 */

namespace App\IekModel\EventArguments;


/**
 * Class NotifyEventArguments
 * @package app\IekModel\EventArguments
 * Notify event arguments class
 */
class NotifyEventArguments extends EventArguments
{
    public $sender;
    public $model;
    public $data;
    public $callback;
    public $callbackArgs;
    public function __construct($sender, $model, $data = null, callable $callback = null, $args = null) {
        $this->sender = $sender;
        $this->model = $model;
        $this->data = $data;
        $this->callback = $callback;
        $this->callbackArgs = $args;
    }
}