<?php

namespace Robot\Commands;

use Illuminate\Console\Command;
use Robot\Models\RobotCate;
use Robot\Models\RobotGroup;

class RsyncRobotCateMemberCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rsync:robotcatemember';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '间隔一个小时更新分类群人数';

    /**
     * @var RobotCate
     */
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
    public function __construct(RobotCate $model)
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
        $builder = $this->model;
        $total = $builder->count();
        if($total == 0) return;
        $totalPage = ceil($total / $this->limit);
        while ($this->page <= $totalPage){
            $list = $builder->offset(($this->page - 1))->limit($this->limit)->get(['id']);
            if(count($list) == 0){
                break;
            }
            foreach ($list as $key=>$items){
                $this->_upRobotCateMemberNum($items->id);
            }
            $this->page++;
        }
    }

    /**
     * 更新
     * @param $id
     * @param $model
     */
    private function _upRobotCateMemberNum($id)
    {
        $this->model->where('id', $id)->update(
            ['member_count' => RobotGroup::query()->where('cate_id', $id)->sum('friend_total')]
        );
    }
}
