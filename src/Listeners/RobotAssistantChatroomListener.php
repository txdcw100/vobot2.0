<?php

namespace Robot\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Robot\Models\RobotAssistantChatroom;

class RobotAssistantChatroomListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function onUpField($event)
    {
        //
        $filters = $event->filters;
        $fields = $event->fields;
        if(!$filters || !$fields) return;
        RobotAssistantChatroom::where($filters)->update($fields);
    }

    /**
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Robot\Events\RobotAssistantChatroomUpFieldEvent',
            'Robot\Listeners\RobotAssistantChatroomListener@onUpField');
    }
}
