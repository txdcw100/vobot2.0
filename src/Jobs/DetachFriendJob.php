<?php

namespace Robot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DetachFriendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 3;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 5;

    private $wxid, $chatroom, $toWxid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wxid, $chatroom, $toWxid)
    {
        //
        $this->wxid = $wxid;
        $this->chatroom = $chatroom;
        $this->toWxid = $toWxid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        app('robot')->getFriend()->detach(
            $this->wxid, $this->chatroom, $this->toWxid
        );
    }
}
