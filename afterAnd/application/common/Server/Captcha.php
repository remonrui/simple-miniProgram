<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 11:40
 */

namespace app\common\Server;

use com\captcha\CaptchaClient;

/*
 * 验证码*/
class Captcha
{
    public static function captcha($verify)
    {
        $appId = config('captcha.appId');
        $appSecret = config("captcha.appSecret");
        $client = new CaptchaClient($appId,$appSecret);
        $client->setTimeOut(2);      //设置超时时间
# $client->setCaptchaUrl("http://cap.dingxiang-inc.com/api/tokenVerify");   //特殊情况需要额外指定服务器,可以在这个指定，默认情况下不需要设置
        $response = $client->verifyToken($verify);
//        echo $response->serverStatus;
//确保验证状态是SERVER_SUCCESS，SDK中有容错机制，在网络出现异常的情况会返回通过
        if($response->result){
            return true;
            /**token验证通过，继续其他流程**/
        }else{
            return false;
            /**token验证失败**/
        }
    }
}