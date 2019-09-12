<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-8-19
 * Time: 下午4:41
 */

namespace App\Events;
use App\IekModel\EventArguments\NotifyEventArguments;
use Illuminate\Queue\SerializesModels;


/**
 * Class NotifyEvent
 * To generator a notify record according to the person action.
 * The event params use common EventArguments, but the EventArguments->data is notify object.
 * DO NOT FORGET TO ADD INTO EventServiceProvider
 * @package App\Events
 */
class NotifyEvent extends Event{
    use SerializesModels;
    /**
     * @var NotifyEventArguments
     */
    public $args;

    /**
     * Create a new event instance.
     * @param $args NotifyEventArguments The data handled via event.
     * @return void
     */
    public function __construct(NotifyEventArguments $args)
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