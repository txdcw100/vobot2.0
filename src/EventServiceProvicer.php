<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-04-01
 * Time: 20:03
 */

namespace Robot;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Robot\Listeners as Listeners;

class EventServiceProvicer extends ServiceProvider
{
    protected $subscribe = [
        Listeners\RobotAssistantListener::class,
        Listeners\RobotGroupListener::class,
        Listeners\RobotFriendListener::class,
        Listeners\RobotMessageListener::class,
        Listeners\RobotCateListener::class,
        Listeners\RobotAssistantChatroomListener::class,
        Listeners\RobotSendListener::class,
        Listeners\RobotKeywordListener::class,

    ];
}