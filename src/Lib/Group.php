<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-09
 * Time: 09:30
 */

namespace Robot\Lib;


use Robot\Events\RobotAssistantChatroomUpFieldEvent;
use Robot\Events\RobotAssistantChatroomEvent;
use Robot\Events\RobotGroupSaveEvent;
use Robot\Events\RobotGroupUpFieldEvent;
use Robot\Models\RobotAssistant;

class Group extends Base
{
    /**
     * @var null
     */
    private static $instanceof = null;

    /**
     * @var
     */
    private $config;

    /**
     * RobotGroup constructor.
     * @param $config
     */
    private function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param $config
     * @return Group|null
     */
    public static function init($config)
    {
        if(is_null(self::$instanceof)){
            self::$instanceof = new self($config);
        }

        self::setToken($config);

        return self::$instanceof;
    }

    /**
     * 列表
     * @param int $assistantId
     * @param array $options
     * @param int $page
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList(int $assistantId = 0, array $options = [], int $page = 1)
    {
        $result = self::httpPost(self::getUrl($this->config, $this->config['group']['list']), [
            'assistant_id' => $assistantId,
            'wx_ids' => $options['wx_ids']??[],
            'name' => $options['name']??'',
            'status' => $options['status']??'',
            'page' => $page
        ]);
        return self::returnMsg($result);
    }

    /**
     * 获取远程群
     * @param int $userId
     * @param int $assistant
     * @param string $wxId
     * @param string $name
     * @param string $action
     * @return array
     */
    public function getChatroom(
        int $assistantId = 0, string $wxId, int $page = 1, int $limit = 5, string $name = '', string $action = '')
    {
        $result = self::httpPost(self::getUrl($this->config, $this->config['group']['chatroom']), [
            'wxid' => $wxId,
            'action' => $action,
            'name' => $name,
            'page' => $page,
            'limit' => $limit
        ]);
        if($result['status'] == 1){
            //更新／添加群信息到库
            event(new RobotAssistantChatroomEvent([
                'data' => $result['data'],
                'assistant_id' => $assistantId,
            ]));
        }
        return self::returnMsg($result);
    }

    /**
     * 添加群
     * @param array $groups
     * @param int $cateId
     * @return array
     */
    public function store(string $wxId, int $cateId, array $groups)
    {
        if(empty($groups)) return self::result(0, '群数据为空');
        if(empty($cateId)) return self::result(0, '群分类数据为空');
        $options = [];
        array_walk($groups, function($items) use(&$options, $wxId){
            $options[] = ['wxid' => $wxId, 'chatroom' => $items['wx_id']];
        });
        $result = self::httpPost(self::getUrl($this->config, $this->config['group']['store']), [
            'datas' => $options,
        ]);
        if($result['status'] == 1) {
            event(new RobotGroupSaveEvent($wxId, $cateId, $groups));
        }

        return self::returnMsg($result);
    }

    /**
     * 更新状态
     * @param int $groupId
     * @param int $status
     * @return array
     */
    public function updateStatus(int $groupId, int $status)
    {
        $result = self::httpPost(self::getUrl($this->config, $this->config['group']['update_status']), [
            'status' => $status,
            'group_id' => $groupId,
        ]);
        return self::returnMsg($result);
    }

    /**
     * 获取群二维码
     * @param $groupId
     * @param $chatroom
     * @return array
     */
    public function getQrcode($groupId, $chatroom)
    {
        $result = self::httpGet(self::getUrl($this->config, $this->config['group']['qrcode']), [
            'wxid' => $chatroom,
            'group_id' => $groupId,
        ]);
        return self::returnMsg($result);
    }

    /**
     * 刷新远程群
     * @param int $assistantId
     * @param string $wxId
     * @param int $page
     * @param int $limit
     * @param string $name
     * @param string $action
     */
    public function refleshChatroom(
        int $assistantId, string $wxId,
        int $page, int $limit = 5,
        string $name = '', string $action = '')
    {
        $result = $this->getChatroom($assistantId, $wxId, $page, $limit, $name, $action);
        if($result['status'] == 0){
            return self::result(0, '暂无数据');
        }
        $totalPage = $result['data']['total_pages'];
        $page = 2;
        while ($page <= $totalPage){
            $result = $this->getChatroom($assistantId, $wxId, $page++, $limit, $name, $action);
            if($result['status'] == 0){
                break;
            }
        }
        return self::result(1, '更新成功');
    }

    /**
     * 删除群接口
     * @param int $robotGroupId 机器人群ID
     * @return array
     */
    public function destroy(int $robotGroupId)
    {
        if($robotGroupId == 0){
            return self::result(0, '参数错误');
        }
        $result = self::httpPost(self::getUrl($this->config, $this->config['group']['destroy']), [
            'group_id' => $robotGroupId,
        ], 'DELETE');
        return self::returnMsg($result);
    }

    /**
     * 群详情
     * @param int $robotGroupId
     */
    public function show(array $options = [], string $handel = 'reflesh')
    {
        if(empty($options)){
            return self::result(0, '参数错误');
        }
        if(isset($options['assistant_id']) && $options['assistant_id']){
            $options['wxid'] = RobotAssistant::where('id', $options['assistant_id'])->value('wx_id');
        }
        $result = self::httpGet(self::getUrl($this->config, $this->config['group']['show']), [
            'group_id' => $options['robot_group_id']??0,
            'wxid' => $options['wxid']??'',
            'chatroom' => $options['chatroom']??'',
            'handel' => $handel,
        ]);
        if($result['status'] == 1){
            if(isset($options['id']) && $options['id']) {
                event(new RobotGroupUpFieldEvent($options['id'], 'name', $result['data']['nickname']));
                event(new RobotGroupUpFieldEvent($options['id'], 'head_pic', $result['data']['avatar']));
            }
            if(isset($options['wxid'])
                && isset($options['chatroom'])
                && $result['data']){
                event(new RobotAssistantChatroomUpFieldEvent(
                    ['wx_id' => $options['chatroom']],
                    [
                        'nickname' => $result['data']['nickname'],
                        'head_pic' => $result['data']['avatar'],
                    ]
                ));
            }
        }
        return self::returnMsg($result);
    }
}