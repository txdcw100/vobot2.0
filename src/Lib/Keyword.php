<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-09
 * Time: 09:30
 */

namespace Robot\Lib;


use Illuminate\Support\Str;
use Robot\Events\RobotAssistantChatroomUpFieldEvent;
use Robot\Events\RobotAssistantChatroomEvent;
use Robot\Events\RobotGroupSaveEvent;
use Robot\Events\RobotGroupUpFieldEvent;
use Robot\Models\RobotAssistant;
use Robot\Models\RobotKeyword;

class Keyword extends Base
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
     * 添加群
     * @param array $groups
     * @param int $cateId
     * @return array
     */
    public function store(string $robotGroupids, array $options)
    {
        info('keyword', ['$robotGroupids' => $robotGroupids]);
        $result = self::httpPost(self::getUrl($this->config, $this->config['keyword']['store']), [
            'group_id' => $robotGroupids,
            'title' => $options['title'],
            'keyword' =>[
                'type' => $options['keyword']['type'],
                'title' => $options['keyword']['title'],
            ] ,
            'type' => $options['type'],
            'txt' => $options['txt'],
            'url' => $options['url'],
            'thumb' => $this->_getThumb($options['thumb']??null),
            'user_id' => $options['user_id'],
            'tenant_id' => $options['tenant_id'],

        ]);
        return self::returnMsg($result);
    }

    private function _getThumb($thumb = null)
    {
        if(empty($thumb)) return '';
        return Str::contains($thumb, ['http', 'https']) ? $thumb : config('app.url') . $thumb;
    }


    /**
     * @param int $keywordId
     * @param $status
     * @return array
     */
    public function operate(int $keywordId, $status)
    {
        $result = self::httpPost(self::getUrl($this->config, $this->config['keyword']['operate']), [
            'keyword_id' => $keywordId,
            'status' =>$status
        ]);
        return self::returnMsg($result);
    }

    /**
     * @param int $keywordId
     * @return array
     */
    public function delete(int $keywordId)
    {
        $result = self::httpPost(self::getUrl($this->config, $this->config['keyword']['delete']), [
            'keyword_id' => $keywordId
        ]);
        return self::returnMsg($result);
    }

    /**
     * 添加群
     * @param array $groups
     * @param int $cateId
     * @return array
     */
    public function update(string $robotGroupids, array $options)
    {
        $keywordId = RobotKeyword::find($options['id'])->robot_keyword_id;
        $result = self::httpPost(self::getUrl($this->config, $this->config['keyword']['update']), [
            'id' => $keywordId,
            'group_id' => $robotGroupids,
            'title' => $options['title'],
            'keyword' =>[
                'type' => $options['keyword']['type'],
                'title' => $options['keyword']['title'],
            ] ,
            'type' => $options['type'],
            'txt' => $options['txt'],
            'url' => $options['url'],
            'thumb' => $this->_getThumb($options['thumb']??null),
            'user_id' => $options['user_id'],
            'tenant_id' => $options['tenant_id'],

        ]);
        return self::returnMsg($result);
    }
}