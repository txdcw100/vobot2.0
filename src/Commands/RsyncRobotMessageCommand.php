<?php

namespace Robot\Commands;

use Illuminate\Console\Command;
use Robot\Jobs\AddMessageJob;
use Robot\Models\RobotAssistant;
use Robot\Models\RobotGroup;

class RsyncRobotMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rsync:robotmessage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步微信机器人聊天信息';

    /**
     * @var
     */
    protected $assistant;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RobotAssistant $assistant = null)
    {
        parent::__construct();
        $this->assistant = $assistant;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $assistantList = $this->_getAssistant();
        if(count($assistantList) == 0) return;
        $times = [
            'start' => now()->format('Y-m-d H:00:00'),
            'end' => date('Y-m-d H:i:s', mktime(23, 59, 59, date('d'), date('m'), date('y'))),
        ];
        foreach ($assistantList as $key=>$items){
            $this->_getGroup($items, $times);
        }
    }

    /**
     * @return mixed
     */
    private function _getAssistant()
    {
        return RobotAssistant::query()->normal()->get(['id', 'robot_assistant_id', 'wx_id']);
    }

    /**
     * @param $assistantId
     */
    private function _getGroup($datas, $options)
    {
        $groupList = RobotGroup::where('assistant_id', $datas['id'])
            ->normal()
            ->get(['id']);
        if(count($groupList) == 0) return;

        foreach ($groupList as $key=>$items){
            $options['group_id'] = $items->id;
            dispatch(new AddMessageJob($options))
                ->delay(now()->addSeconds($key+1));

            unset($options['group_id']);
        }
    }
}
