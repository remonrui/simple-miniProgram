<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 14:30
 */

namespace app\common\validate;

class Carousel extends BaseValidate
{
    protected $rule = [
        'img' =>  'require',
    ];

    protected $message = [
        'img' =>  '图片必须添加',
    ];
}