<?php
namespace app\common\behavior;

use think\Request;
class AppInit
{
    public static function run(){
        // 站点初始化
        self::initialization();
    }
    /**
     * 变量
     */
    private static function initialization() {
        // 定义废除的一些常量
        define('NOW_TIME', $_SERVER['REQUEST_TIME']);

        $request = Request::instance();
        define('REQUEST_METHOD', $request->method());
        define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
        define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
        define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
        define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);
        define('IS_AJAX', $request->isAjax());
        define('__EXT__', $request->ext());
    }
    
}