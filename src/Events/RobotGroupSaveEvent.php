<?php

namespace Robot\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RobotGroupSaveEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $params;

    public $cateId = 0;

    public $wxId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($wxId, $cateId, $params)
    {
        //
        $this->params = $params;
        $this->cateId = $cateId;
        $this->wxId = $wxId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
