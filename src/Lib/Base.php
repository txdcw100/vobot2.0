<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-09
 * Time: 09:34
 */

namespace Robot\Lib;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class Base
{
    const ROBOT_INDEX='robot_token:';
    /**
     * 客户端
     * @return Client
     */
    protected static function getClientObj()
    {
        return new Client([
            'debug' => false,
        ]);
    }

    /**
     * post请求
     * @param string $url
     * @param array $options
     * @param bool $isJson
     * @param int $timeout
     * @return bool|mixed
     */
    protected static function httpPost(
        $url = '', $options = [], string $method = 'POST', $timeout = 5.0)
    {
        try{
            $response = self::getClientObj()->request($method, $url, [
                'form_params' => $options,
                'timeout'  => $timeout,
                'headers' => ['token' => self::getToken()]
            ]);
            $body = $response->getBody();
            $result = json_decode($body->getContents(), true);

            if(config('vbot.log')){
                info('robot httpPost:', [
                    'url' => $url,
                    'options' => $options,
                    'result' => $result
                ]);
            }

            if ($result) {
                return $result;
            }
            return false;
        }
        catch (\Exception $e){
            if(config('vbot.log')) {
                info('robot interface httpPost exception:' . $e->getMessage() . ' url:' . $url, $options);
            }
            return false;
        }
    }

    /**
     * @param string $url
     * @param array $options
     * @param float $timeout
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected static function httpGet($url = '', $options = [], $timeout = 5.0)
    {
        try{
            $response = self::getClientObj()->request('GET', $url, [
                'query' => $options,
                'timeout'  => $timeout,
                'headers' => ['token' => self::getToken()]
            ]);
            $body = $response->getBody();
            $result = json_decode($body->getContents(), true);

            if(config('vbot.log')){
                info('robot httpGet:', [
                    'url' => $url,
                    'options' => $options,
                    'result' => $result
                ]);
            }

            if ($result) {
                return $result;
            }
            return false;
        }
        catch (\Exception $e){
            if(config('vbot.log')) {
                info('robot interface httpGet exception:' . $e->getMessage() . ' url:' . $url, $options);
            }
            return false;
        }
    }

    /**
     * url拼接
     * @param $config
     * @param $model
     * @param $method
     * @return string
     */
    protected static function getUrl(array $config, $apiUrl)
    {
        return $config['url'] . $apiUrl;
    }

    /**
     * 获取token
     * @param array $config
     */
    protected static function setToken(array $config)
    {
        $key = self::ROBOT_INDEX . config('vbot.appid');
        if(!Cache::get($key)) {
            $result = self::httpPost(self::getUrl($config, $config['login']['token']), [
                'app_id' => $config['appid'],
                'app_secret' => $config['appsecret'],
            ]);
            if ($result['status'] == 1) {
                Cache::put($key, $result['data']['access_token'], now()->addSeconds(86400));
            }
        }
    }

    /**
     * @return mixed
     */
    protected static function getToken()
    {
        return Cache::get(self::ROBOT_INDEX . config('vbot.appid'));
    }

    /**
     * @param $status
     * @param $message
     * @param null $datas
     * @return array
     */
    protected static function returnMsg($result)
    {
        if($result['status'] == 1){
            return self::result(1, 'ok', $result['data']);
        }
        return self::result(0, $result['message'], $result['data']);
    }

    /**
     * @param $status
     * @param $message
     * @param $datas
     * @return array
     */
    protected static function result($status, $message, $datas = null)
    {
        return ['status' => $status, 'message' => $message, 'data' => $datas];
    }
}