<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21
 * Time: 12:03
 */

namespace app\common\validate;


class Category extends BaseValidate
{
    protected $rule = [
        'title' =>  'require',
    ];

    protected $message = [
        'title' =>  '标题名称必须添加',
    ];
}