<?php

namespace Robot\Provider;

use Illuminate\Support\ServiceProvider;
use Robot\RobotManager;

class RobotServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者加是否延迟加载.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     * php artisan vendor:publish --force 找到对应的服务编号输入回车
     */
    public function boot()
    {
        //
        $this->publishes([
            __DIR__.'/config/vbot.php' => config_path('vbot.php'),
        ], 'config');

        //数据库文件
        $this->publishes([
            __DIR__.DIRECTORY_SEPARATOR.'migrations' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('robot', function($app){
            return new RobotManager($app['config']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['robot'];
    }
}
