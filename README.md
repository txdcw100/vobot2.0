# COMPOSE 配置

- "Robot\\":"packages/robot/src/"

## CONFIG 配置

- app.php providers 添加
- Robot\RobotServiceProvider::class,

## Evn 添加接口url

- VBOT_API_URL
- 添加api域名

## Providers\EventServiceProvider subscribe 添加需要监听事件

- Robot\Listeners\RobotAssistantListener::class
- Robot\Listeners\RobotFriendListener::class
- Robot\Listeners\RobotGroupListener::class
- Robot\Listeners\RobotMessageListener::class

## Code Examples
- php artisan vendor:publish --force
- 选择 Robot\RobotServiceProvider 编号
- php artisan migrate 增加需要依赖表

## ROBOT接口调用
- app('robot')->getAssistant()群助手入口
- app('robot')->getGroup()群入口
- app('robot')->getLogin()登录入口
- app('robot')->getFriend()群成员入口
- app('robot')->getMessage()群消息入口

## COMMAND 同步ROBOT微信信息
- App\Console  Kernel $commands 添加 
- Robot\Commands\RsyncRobotMessageCommand::class
- Robot\Commands\RsyncAssistantStateCommand::class
- Robot\Commands\RsyncRobotCateMemberCommand::class
- schedule 添加 
- 同步微信信息 $schedule->command('rsync:robotmessage')->everyMinute();
- 同步群助手状态 $schedule->command('rsync:assistantstate')->everyMinute();
- 同步群分类人数 $schedule->command('rsync:robotcatemember')->hourly()
