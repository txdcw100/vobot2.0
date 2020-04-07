<?php

namespace Robot\Listeners;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Robot\Models\RobotGroup;
use Robot\Models\RobotGroupSend;
use Robot\Models\RobotMaterial;
use Robot\Models\RobotSend;
use Illuminate\Support\Str;

class RobotSendListener
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
        $datas = ($event->datas);
        if(!$datas) return self::getResult(0, '保存字段数据为空');
        if(!$datas['tenant_id']) return self::getResult(0, '商户信息为空');
        $send = $this->store($datas);
        $groupIds = $this->_getGroup($datas['group_id']);
        Cache::driver('redis')->set(
            'send:'.$send['data'],
            json_encode([
                'start_time' => $datas['plan_send'],
                'groupIds' => $groupIds,
                'datas' => $datas,
                'send_id'=>$send['data']
            ]),
            time()+51840000);
    }

    /**
     * 创建
     * @return [type]
     */
    private function store($datas)
    {
        DB::beginTransaction();
        $materialObj = RobotMaterial::create([
            'content' =>  $this->_getContent($datas['type'], $datas),
            'type' => $datas['type'],
            'user_id' => $datas['user_id'],
            'status' => RobotMaterial::STATUS_NORMAL,
        ]);
        $sendObj = RobotSend::create([
            'title' => $datas['title'],
            'user_id' => $datas['user_id'],
            'tenant_id' => $datas['tenant_id'],
            'status' => RobotSend::STATUS_NORMAL,
            'material_id' => $materialObj->id,
            'channel' => $datas['channel']??RobotSend::CHANNEL_ASSISTANT,
            'plan_send' => $datas['plan_send']
        ]);
        $sendId = $sendObj->id;
        $groupItems = [];
        $groupIds = explode(',', $datas['group_id']);
        $tmpTime = now()->toDateTimeString();
        array_walk($groupIds, function($item) use(&$groupItems, &$tmpTime, &$sendId){
            $groupItems[] = [
                'send_id' => $sendId,
                'group_id' => $item,
                'created_at' => $tmpTime,
                'updated_at' => $tmpTime,
            ];
        });
        if($groupItems){
            RobotGroupSend::insert($groupItems);
        }
        else{
            DB::rollback();
            return self::getResult(0, '保存关键词和群关系参数为空');
        }
        DB::commit();

        return self::getResult(1, 'ok',$sendId);
    }

    /**
     * 获取内容
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    protected function _getContent($type, $datas)
    {
        $action = '_get'.ucfirst(RobotMaterial::getAllTypeEnames()[$type]);
        return $this->$action($datas);
    }

    /**
     * 文本
     * @param  [type]
     * @return [type]
     */
    protected function _getText($datas)
    {

        return $datas['txt']['content'];
    }

    /**
     * 图片
     * @param  [type]
     * @return [type]
     */
    protected function _getImage($datas)
    {
        return Str::contains($datas['thumb'], ['http', 'https']) ? $datas['thumb'] : config('app.url') . $datas['thumb'];
    }

    /**
     * 链接
     * @param  [type]
     * @return [type]
     */
    protected function _getUrl($datas)
    {
        $thumb = $this->_getImage($datas);
        return serialize([
            'title' => $datas['url']['title'],
            'content' => $datas['url']['desc'],
            'url' => $datas['url']['url'],
            'thumb' => $thumb,
            'thumb_url' => $thumb
        ]);
    }


    /**
     * @param $data
     * @return string
     */
    protected function _getGroup($data)
    {
        $groupId = (explode(',',$data));
        $groupIds = [];
        foreach($groupId as $item){
            $groupIds[] = RobotGroup::find($item)->robot_group_id;
        }
        return implode(',',$groupIds);
    }
    /**
     * @param $status
     * @param $message
     * @param mixed $data
     *
     * 业务逻辑统一结果输出
     */
    protected static function getResult($status, $message, $data = null)
    {
        if (!is_null($data)) {
            return ['status' => $status, 'message' => $message, 'data' => $data];
        }
        return ['status' => $status, 'message' => $message];
    }

    /**
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Robot\Events\RobotSendSaveEvent',
            'Robot\Listeners\RobotSendListener@onSave');
    }
}
