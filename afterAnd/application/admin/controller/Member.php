<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\model\AuthGroup;
use app\common\model\AuthGroupAccess;
use app\common\model\Member as MemberModel;
use think\Config;

/**
 * 后台用户控制器
 */
class Member extends Admin
{
    /**
     * 用户管理首页
     */
    public function index(){
        $nickname       =  input('nickname');
        $map['status']  =  ['egt', 0];
        if(is_numeric($nickname)) {
            $map['id|nickname'] = [intval($nickname), ['like','%'.(string)$nickname.'%'], '_multi'=>true];
        } else {
            $map['nickname'] = ['like', '%'.(string)$nickname.'%'];
        }
        $list = MemberModel::where($map)->paginate();
        int_to_string($list);
        $this->assign('list', $list);

        $authGroups = AuthGroup::where('status', 1)->select();
        $this->assign('authGroups', $authGroups);

        return $this->fetch();
    }

    /**
     * 修改密码初始化
     */
    public function updatePassword(){
        $member = MemberModel::get(UID);
        $oldPassword = input('oldPassword');
        $newPassword = input('newPassword');
        $rePassword = input('rePassword');
        if(think_ucenter_md5($oldPassword, config('uc_auth_key')) === $member['password']){
            if(!empty($newPassword) && $newPassword == $rePassword) {
                $member['password'] = think_ucenter_md5($newPassword, config('uc_auth_key'));
                $result = $member->allowField(true)->save();
                if($result) {
                    $this->success('修改成功');
                } else {
                    $this->error($member->getError());
                }
            } else {
                $this->error('新密码不能为空或者新密码和确认密码不一致!');
            }
        } else {
            $this->error('旧密码错误!');
        }
    }

    /**
     * 管理员状态修改
     */
    public function changeStatus($method=null){
        $id = array_unique((array)input('id/a',0));
        if( in_array(Config::get('user_administrator'), $id)){
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] = array('in',$id);
        switch ( strtolower($method) ){
            case 'forbiduser':
                $data = ['status'=> 0];
                break;
            case 'resumeuser':
                $data = ['status'=> 1];
                break;
            case 'deleteuser':
                $data = ['status'=> -1];
                break;
            default:
                $this->error('参数非法');
        }

        $result = MemberModel::where($map)->update($data);
        if($result) {
            // 删除用户时,清理相关权限数据
            if(strtolower($method) == 'deleteuser') {
                AuthGroupAccess::where('uid', 'in', $id)->delete();
            }
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }

    /**
     * 添加用户
     * @return array|void
     */
    public function add() {

        $data = input('post.');
        /* 检测密码 */
        if($data['password'] != $data['rePassword']) {
            $this->error('密码和重复密码不一致！');
        }

        $data['password'] = think_ucenter_md5($data['password'], config('uc_auth_key'));

        if($data['nickname'] == '' ) {
            $data['nickname'] = $data['username'];
        }

        $member = new MemberModel();
        $result = $member->allowField(true)->validate(true)->save($data);
        if($result) {   //注册成功
            AuthGroupAccess::create(['uid'=>$member['id'], 'group_id'=>input('group_id')]);
            $this->success('用户添加成功！');
        } else {
            $this->error($member->getError());
        }
    }

    /**
     * 编辑用户信息
     * @return array|void
     * @throws \think\Exception
     */
    public function edit() {
        if(IS_POST) {
            $password = input('password');
            $rePassword = input('rePassword');
            $email = input('email');
            $data = [
                'id'=> input('id'),
                'email' => $email,
                'nickname' => input('nickname')
            ];

            // 修改密码
            if(!empty($password)) {
                if($password != $rePassword){
                    $this->error('密码和重复密码不一致！');
                }
                if(mb_strlen($password) < 6) {
                    $this->error('密码至少6位！');
                }
                $data['password'] = think_ucenter_md5($password, config('uc_auth_key'));

            }

            // 修改用户权限组
            $access = AuthGroupAccess::where(['uid'=>input('id')])->find();
            if(empty($access)) {
                $accessData = [
                    'uid'=>input('id'),
                    'group_id'=>input('group_id')
                ];
                $resultAuth = AuthGroupAccess::create($accessData);
            } else {
                $resultAuth = AuthGroupAccess::where('uid', input('id'))->update(['group_id'=>input('group_id')]);
            }

            $member = new MemberModel();
            $result = $member->allowField(true)->validate('Member.edit')->isUpdate(true)->save($data);

            if($result || $resultAuth) {
                $this->success('更新成功');
            } elseif($result === 0) {
                $this->error('没有更新内容');
            } else {
                $this->error($member->getError());
            }
        } else {
            $member = MemberModel::get(input('id'));
            if($member) {
                $data = [
                    'id' => $member['id'],
                    'username' => $member['username'],
                    'nickname' => $member['nickname'],
                    'email' => $member['email'],
                    'group_id' => !empty($member->roles[0]) ? $member->roles[0]['id'] : 1
                ];
                $this->success('获取成功', '', $data);
            } else {
                $this->error('获取失败');
            }
        }
    }

    /**
     * 个人信息内容
     * @return mixed
     */
    public function profile() {
        $member = MemberModel::get(UID);
        $this->assign('member', $member);

        return $this->fetch();
    }

    /**
     * 上传头像
     * @return array
     */
    public function updateHeader() {
        $imageStr = input('imageStr');
        $base64 = substr($imageStr, 22);

        $path = File::base64ToImage($base64);
        $result = MemberModel::where('id', UID)->update(['header'=>$path]);
        if($result) {
            $auth = session('user_auth');
            $auth['header'] = $path;
            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));

            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }

    public function update() {
        $member = new MemberModel();
        $result = $member->allowField(true)->validate('Member.edit')->isUpdate(true)->save($_POST);
        if($result) {
            $auth = session('user_auth');
            $auth['username'] = input('nickname');
            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));

            $this->success('更新成功');
        } elseif($result === 0) {
            $this->error('没有更新内容');
        } else {
            $this->error($member->getError());
        }
    }

}