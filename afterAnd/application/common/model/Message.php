<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/30
 * Time: 10:22
 */

namespace app\common\model;


use think\Model;
class Message extends Model
{
    public function connection()
    {
        return $this->hasOne('MessageUserCategory','m_id','id');
    }

    //创建留言
    public static function createMessage($msg)
    {
        $model = new self();
        $data = [
            'msg'=>$msg,
            'time'=>NOW_TIME,
            'type'=>1
        ];
        $result = $model->validate(true)->save($data);
        if ($result){
            return $model->id;
        }
        return false;
    }

    //所有留言
    public static function allMessage()
    {
        $model = new self();
        $map = [
            'p_id'=>0,
            'type'=>1,
            'status'=>['gt',0]
        ];

        return $model->where($map)->order('time desc')->paginate();
    }

    //获取留言回复
    public static function getReply($id)
    {
        $map =[
            'p_id'=>$id,
            'type'=>2,
            'status'=>['gt',0],
        ];
        $reply =  self::where($map)->find();
        return $reply['msg'];
    }

    //获取单条留言及回复
    public static function getMessage($id)
    {
        $map =[
            'id'=>$id,
        ];
        $data['msg'] = self::where($map)->field('id,msg')->find();
        $data['reply'] = self::getReply($id);

        return $data;
    }

    //获取单条状态留言
    public static function getMessageStatus($id)
    {
        $model = new self();
        $map =[
            'id'=>$id,
        ];
        $status = $model->where($map)->field('status')->find();
        return $status->getData();
    }


    //留言回复
    public static function replyMessage($data)
    {
        $model = new self();
        $msg = [
            'p_id'=>$data['id'],
            'msg'=>$data['msg'],
            'time'=>NOW_TIME,
            'type'=>2
        ];
        $result = $model->validate(true)->save($msg);
        if ($result){
            $change = self::changeStatus($data['id'],2);
            if ($change){
                return 1;
            }else{
                return '留言失败';
            }
        }else{
            return $model->getError();
        }
    }

    /*
     * 改变留言状态*/
    public static function changeStatus($id,$status)
    {
        $model = new self();

        return $model->where('id',$id)->update(['status'=>$status]);
    }

    //转换时间
    public function getTimeAttr($value)
    {
        return time_date($value);
    }

    //转换类型
    public function getStatusAttr($value)
    {
        $status = [1=>'未回复',2=>'已回复'];
        return $status[$value];
    }
}