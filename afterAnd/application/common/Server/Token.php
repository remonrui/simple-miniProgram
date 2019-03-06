<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/10
 * Time: 9:51
 */

namespace app\common\server;
use think\Cache;
use app\common\exception\TokenException;
use think\Request;
/*
 * 令牌
 * */
class Token
{
    //生成令牌
    protected static function generateToken()
    {
        $randChar = random(config('web.order_token_len'));
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $tokenSalt = config('web.key');
        return md5($randChar . $timestamp . $tokenSalt);
    }

    /**
     * 当需要获取全局UID时，应当调用此方法
     *而不应当自己解析UID
     *
     */
    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        if (!$uid)
        {
            throw new ParameterException(
                [
                    'msg' => '没有指定需要操作的用户对象'
                ]);
        }
        return $uid;
    }

    //依据token从缓存拿到用户uid
    private static function getCurrentTokenVar($key)
    {
        $token = Request::instance()
            ->header('token');
        $vars = Cache::get($token);
        if (!$vars)
        {
            throw new TokenException();
        }
        else {
            if(!is_array($vars))
            {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            }
            else{
                throw new Exception('尝试获取的Token变量并不存在');
            }
        }
    }

    //获取令牌
    public static function getToken($token)
    {
        return Cache::get($token);

    }

    //验证令牌
    public static function verifyToken($token)
    {
        $exist = self::getToken($token);
        if($exist){
            return true;
        }
        else{
            return false;
        }
    }
}