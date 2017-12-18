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

/**
 * 用户接口
 */
//Route::get('api/user/all','api/User/getAllUsers');
//Route::get('api/user/page/[:page]/[:pageSize]','api/User/getPageForUser');
//Route::get('api/user/:id','api/User/getUserById');
Route::post('api/user/register','api/User/register');
Route::post('api/user/login','api/User/login');
Route::post('api/user/profile','api/User/profile');
Route::get('api/user/friends/:userId/[:page]/[:pageSize]','api/User/getFriendsForPage');

/**
 * 电视接口
 */
Route::get('api/tv/page/:typeId/[:page]/[:pageSize]','api/Television/getTvForPageByType');
Route::post('api/tv/search','api/Television/getTvForPageBySearch');
Route::get('api/tv/province/:code/[:page]/[:pageSize]','api/Television/getTvForPageByProvince');
Route::get('api/tv/prop/:typeId/[:page]/[:pageSize]','api/Television/getTvByProperty');
Route::get('api/tv/index','api/Television/getTvForIndex');
Route::post('api/tv/uploadbg','api/Television/uploadbg');
Route::get('api/tv/updatehit/:id','api/Television/updateHit');
Route::get('api/tv/record/:userId/:typeId/[:page]/[:pageSize]','api/Television/getTvByUserIdAndTypeId');
Route::post('api/tv/delrecord','api/Television/deleteTvByUserIdAndTypeId');
Route::get('api/tv/all','api/Television/getalltv');
Route::post('api/tv/collect/:type_id/:userId/:tv_id','api/Television/addTVByUserId');
Route::get('api/tv/program/:date/:class','api/Television/getProgram');

/**
 * 评论接口
 */
Route::get('api/comment/page/:typeId/:uid/[:page]/[:pageSize]','api/Comment/getCommentForPage');
Route::post('api/comment/add','api/Comment/addComment');

/**
 * 视频接口
 */
Route::post('api/video/upload','api/Video/uploadVideo');
Route::get('api/video/page/[:page]/[:pageSize]','api/Video/getVideoForPage');

/**
 * 微话题接口
 */
Route::post('api/chat/add','api/Chat/addChat');
Route::get('api/chat/page/[:typeId]/[:userId]/[:page]/[:pageSize]','api/Chat/getChatForPage');

/**
 * 地区接口
 */
Route::get('api/region/provinces','api/Region/getProvinces');

Route::get('api/notice/page/[:page]/[:pageSize]','api/Notice/getNoticeForPage');


return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],


];
