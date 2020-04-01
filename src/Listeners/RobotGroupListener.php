<?php

namespace Robot\Listeners;

use App\Services\RobotService;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Robot\Events\RobotCateIncFieldNumEvent;
use Robot\Models\RobotAssistant;
use Robot\Models\RobotCate;
use Robot\Models\RobotGroup;

class RobotGroupListener
{
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
     * 创建
     * @param $event
     */
    public function onSave($event)
    {
        $datas = $event->params;
        $cateId = $event->cateId??0;
        if(!$cateId || empty($datas)) return;
        $wxId = $event->wxId;
        if(!$wxId) return;

        //分类
        $tenantId = RobotCate::where('id', $cateId)->value('tenant_id');
        if(!$tenantId) return;

        $assistantInfos = RobotAssistant::where('wx_id', $wxId)->first();
        if(!isset($assistantInfos->id)) return;
        if(empty($assistantInfos->robot_assistant_id)) return;

        $wxIds = [];
        array_walk($datas, function($items) use(&$wxIds){
            $wxIds[] = $items['wx_id'];
        });
        $assistantId = $assistantInfos->id;
        //接口
        $result = app('robot')->getGroup()->getList(
            $assistantInfos->robot_assistant_id,
            ['wx_ids' => $wxIds]
        );
        if($result['status'] == 0) return;

        $items = $result['data']['items'];
        if(empty($items)) return;
        array_walk($items, function($item) use($assistantId, $cateId, $tenantId){
            $this->_store([
                'tenant_id' => $tenantId,
                'assistant_id' => $assistantId,
                'robot_group_id' => $item['id'],
                'qrcode' => $item['qrcode'],
                'head_pic' => $item['head_pic'],
                'cate_id' => $cateId,
                'name' => $item['name'],
                'friend_total' => $item['friend_total']??0,
                'chat_room_owner' => $item['chatroom_owner'],
                'wx_id' => $item['wx_id'],
                'status' => RobotGroup::GROUP_STATUS_NORMAL,
                'type' => 0,
                'expired_at' => $item['expired_at']??Carbon::parse('+5 day')->format('Y-m-d 00:00:00'),
            ]);
        });
        return;
    }

    /**
     * @param $datas
     */
    private function _store($datas)
    {
        if(RobotGroup::where([
            'assistant_id' => $datas['assistant_id'],
            'wx_id' => $datas['wx_id'],
            'cate_id' => $datas['cate_id'],
        ])->value('id')) return;
        $result = RobotGroup::create($datas);
        if($result->id)
            //增加群数事件
            event(new RobotCateIncFieldNumEvent($datas['cate_id'], 'group_count'));
            //增加人数事件
            event(new RobotCateIncFieldNumEvent($datas['cate_id'], 'member_count', $datas['friend_total']));
            //记录群成员队列
            RobotService::init()->addFriendJob($result->id, $datas['robot_group_id'], true, 1, 'fresh');
            //更新头像
            $this->_upCateAvatar($datas['cate_id'], $datas['head_pic']);
    }

    /**
     * 更新分类头像
     * @param $cateId
     * @param $avatar
     */
    private function _upCateAvatar($cateId, $avatar)
    {
        if(empty($avatar)) return;
        RobotCate::where('id', $cateId)->update(['avatar' => $avatar]);
    }

    /**
     * 更新
     * @param $event
     */
    public function onUpField($event)
    {
        $field = $event->field??'';
        if(!$field) return;
        $value = $event->value??'';
        $id = $event->groupId??0;
        if(!$id) return;
        RobotGroup::where('id', $id)->update([
            $field => $value,
        ]);
    }

    /**
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Robot\Events\RobotGroupSaveEvent',
            'Robot\Listeners\RobotGroupListener@onSave');

        $events->listen(
            'Robot\Events\RobotGroupUpFieldEvent',
            'Robot\Listeners\RobotGroupListener@onUpField');
    }
}
