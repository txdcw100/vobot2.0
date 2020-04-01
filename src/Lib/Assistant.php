<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-09
 * Time: 09:26
 */

namespace Robot\Lib;


class Assistant extends Base
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
     * 群助手
     * @param int $page
     */
    public function list(int $page = 1)
    {
        $result = self::httpGet(self::getUrl($this->config, $this->config['assistant']['list']), ['page' => $page]);
        return self::returnMsg($result);
    }

    /**
     * 获取登录二维码
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function qrcode()
    {
        $result = self::httpGet(self::getUrl($this->config, $this->config['login']['qrcode']), []);
        return self::returnMsg($result);
    }

    /**
     * 群助手信息
     * @param string $wxId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function info(string $wxId)
    {
        $result = self::httpGet(self::getUrl($this->config, $this->config['assistant']['info']), [
            'wxid' => $wxId
        ]);
        return self::returnMsg($result);
    }
}