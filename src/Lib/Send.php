<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-09
 * Time: 09:30
 */

namespace Robot\Lib;
use Illuminate\Support\Str;

class Send extends Base
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

    public function message(string $robotGroupids, array $options)
    {
        $thumb = !empty($options['thumb']) ? Str::contains($options['thumb'], ['http', 'https']) ? $options['thumb'] : config('app.url') . $options['thumb'] :null;
        $result = self::httpPost(self::getUrl($this->config, $this->config['send']['message']), [
            'group_id' => $robotGroupids,
            'title' => $options['title'],
            'type' => $options['type'],
            'txt' => $options['txt'],
            'url' => $options['url'],
            'thumb' => $thumb,
            'channel'=>$options['channel']

        ]);
        return self::returnMsg($result);
    }



}