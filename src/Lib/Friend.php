<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-11
 * Time: 23:05
 */

namespace Robot\Lib;


use Robot\Events\RobotFriendDelEvent;

class Friend extends Base
{
    private static $instanceof = null;

    private $config;

    private function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @return Assistant|null
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
     * 删除成员
     * @param int $groupId
     * @param string $to_wxid
     */
    public function deleteMemeber(int $friendId, int $robotGroupId, string $to_wxid)
    {
        $result = self::httpPost(self::getUrl($this->config, $this->config['friend']['destroy']), [
            'group_id' => $robotGroupId,
            'to_wxid' => $to_wxid,
        ], 'DELETE');
        if($result['status'] == 1){
            event(new RobotFriendDelEvent($friendId));
        }
        return self::returnMsg($result);
    }

    /**
     * 群成员
     * @param int $robotGroupId
     * @param int $page
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList(int $robotGroupId, int $page = 1, int $limit = 10, string $handle = '')
    {
        $result = self::httpGet(self::getUrl($this->config, $this->config['friend']['friend']) .'/'. $robotGroupId , [
            'page' => $page,
            'limit' => $limit,
            'handle' => $handle
        ]);
        return self::returnMsg($result);
    }

    /**
     * 直接操作微信
     * @param $wxid
     * @param $chatroom
     * @param $toWxid
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function detach($wxid, $chatroom, $toWxid)
    {
        $result = self::httpPost(self::getUrl($this->config, $this->config['friend']['detach']) , [
            'wxid' => $wxid,
            'chatroom' => $chatroom,
            'to_wxid' => $toWxid
        ]);
        return self::returnMsg($result);
    }
}