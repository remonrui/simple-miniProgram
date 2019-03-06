<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/31
 * Time: 11:04
 */

namespace app\common\validate;


class Message extends BaseValidate
{
    protected $rule = [
        'msg' =>  'require',
    ];

    protected $message = [
        'msg' =>  '留言内容必须',
    ];
}