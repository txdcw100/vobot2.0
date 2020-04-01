<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-12
 * Time: 11:31
 */

namespace Robot\Lib;


class Message extends Base
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
     * 消息分页
     * @param int $robotAssistantId
     * @param int $robotGroupId
     * @param array $options
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList(
        int $robotAssistantId,
        int $robotGroupId,
        array $options,
        int $page = 10,
        int $limit = 10)
    {
        $params = [
            'assistant_id' => $robotAssistantId,
            'page' => $page,
            'limit' => $limit,
        ];

        isset($options['name'])&&$params['name'] = $options['name'];
        isset($options['type'])&&$params['type'] = int($options['type']);
        isset($options['start'])&&$params['start'] = $options['start'];
        isset($options['end'])&&$params['end'] = $options['end'];
        isset($options['content'])&&$params['content'] = $options['content'];

        $result = self::httpGet(self::getUrl($this->config, $this->config['message']['list']) .'/'. $robotGroupId, $params);
        return self::returnMsg($result);
    }
}