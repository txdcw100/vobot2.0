<?php

namespace Robot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Robot\Events\RobotMessageSaveEvent;

class AddMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 5;

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 2;

    /**
     * @var
     */
    private $options;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($options)
    {
        //
        $this->options = $options;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        event(new RobotMessageSaveEvent($this->options['group_id'], Arr::except($this->options, ['group_id'])));
    }
}
