<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-10
 * Time: 09:53
 */

namespace Robot\Lib;


use Robot\Events\RobotAssistantLogoutEvent;
use Robot\Events\RobotAssistantSaveEvent;
use Robot\Models\RobotAssistant;

class Login extends Base
{
    /**
     * @var string
     */
    private static $instanceof = null;

    /**
     * @var array
     */
    private $config = [];

    /**
     * Login constructor.
     * @param $config
     */
    private function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 入口
     * @param $config
     * @return string|Login
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
     * 群助手下线
     * @param string $wxId
     */
    public function logout(string $wxId)
    {
        $result = self::httpPost(self::getUrl($this->config, $this->config['login']['logout']), ['wxid' => $wxId]);
        if($result['status'] == 1) {
            event(new RobotAssistantLogoutEvent([
                'wxid' => $wxId
            ]));
        }
        return self::returnMsg($result);
    }

    /**
     * 登录
     * @param int $userId
     * @param string $wxId
     * @param string $uuId
     * @return array
     */
    public function checklogin(int $userId, string $wxId = '', string $uuId)
    {
        if($wxId) {
            $infos = RobotAssistant::where('wx_id', $wxId)->first();
            if (isset($infos->status) && $infos->status == 1) {
                return self::result(1, '已登录');
            }
        }
        $result = self::httpPost(self::getUrl($this->config, $this->config['login']['checklogin']), [
            'wxid' => $wxId,
            'uuid' => $uuId,
            'source' => 'api',
        ]);

        if(config('vbot.log')){
            info('checklogin:', ['result' => $result]);
        }

        if($result['status'] == 1){
            event(new RobotAssistantSaveEvent([
                'data' => $result['data'],
                'infos' => $infos??'',
                'user_id' => $userId,
                'uuid' => $uuId,
                'wxid' => $wxId,
            ]));
        }
        return self::returnMsg($result);
    }
}