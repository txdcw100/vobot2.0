<?php
/**
 * Created by PhpStorm.
 * User: 89340
 * Date: 2020/1/7
 * Time: 10:29
 */

namespace Robot\Commands;

use Illuminate\Console\Command;
use Robot\Jobs\GroupQrcodeJob;
use Robot\Models\RobotGroup;


class RobotQrcodeCommand extends Command
{


    protected $signature = 'rsync:robotqrcode';

    protected $description = '图片转化';

    const QUEUE_INDEX = 'robot-public';

    /**
     * @var RobotGroup
     */
    private $model;

    public function __construct(RobotGroup $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $groupList = $this->_getGroup();
        if(count($groupList) == 0) return;
        foreach($groupList as $lv){
            dispatch(new GroupQrcodeJob([
                'id' => $lv->id,
                'chatroom' => $lv->wx_id,
                'robot_group_id' => $lv->robot_group_id,
                'qrcode' => $lv->qrcode,
            ]))
            ->onQueue(self::QUEUE_INDEX);
        }
    }


    /**
     * @return mixed
     */
    private function _getGroup()
    {
        return RobotGroup::WhereDate('expired_at', '<', now()->format('Y-m-d 00:00:00'))
            ->orWhereNull('qrcode_img')
            ->get(['id', 'wx_id', 'robot_group_id', 'qrcode']);
    }
}
