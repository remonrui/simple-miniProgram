<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/8
 * Time: 10:20
 */

namespace app\common\validate;


class User extends BaseValidate
{
    protected $rule = [
        'username'  =>  'require|min:4|max:25|unique:user|alphaDash',
        'email' =>  'require|email|unique:user',
        'mobile' => ['regex'=>'/^1\d{10}$/'],
        'password' => 'require|min:6'
    ];

    protected $message = [
        'username.require'  =>  '用户名必须',
        'username.min'  =>  '用户名长度不能小于4',
        'username.alphaDash'  =>  '用户名必须为字母和数字，下划线_及破折号-。',
        'username.unique'  =>  '用户名已经存在',
        'email.require' =>  '邮箱不能为空',
        'email.email' =>  '邮箱格式错误',
        'email.unique' =>  '邮箱已注册',
        'password.min' => '密码至少6位',
        'password.require' => '密码不能为空',
        'mobile' =>  '手机格式错误',
    ];

    protected $scene = [
        'add'   =>  ['username','email','mobile'],
        'edit'  =>  ['email','mobile'],
        'password'  =>  ['password']
    ];
}