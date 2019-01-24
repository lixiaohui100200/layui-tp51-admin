<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('redirect', 'index/redirectLast');
Route::get('open/wx/weijin', 'ding/openWeijinShop');

Route::group('wj', function(){
	Route::group(['method' => 'get'], [
		'depoints/top' => 'ding/topping',
		'wx/firstlogin' => 'ding/wxUserFirstLogin',
		'' => 'wjutil/index/index',
		'login/auto' => 'wjutil/login/autologin',
		'meeting/sign' => 'wjutil/meeting/signIn',
		'lucky' => 'wjutil/meeting/luckyDraw',

		'now' => 'wjutil/index/now',
		'users' => 'wjutil/index/allyusers',
	]);
});

Route::group('do', function(){
	Route::group(['method' => 'post'], [
		'sendmsg' => 'through/sendMsgCode'
	]);
});