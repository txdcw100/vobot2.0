<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-09
 * Time: 09:21
 */

namespace Robot;


use Robot\Lib\Assistant;
use Robot\Lib\Friend;
use Robot\Lib\Group;
use Robot\Lib\Login;
use Robot\Lib\Message;

class RobotManager
{
    const CONFIG_INDEX = 'vbot';

    private $config;

    /**
     * VbotManager constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 群助手
     * @return Assistant|null
     */
    public function getAssistant()
    {
        return Assistant::init($this->config[self::CONFIG_INDEX]);
    }

    /**
     * 群
     * @return Group|null
     */
    public function getGroup()
    {
        return Group::init($this->config[self::CONFIG_INDEX]);
    }

    /**
     * @return string|Login
     */
    public function getLogin()
    {
        return Login::init($this->config[self::CONFIG_INDEX]);
    }

    /**
     * @return Assistant|null
     */
    public function getFriend()
    {
        return Friend::init($this->config[self::CONFIG_INDEX]);
    }

    /**
     * @return Assistant|null
     */
    public function getMessage()
    {
        return Message::init($this->config[self::CONFIG_INDEX]);
    }
}