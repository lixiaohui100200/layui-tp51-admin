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

Route::get('redirect', 'index/redirectLast'); //记住的跳转地址

Route::group('admin', function(){
	Route::group(['method' => 'get'],[
		'/' => 'admin/index/index'
	]);
	Route::rule('ueditor' , 'admin/index/ueditor');
	Route::rule('layuiUpload', 'admin/index/layuiUpload');
});