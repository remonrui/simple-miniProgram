<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 12:15
 */

namespace app\api\controller\v1;


use app\api\controller\Base;
use app\common\Server\UserToken;
use app\common\model\User;
use app\common\server\Token as TokenService;
use app\common\exception\ParameterException;
use app\common\Server\WxBizDataCrypt;


class Token extends Base
{
    //拿到token
    public function getToken($code)
    {
        $wx = new UserToken($code);
        $token = $wx->get();
        return json(['token'=>$token],200);
    }

    //token验证
    public function verifyToken($token='')
    {
        if(!$token){
            throw new ParameterException([
                'token不允许为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        return json(['isValid'=>$valid],200);
    }

    //更新用户信息
    public function userInfo()
    {
        $msg = input('post.');
        $cache = json_decode(TokenService::getToken($msg['token']),true);
        if (!$cache){
            return false;
        }
        $sessionKey = $cache['session_key'];

        $appid = config('wx.app_id');

        $encryptedData = $msg['data']['encryptedData'];

        $iv = $msg['data']['iv'];

        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            $userInfo = json_decode($data,true);
            User::updateUserInfo($userInfo);
        }
    }
}