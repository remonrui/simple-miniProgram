<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\model\Member;
use app\common\model\Config;
use think\Controller;
/**
 * 后台公共控制器
 */
class Base extends Controller
{
    protected function _initialize() {
        /* 读取数据库中的配置 */
        $config = cache('db_config_data');
        if(!$config) {
            $configModel = new Config();
            $config = $configModel->lists();
            cache('db_config_data',$config);
        }
        config($config); //添加配置
    }
    /**
     * 后台用户登录
     */
    public function login($username = null, $password = null, $verify = null) {
        if(IS_POST) {
            $member = Member::where('username', input('username'))->find();
            if(!empty($member) && $member['status']){
                $memberMobile = new Member();
                if(think_ucenter_md5($password, config('uc_auth_key')) === $member['password']){
                    $memberMobile->login($member['id']);
                    $this->success('验证成功', url('index/index'));
                } else {
                    $this->error('帐号或者密码错误!');
                }
            } else {
                $this->error('帐号禁用或者不存在');
            }
        } else {
            if(is_login()){
                $this->redirect('Index/index');
            }else{
                /* 读取数据库中的配置 */
                $config	= cache('db_config_data');
                if(!$config) {
                    $config	= Config::all();
                    cache('db_config_data',$config);
                }
                config($config); //添加配置

                return $this->fetch();
            }
        }
    }

    /* 退出登录 */
    public function logout(){
        $url = config('web.host').'/admin-login.html';
        if(is_login()){
            $member = new Member();
            $member->logout();
            //$this->success('退出成功！', Url::build('login'));
            $this->redirect($url);
        } else {
            $this->redirect($url);
        }
    }
    public function skinconfig() {
        return $this->fetch();
    }
}
