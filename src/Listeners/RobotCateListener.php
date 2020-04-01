<?php

namespace Robot\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Robot\Models\RobotCate;

class RobotCateListener
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
    public function onIncField($event)
    {
        //
        $field = $event->field??'';
        if(!$field) return;
        $num = $event->num??1;
        $id = $event->cateId??0;
        if(!$id) return;
        RobotCate::where('id', $id)->increment($field, $num);

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function onDecField($event)
    {
        //
        $field = $event->field??'';
        if(!$field) return;
        $num = $event->num??1;
        $id = $event->cateId??0;
        if(!$id) return;
        RobotCate::where('id', $id)->decrement($field, $num);

    }

    /**
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Robot\Events\RobotCateIncFieldNumEvent',
            'Robot\Listeners\RobotCateListener@onIncField');

        $events->listen(
            'Robot\Events\RobotCateDecFieldNumEvent',
            'Robot\Listeners\RobotCateListener@onDecField');
    }
}
