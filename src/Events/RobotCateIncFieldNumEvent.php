<?php

namespace Robot\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RobotCateIncFieldNumEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cateId;
    public $field;
    public $num;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $cateId, string $field, int $num = 1)
    {
        //
        $this->cateId = $cateId;
        $this->field = $field;
        $this->num = $num;
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
