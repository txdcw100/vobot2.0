<?php

namespace Robot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Robot\Models\RobotGroup;

class GroupQrcodeJob implements ShouldQueue
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
    public $tries = 3;


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
        $id = $this->options['id']??0;
        if(0 >= $id) return;
        $chatroom = $this->options['chatroom']??'';
        if(empty($chatroom)) return;
        $robotGroupId = $this->options['robot_group_id']??'';
        if(0 >= $robotGroupId) return;

        $result = app('robot')->getGroup()->getQrcode($robotGroupId, $chatroom);
        if(0 == $result['status']) return;
        $this->_upRobotGroupQrcode($id, $result['data']);
        return;
    }

    /**
     * 更新
     * @param $id
     * @param $model
     */
    private function _upRobotGroupQrcode($id, $datas)
    {
        RobotGroup::where('id', $id)->update(
            [
                'expired_at' => $datas['expired_at'],
                'qrcode' => $datas['qrcode'],
                'qrcode_img' => uploadBase64($datas['qrcode'])['path']??''
            ]
        );
    }
}
