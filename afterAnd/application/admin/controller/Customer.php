<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 10:21
 */

namespace app\admin\controller;

use app\common\model\User;
use app\common\model\Like;
use app\common\model\Message;
class Customer extends Admin
{
    /**
     * 用户管理首页
     */
    public function index(){
        $map['status']  =  ['egt', 0];
        $list = User::where($map)->paginate(12);
        int_to_string($list);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /*
     * 用户收藏*/
    public function getLike($id)
    {
        $like = Like::getAllLikes($id);
        $return = [];
        foreach ($like as $value){
            $return[] = $value->category;
        }

        return $return;
    }

    /*用户留言*/
    public function message()
    {
        $list = Message::allMessage();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /*
     * 获取用户留言*/
    public function getMessage($id)
    {
        $message = Message::getMessage($id);

        return $message;
    }

    /*留言回复*/
    public function reply()
    {
        $data = input('post.');
        $status = Message::getMessageStatus($data['id']);
        if ($status['status'] == 2){
            return $this->error('你已经回复过了');
        }
        $result = Message::replyMessage($data);
        if ($result == 1){
            return $this->success('回复成功');
        }else{
            return $this->error($result);
        }
    }
}
