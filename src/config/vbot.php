<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-05
 * Time: 17:11
 */
return [
    'appid' => env('VBOT_APPID', '***'),
    'appsecret' => env('VBOT_APPSECRET', '***'),
    'url' => env('VBOT_API_URL', '****'),
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