<?php

namespace Robot\Listeners;

use App\Services\RobotService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Robot\Models\RobotGroup;
use Robot\Models\RobotGroupFriend;
use Robot\Models\RobotGroupMessage;

class RobotMessageListener
{
    /**
     * @var int
     */
    private $limit = 20;

    /**
     * @var array
     */
    private $options = [];
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 保存信息
     * @param $event
     */
    public function onSave($event)
    {
        $groupId = $event->groupId;
        if(!$groupId) return;

        //其他查询参数
        $this->options = $event->options;

        $infos = RobotGroup::select('id', 'name', 'robot_group_id', 'assistant_id')
            ->with('belongsToAssistant')
            ->find($groupId);
        if(!isset($infos->id)) return;
        $this->_rsyncFriend($infos);

        $robotAssistantId = $infos->belongsToAssistant->robot_assistant_id;
        $robotGroupId = $infos->robot_group_id;

        $result = $this->_getMessageList($robotAssistantId, $robotGroupId);

        if($result['status'] == 0) return;
        if($result['data']['total'] == 0) return;

        $page = 1;
        while ($page <= $result['data']['total_pages']){
            $messageList = $this->_getMessageList($robotAssistantId, $robotGroupId, $page++);
            if($messageList['status'] == 0) break;
            if(empty($messageList['data']['items'])) break;
            $this->_saveMessage($messageList['data']['items'], $groupId);
        }
        return;
    }

    /**
     * @param $datas
     * @param $groupId
     */
    private function _saveMessage($datas, $groupId)
    {
        $options = [];
        foreach ($datas as $key=>$items){
            if($this->_getMessage($items['id'])) continue 1;
            if(in_array($items['msg_type'], ['10000', '10002'])) continue 1;
            if(empty($items['content'])) continue 1;

            $options[] = [
                'msg_id' => $items['id'],
                'friend_id' => $this->_getFriendId($groupId, $items['wx_id'])?:0,
                'group_id' => $groupId,
                'wx_id' => $items['wx_id'],
                'msg_type' => $items['msg_type'],
                'content' => $items['content'],
                'created_at' => $items['created_at']?:now()->toDateTimeString(),
            ];
        };
        if($options){
            RobotGroupMessage::insert($options);
        }
    }

    /**
     * @param $msgId
     * @return mixed
     */
    private function _getMessage($msgId)
    {
        return RobotGroupMessage::where('msg_id', $msgId)->value('id');
    }

    private function _getFriendId($groupId, $wxId)
    {
        return Cache::remember("friend-{$wxId}-{$groupId}", now()->addMinutes(3600),function() use($groupId, $wxId){
           return RobotGroupFriend::where(['wx_id' => $wxId, 'group_id' => $groupId])->value('id')?:0;
        });
    }

    /**
     * @param $robotAssistantId
     * @param $robotGroupId
     * @param int $page
     * @return mixed
     */
    private function _getMessageList($robotAssistantId, $robotGroupId, $page = 1)
    {
        return app('robot')->getMessage()->getList(
            $robotAssistantId,
            $robotGroupId,
            $this->options,
            $page,
            $this->limit
        );
    }

    /**
     * 是否同步群成员
     * @param $infos
     */
    private function _rsyncFriend($infos)
    {
        if(RobotGroupFriend::where('group_id', $infos->id)->count()){
            return;
        }
        //创建群成员
        RobotService::init()->addFriendJob($infos->id, $infos->robot_group_id);
    }

    /**
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Robot\Events\RobotMessageSaveEvent',
            'Robot\Listeners\RobotMessageListener@onSave');
    }
}
