<?php
namespace app\common\model;

use think\Model;

class Member extends Model
{
    protected $insert = [ 'reg_time', 'reg_ip', 'password', 'status'=>1 ];
    /**
     * 用户权限组等三张表关联
     * @return string|\think\db\Query
     */
    public function roles() {
        return $this->belongsToMany('AuthGroup', 'auth_group_access', 'group_id', 'uid');
    }

    public function lists($status = 1, $order = 'uid DESC', $field = true){
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

    /**
     * 管理员登录
     * @param $id
     * @return bool
     * @throws \think\exception\DbException
     */
    public function login($id){
        /* 检测是否在当前应用注册 */
        $user = $this->get($id);
        if(!$user || $user['status'] != 1) {
            $this->error = '用户不存在或已被禁用！'; //应用级别禁用
            return false;
        }
        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }

    /**
     * 注销当前用户
     */
    public function logout(){
        session(null);
    }

    /**
     * @param $user
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'last_login_time' => time(),
            'last_login_ip'   => get_client_ip(1),
        );
        $this->save($data, ['id'=>$user['id']]);
        $this->where('id',$user['id'])->setInc('login');
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'id'              => $user['id'],
            'username'        => $user['nickname'],
            'userid'            =>$user['username'],
            'header'          => $user['header'],
            'last_login_time' => $user['last_login_time'],
            'login' => $user['login'],
        );

        $access = AuthGroupAccess::where('uid='.$user['id'])->find();
        if(!empty($access)) $auth['group_name'] = $access->group->title;

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
    }

    /**
     * 获取用户昵称
     * @param $uid
     * @return mixed
     */
    public function getNickName($uid){
        return $this->where(['id'=>$uid])->value('nickname');
    }

    /**
     * @param $nickName
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkName($nickName){
        $result = $this->where(['nickname'=>$nickName])->find();
        if($result) {
            return false;
        }else {
            return true;
        }
    }

    protected function setRegTimeAttr(){
        return NOW_TIME;
    }

    protected function setRegIpAttr(){
        return get_client_ip(1);
    }

    /**
     * 更新登入信息
     * @param $uid
     */
    public function updateLogin($uid){
        $data = array(
            'last_login_time' => time(),
            'last_login_ip'   => get_client_ip(1),
        );
        $this->where(['id' => $uid])->update($data);
    }
}