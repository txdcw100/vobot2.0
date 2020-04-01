<?php

namespace Robot\Commands;

use Illuminate\Console\Command;
use Robot\Jobs\DetachFriendJob;
use Robot\Models\RobotBlacklist;
use Robot\Models\RobotGroup;

class DelRobotBlackFriendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excue:delrobotblackfriend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '群成员成黑名单，执行调用删除群成员接口';

    private $model;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RobotBlacklist $model)
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
        $builder = $this->model->where('status', 0);
        if($builder->count() == 0) return;

        $result = $builder->get();
        foreach ($result as $key=>$items){
            if(empty($items->wx_id)) continue 1;
            $this->_getGroup($items->tenant_id, $items->wx_id, $items->group_id);
        }
        return;
    }

    /**
     * 群
     * @param $tenantId
     * @param $friendWxid
     */
    private function _getGroup($tenantId, $friendWxid, $groupIds)
    {
        $list = RobotGroup::where('tenant_id', $tenantId)
            ->with(['friends' => function($query) use($friendWxid){
                return $query->where('wx_id', $friendWxid);
            }])
            ->get(['id', 'robot_group_id']);
        if(count($list) == 0){
            return;
        }
        foreach ($list as $oKey=>$items){
            if($items->friends->count() == 0) {
                $this->_delDetachFriend($groupIds, $friendWxid);
                continue;
            }
            foreach ($items->friends as $sKey=>$item) {
                $this->_delMember($item->id, $items->robot_group_id, $item->wx_id);
            }
        }
    }

    /**
     * @param int $friendId
     * @param int $robotGroupId
     * @param string $friendWxid
     */
    private function _delMember(int $friendId, int $robotGroupId, string $friendWxid)
    {
        app('robot')->getFriend()->deleteMemeber(
            $friendId,
            $robotGroupId,
            $friendWxid
        );
    }

    /**
     * 直接操作微信
     * @param $groupIds
     * @param $toWxid
     */
    private function _delDetachFriend($groupIds, $toWxid)
    {
        if(empty($groupIds)) return;
        $list = RobotGroup::whereIn('id', $groupIds)->normal()
            ->with('belongsToAssistant')
            ->get(['id', 'robot_group_id', 'wx_id', 'assistant_id']);
        if(count($list) == 0) return;
        foreach ($list as $key=>$items){
            dispatch(new DetachFriendJob(
                $items->belongsToAssistant->wx_id,
                $items->wx_id,
                $toWxid
            ))->delay(now()->addSeconds(1));
        }
    }
}
