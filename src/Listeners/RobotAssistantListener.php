<?php

namespace Robot\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Robot\Models\RobotAssistant;
use Robot\Models\RobotAssistantChatroom;
use Robot\Models\RobotGroup;

class RobotAssistantListener
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
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function onLogout($event)
    {
        //
        $wxId = $event->params['wxid']??'';
        if(!$wxId) return;
        RobotAssistant::where('wx_id', $wxId)->update([
            'status' => RobotAssistant::STATUS_OFF
        ]);
        return;
    }

    /**
     * 创建／更新
     * @param $event
     */
    public function onSave($event)
    {
        $datas = $event->params['data']??'';
        if(!isset($datas['wx_id']) || empty($datas['wx_id'])) return;

        $infos = $event->params['infos']??'';
        if($infos){
            $this->_upAssistant($infos->id, $event->params['uuid']);
        }
        else{
            DB::beginTransaction();
            $assistantId = RobotAssistant::where('wx_id', $datas['wx_id'])->value('id');
            if($assistantId){
                DB::rollback();
                $this->_upAssistant($assistantId, $event->params['uuid']);
                return;
            }

            $result = app('robot')->getAssistant()->info($datas['wx_id']);
            if($result['status'] == 0){
                DB::rollback();
                return;
            }

            RobotAssistant::create([
                'user_id' => $datas['user_id']??0,
                'robot_assistant_id' => $result['data']['id']??0,
                'wx_uid' => $event->params['uuid'],
                'wx_id' => $datas['wx_id'],
                'status' => RobotAssistant::STATUS_ON,
                'nickname' => $result['data']['nickname']??'',
                'avatar' => $result['data']['avatar']??'',
                'alias' => $result['data']['alias']??'',
                'source' => $result['data']['source']??'api'
            ]);
            DB::commit();
        }
        return;
    }

    /**
     * 更新群助手
     * @param $id
     * @param $uuId
     */
    private function _upAssistant($id, $uuId)
    {
        RobotAssistant::where('id', $id)->update([
            'wx_uid' => $uuId,
            'status' => RobotAssistant::STATUS_ON,
        ]);
    }

    /**
     * 群操作
     * @param $event
     */
    public function onChatroom($event)
    {
        $assistantId = $event->params['assistant_id']??0;
        if(!$assistantId) return;
        $datas = $event->params['data']['items']??[];
        if(!$datas) return;
        $addParams = [];
        foreach ($datas as $key=>$items){
            if(!$this->_isChatroom($items)){
                array_push($addParams, [
                    'assistant_id' => $assistantId,
                    'wx_id' => $items['chatroom'],
                    'nickname' => $items['nickname'],
                    'qrcode' => $items['qrcode']??'',
                    'head_pic' => $items['head_pic'],
                    'created_at' => now()->toDateTimeString(),
                ]);
            }
            $this->_upGroup($items);
        }
        if($addParams){
            RobotAssistantChatroom::insert($addParams);
        }
        return;
    }

    /**
     * 验证群
     * @param $assistantId
     * @param $options
     * @return bool
     */
    private function _isChatroom($options)
    {
        $infos = RobotAssistantChatroom::where('wx_id', $options['chatroom'])->first();
        if(!isset($infos->id)){
            return false;
        }
        $upParams = [];
        if(empty($infos->nickname) || $infos->nickname == '暂无群昵称'){
            $upParams['nickname'] = $options['nickname'];
        }
        if(empty($infos->head_pic)){
            $upParams['head_pic'] = $options['head_pic'];
        }
        if($upParams){
            RobotAssistantChatroom::where('wx_id', $options['chatroom'])->update($upParams);
        }
        return true;
    }

    /**
     * @param $assistantId
     * @param $options
     * @return bool
     */
    private function _upGroup($options)
    {
        $infos = RobotGroup::where('wx_id', $options['chatroom'])->first();
        if(!isset($infos->id)){
            return false;
        }
        $upParams = [];
        if(empty($infos->nickname) || $infos->nickname == '暂无群昵称'){
            $upParams['name'] = $options['nickname'];
        }
        if(empty($infos->head_pic)){
            $upParams['head_pic'] = $options['head_pic'];
        }
        if(isset($options['member_count']
            ) && ($infos->friend_total != $options['member_count'])){
            $upParams['friend_total'] = $options['member_count'];
        }
        if($upParams){
            RobotGroup::where('id', $infos->id)->update($upParams);
        }
        return true;
    }

    /**
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Robot\Events\RobotAssistantLogoutEvent',
            'Robot\Listeners\RobotAssistantListener@onLogout');

        $events->listen(
            'Robot\Events\RobotAssistantSaveEvent',
            'Robot\Listeners\RobotAssistantListener@onSave');

        $events->listen(
            'Robot\Events\RobotAssistantChatroomEvent',
            'Robot\Listeners\RobotAssistantListener@onChatroom'
        );
    }
}
