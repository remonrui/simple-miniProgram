<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/8
 * Time: 10:46
 */

namespace app\common\model;


use think\Model;

/*
 * 邮件队列模型*/
class MailList extends Model
{
    /*数据库单例连接*/
    protected static $instance;

    /*自动写入时间戳*/
    protected $autoWriteTimestamp = true;
    /*关闭更新时间*/
    protected $updateTime = false;

    protected $params =[
        \PDO::ATTR_PERSISTENT   => true,
        \PDO::ATTR_CASE         => \PDO::CASE_LOWER,
    ];

    /*连接单例*/
    public static function getInstance()
    {
        if(!(self::$instance instanceof self)){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /*
     * 获取未发送邮箱
     * */
    public static function mailNoActive()
    {
       $map =[
           'active'=>0,
       ];

       return self::getInstance()->where($map)->find();
    }

    /*
     * 获取某个验证
     * */
    public static function checkId($id)
    {
        $model = new self();

        $map =[
            'id'=>$id
        ];
        return $model->where($map)->find();
    }

    /*
     * token*/
    public function setTokenAttr($value){
        return random($value);
    }
}