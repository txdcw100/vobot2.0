<?php

namespace Robot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Robot\Events\RobotGroupUpFieldEvent;
use Robot\Models\RobotGroupFriend;

class AddFriendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 5;

    /**
     * @var
     */
    protected $groupId = 0;

    /**
     * @var
     */
    protected $robotGroupId = 0;

    /**
     * @var string
     */
    protected $handle = '';

    /**
     * @var int
     */
    protected $limit = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $groupId, int $robotGroupId, $handle = '')
    {
        //
        $this->groupId = $groupId;
        $this->robotGroupId = $robotGroupId;
        $this->handle = $handle;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        try{
            $result = $this->_getFriendList(1, $this->limit, $this->handle);
            if($result['status'] == 0) return;
            if($result['data']['total'] == 0) return;

            event(new RobotGroupUpFieldEvent($this->groupId, 'friend_total', $result['data']['total']));

            $page = 1;
            if($page == $result['data']['total_pages']){
                $this->_saveFriend($result['data']['items']);
            }
            else {
                while ($page <= $result['data']['total_pages']) {
                    $list = $this->_getFriendList($page++, $this->limit);
                    if ($list['status'] == 0) break;
                    if ($list['data']['total_pages'] == 0) break;
                    $this->_saveFriend($list['data']['items']);
                }
            }
            return;
        }
        catch (\Exception $ex){
            info('AddFriendJob exception:'.$ex->getMessage());
            return;
        }
    }

    /**
     * @param int $page
     * @param int $limit
     */
    private function _getFriendList(int $page = 1, int $limit = 10, string $handle = '')
    {
        return app('robot')->getFriend()->getList($this->robotGroupId, $page, $limit, $handle);
    }

    /**
     * 保存群成员
     * @param array $datas
     */
    private function _saveFriend(array $datas)
    {
        $options = [];
        array_walk($datas, function($items) use(&$options){
            if(!RobotGroupFriend::where(['group_id' => $this->groupId,
                'wx_id' => $items['wx_id']])->value('id')) {
                $options[] = [
                    'group_id' => $this->groupId,
                    'nickname' => $items['name'],
                    'wx_id' => $items['wx_id'],
                    'avatar' => $items['avatar']?:'',
                    'message_count' => $items['message_count'],
                    'created_at' => now()->toDateTimeString(),
                ];
            }
        });
        if($options){
            RobotGroupFriend::insert($options);
            unset($options);
        }
    }

    /**
     * @param null $exception
     */
    public function fail($exception = null)
    {
        info('AddFriendJob fail: groupId '.$this->groupId.' robot_group_id:'.$this->robotGroupId);
    }
}
