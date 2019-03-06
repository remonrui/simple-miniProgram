<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/30
 * Time: 15:59
 */

namespace app\common\model;


use think\Model;
use app\common\server\Token;

class MessageUserCategory extends Model
{

    //分类关联
    public function category()
    {
        return $this->belongsTo('Category', 'c_id', 'id')->field('title');
    }

    //留言关联
    public function message()
    {
        return $this->belongsTo('Message', 'm_id', 'id')->field('msg,time');
    }

    //用户关联
    public function user()
    {
        return $this->belongsTo('User', 'u_id', 'id')->field('nickname');
    }
    //创建留言id
    public static function createMessageConnection($c_id,$m_id)
    {
        $uid = Token::getCurrentUid();
        $model = new self();
        $data = [
            'c_id'=>$c_id,
            'u_id'=>$uid,
            'm_id'=>$m_id,
        ];
        return $model->save($data);
    }

    //所有留言
    public static function getAllMessage()
    {
        $uid = Token::getCurrentUid();
        $model = new self();
        $map = [
            'u_id'=>$uid,
        ];
        return $model->where($map)->field('c_id,m_id')->select();

    }
}