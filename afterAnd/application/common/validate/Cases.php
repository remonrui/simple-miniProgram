<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 15:33
 */

namespace app\common\validate;


class Cases extends BaseValidate
{
    protected $rule = [
        'title' =>  'require',
        'img' =>  'require',
    ];

    protected $message = [
        'title' =>  '案例名称必须添加',
        'img' =>  '图片必须添加',
    ];
}