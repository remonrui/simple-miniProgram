<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 13:42
 */

namespace app\common\model;


use think\Model;
use app\common\server\Token;

class Like extends Model
{
    public function category()
    {
        return $this->belongsTo('Category', 'c_id', 'id')->field('id,title');
    }

    //收藏详情
    public static function getDetailLike($c_id = 0)
    {
        $uid = Token::getCurrentUid();
        $map = [
            'u_id'=>$uid,
            'c_id'=>$c_id,
        ];
        return self::where($map)->field('status')->find();
    }

    //创建收藏
    public static function createDetailLike($c_id)
    {
        $uid = Token::getCurrentUid();
        $model = new self();
        $data = [
            'c_id'=>$c_id,
            'u_id'=>$uid,
            'time'=>NOW_TIME,
        ];
        return $model->save($data);
    }

    //改变收藏
    public static function changeDetailLike($c_id,$status)
    {
        $uid = Token::getCurrentUid();
        $map = [
            'c_id'=>$c_id,
            'u_id'=>$uid,
        ];
        return self::where($map)->update(['status'=>$status,'time'=>NOW_TIME]);
    }

    //个人所有收藏
    public static function getAllLikes($uid = 0)
    {
        if (!$uid){
            $uid = Token::getCurrentUid();
        }
        $model = new self();
        $map = [
            'u_id'=>$uid,
            'status'=>1,
        ];
        return $model->where($map)->order('id desc')->select();
    }


}