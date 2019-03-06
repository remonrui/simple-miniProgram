<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 16:23
 */

namespace app\api\controller\v1;


use app\api\controller\Base;
use app\common\model\Like;
use app\common\model\MessageUserCategory;
use app\common\model\Message;

class My extends Base
{
    /**
     * 我的收藏
     * @return \think\response\Json
     */
    public function myLike()
    {
        $like = Like::getAllLikes();
        $return = [];
        foreach ($like as $value){
            $return[] = ($value->category);
        }
        return json($return,200);
    }


    public function myMessage()
    {
        $like = MessageUserCategory::getAllMessage();
        $return = [];
        foreach ($like as $value){
           $value['category'] = $value->category->title;
           $value['message'] = $value->message->msg;
           $m_id = $value['m_id'];
           $value['reply'] =Message::getReply($m_id);
           $return[] = $value;
        }
        return json($return,200);
    }
}