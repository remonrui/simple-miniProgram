<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 16:12
 */

namespace app\common\model;


use think\Model;

class User extends  Model
{
    /**
     * 用户是否存在
     * 存在返回uid，不存在返回0
     */
    public static function getByOpenID($openid)
    {
        $user = User::where('open_id', '=', $openid)->find();
        return $user;
    }

    /*
     * 更新用户信息
     * */
    public static function updateUserInfo($data){
        $model = new self();
        $model->where('open_id',$data['openId'])->update([
            'nickname'=>$data['nickName'],
            'sex'=>$data['gender'],
            'avatar'=>$data['avatarUrl'],
        ]);
    }

    /*
     * 有效用户数
     * */
    public static function UserNumber()
    {
        $model = new self();
        $map =[
            'status' =>1,
        ];
        return $model->where($map)->count('*');
    }


}