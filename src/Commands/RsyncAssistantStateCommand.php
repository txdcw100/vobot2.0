<?php

namespace Robot\Commands;

use Illuminate\Console\Command;
use Robot\Events\RobotAssistantLogoutEvent;
use Robot\Models\RobotAssistant;

class RsyncAssistantStateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rsync:assistantstate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步微信群助手状态';

    private $model;

    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var int
     */
    private $limit = 10;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RobotAssistant $model)
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
        //
        $builder = $this->model->normal();
        $total = $builder->count();
        if($total == 0) return;

        $totalPage = ceil($total / $this->limit);
        while ($this->page <= $totalPage){
            $list = $builder->offset(($this->page - 1))->limit($this->limit)->get();
            if(count($list) == 0){
                break;
            }
            foreach ($list as $key=>$items){
                $this->_getAssistantState($items->wx_id);
            }
            $this->page++;
        }
    }

    /**
     * @param string $wxId
     */
    private function _getAssistantState(string $wxId)
    {
        $result = app('robot')->getAssistant()->info($wxId);
        if($result['status'] == 0) return;
        if($result['data']['state'] == 1) return;
        event(new RobotAssistantLogoutEvent(['wxid' => $wxId]));
    }
}
