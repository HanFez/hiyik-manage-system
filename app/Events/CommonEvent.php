<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/4/20
 * Time: 16:13
 */
namespace App\Events;

use App\IekModel\EventArguments\EventArguments;
use Illuminate\Queue\SerializesModels;
class CommonEvent extends Event
{
    use SerializesModels;
    /**
     * @var EventArguments
     */
    public $args;

    /**
     * Create a new event instance.
     * @param $args EventArguments The data handled via event.
     * @return void
     */
    public function __construct(EventArguments $args)
    {
        $this->args = $args;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}