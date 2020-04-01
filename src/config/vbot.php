<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-05
 * Time: 17:11
 */
return [
    'appid' => env('VBOT_APPID', '43fcee38a15fa5c2db2803046565b826'),
    'appsecret' => env('VBOT_APPSECRET', '76f689d6f21affc932cf118b1cb45567'),
    'url' => env('VBOT_API_URL', 'http://vobot.cw100.cn/'),
    'login' => [
        'token' => 'api/token',
        'logout' => 'api/login/logout',
        'qrcode' => 'api/login/qrcode',
        'checklogin' => 'api/login/checklogin',
    ],
    'assistant' => [
        'list' => 'api/assistant/list',
        'info' => 'api/assistant/info'
    ],
    'group' => [
        'list' => 'api/group/list',
        'chatroom' => 'api/group/chatroom',
        'store' => 'api/group/store',
        'update_status' => 'api/group/updateStatus',
        'qrcode' => 'api/group/qrcode',
    ],
    'friend' => [
        'friend' => 'api/friend',
        'destroy' => 'api/friend/destroy',
    ],
    'message' => [
        'list' => 'api/message'
    ],
];