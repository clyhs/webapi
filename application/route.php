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

Route::get('api/user/all','api/User/getAllUsers');
Route::get('api/user/page/[:page]/[:pageSize]','api/User/getPageForUser');
Route::get('api/user/:id','api/User/getUserById');


Route::get('api/tv/page/:typeId/[:page]/[:pageSize]','api/Television/getTvForPageByType');
Route::get('api/tv/property/:typeId/[:page]/[:pageSize]','api/Television/getTvByProperty');

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],


];
