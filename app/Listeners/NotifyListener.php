<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-8-19
 * Time: 下午4:45
 */

namespace App\Listeners;
use App\Events\NotifyEvent;
use Illuminate\Support\Facades\Log;

class NotifyListener {
    /**
     * Create the event listener.
     *
     */
    public function __construct() {
    }

    /**
     * Handle the event.
     * The $args like bellow:
     * $args->model
     * $args->callback
     * $args->callbackArgs
     * $args->model
     *
     * @param  NotifyEvent  $event
     * @return void
     */
    public function handle(NotifyEvent $event)
    {
        $args = $event->args;
        $data = $args->data;
        $model = $args->model;
        if(!is_null($data)) {
            $model::makeNotify($data);
        }
        $callback = $args->callback;
        $callbackArgs = $args->callbackArgs;
        if(!is_null($callback)) {
            call_user_func($callback, $callbackArgs);
        }
    }
}