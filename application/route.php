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
Route::post('api/user/register','api/User/register');
Route::get('api/user/login','api/User/login');
Route::post('api/user/profile','api/User/profile');


Route::get('api/tv/page/:typeId/[:page]/[:pageSize]','api/Television/getTvForPageByType');
Route::get('api/tv/province/:code/[:page]/[:pageSize]','api/Television/getTvForPageByProvince');
Route::get('api/tv/prop/:typeId/[:page]/[:pageSize]','api/Television/getTvByProperty');
Route::get('api/tv/index','api/Television/getTvForIndex');
Route::post('api/tv/uploadbg','api/Television/uploadbg');
Route::get('api/tv/updatehit/:id','api/Television/updateHit');
Route::get('api/tv/record/:userId/:typeId/[:page]/[:pageSize]','api/Television/getTvByUserIdAndTypeId');
Route::get('api/tv/all','api/Television/getalltv');

Route::get('api/comment/page/:typeId/:uid/[:page]/[:pageSize]','api/Comment/getCommentForPage');
Route::post('api/comment/add','api/Comment/addComment');

Route::post('api/video/upload','api/Video/uploadVideo');
Route::get('api/video/page/[:page]/[:pageSize]','api/Video/getVideoForPage');

Route::post('api/chat/add','api/Chat/addChat');
Route::get('api/chat/page/[:typeId]/[:userId]/[:page]/[:pageSize]','api/Chat/getChatForPage');

Route::get('api/region/provinces','api/Region/getProvinces');


return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],


];
