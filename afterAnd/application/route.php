<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

//admin/base
Route::get('/','admin/base/login');
Route::get('admin-login','admin/base/login');
Route::post('admin-login','admin/base/login');


//Category
// 正则匹配区别id和all，注意d后面的+号，没有+号将只能匹配个位数
//Route::get('api/:version/category/:id', 'api/:version.Category/getCategory',[], ['id'=>'\d+']);
//Route::get('api/:version/category/:id/products', 'api/:version.Category/getCategory',[], ['id'=>'\d+']);
Route::get('api/:version/category/main', 'api/:version.Category/getCategoryMain');
Route::get('api/:version/category/son', 'api/:version.Category/getCategorySon');
Route::get('api/:version/category/detail', 'api/:version.Category/getCategoryDetail');
Route::get('api/:version/category/like', 'api/:version.Category/getCategoryLike');
Route::get('api/:version/category/changeLike', 'api/:version.Category/categoryChangeLike');
Route::get('api/:version/category/message', 'api/:version.Category/categoryMessage');

//Carousel
Route::get('api/:version/carousel/all', 'api/:version.Carousel/getCarousel');

//Cases
Route::get('api/:version/cases/all', 'api/:version.Cases/getCases');

//Token
Route::post('api/:version/token/get', 'api/:version.Token/getToken');
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');
Route::post('api/:version/token/info', 'api/:version.Token/userInfo');

//My
Route::get('api/:version/my/like', 'api/:version.My/myLike');
Route::get('api/:version/my/message', 'api/:version.My/myMessage');