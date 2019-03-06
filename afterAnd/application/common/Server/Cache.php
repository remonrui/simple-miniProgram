<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 9:20
 */

namespace app\common\Server;


class Cache
{
    //缓存
    public static function setCache($cacheName,$data,$valid = 0)
    {
        return cache($cacheName,serialize($data),$valid);
    }

    //获取缓存
    public static function getCache($cacheName)
    {
        $cache = unserialize(cache($cacheName));
        if ($cache){
            return $cache;
        }
        return false;
    }

    public static function resetCache($cacheName)
    {
        cache($cacheName,NULL);
    }
}