<?php
namespace app\admin\controller;

use app\common\model\User;
use app\common\model\Order;
class Index extends Admin {

    public function index() {
        $userNumber =User::UserNumber();

        $admin = session('user_auth');

        $login_time = $admin['login'];

        $this->assign([
            'user'=>$userNumber,
            'login'=>$login_time
        ]);
        return $this->fetch();
    }

    public function search()
    {
        return '';
    }

}