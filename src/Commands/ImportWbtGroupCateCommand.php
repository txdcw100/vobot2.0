<?php

namespace Robot\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Robot\Models\RobotCate;

class ImportWbtGroupCateCommand extends Command
{
    private $wbtGroupCateTable = 'wbt_group_cates';

    private $limit = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rsync:wbtgroupcate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '原微信机器人分类导入';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $builder = DB::connection('mysql_robot')
                ->table($this->wbtGroupCateTable);
        $count = $builder->count();
        $page = 1;
        $totalPage = ceil($count / $this->limit);

        while ($page <= $totalPage){
            $list = $builder->offset(($page - 1) * $this->limit)
                ->limit($this->limit)
                ->get();
            foreach ($list as $key=>$items){
                $this->_saveRbotCate($items);
            }
            sleep(1);
            $page++;
        }
    }

    /**
     * 保存
     * @param $options
     */
    private function _saveRbotCate($options)
    {
        $cateIds = explode(',', str_replace(['[',']'], '', $options->category_id));
        if($cateIds){
            $categoryIds = array_map(function($id){
                return str_replace('"', '', $id);
            },$cateIds);
        }
        else{
            $categoryIds = [];
        }
        RobotCate::firstOrCreate([
            'tenant_id' => $options->tenant_id,
            'name' => $options->name
        ], [
            'category_id' => $categoryIds,
            'operate_id' => $options->operate_id,
            'store_id' => $options->store_id,
            'group_count' => 0,
            'member_count' => 0,
            'avatar' => $this->_getGroupHead($options->id),
            'status' => $options->status,
            'created_at' => $options->created_at,
            'updated_at' => $options->updated_at,
        ]);
        unset($cateIds);
    }

    /**
     * 微信群分类图片
     * @param null $id
     * @return string
     */
    private function _getGroupHead($id = null){
        $imgCount = config('robot.cate_images_count', 20);
        if(is_null($id)){
            return get_http_url('robot/headimg/group/' . mt_rand(1, $imgCount) . '.png', 'api');
        }
        $index = $id % $imgCount;
        $index = $index == 0 ? $imgCount : $index;
        return get_http_url('robot/headimg/group/' . (string)$index . '.png', 'api');
    }
}
