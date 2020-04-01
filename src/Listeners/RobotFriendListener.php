<?php

namespace Robot\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Robot\Events\RobotCateDecFieldNumEvent;
use Robot\Models\RobotGroupFriend;

class RobotFriendListener
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
     * 删除
     * @param $event
     */
    public function onDel($event)
    {
        $friendId = $event->friendId;
        if(!$friendId) return;
        $infos = RobotGroupFriend::with('groups')->find($friendId);
        if($infos->delete()){
            $infos->groups->decrement('friend_total');

            //减少群分类人数
            event(new RobotCateDecFieldNumEvent($infos->groups->cate_id, 'member_count'));
        }
        return;
    }

    /**
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Robot\Events\RobotFriendDelEvent',
            'Robot\Listeners\RobotFriendListener@onDel');
    }
}
