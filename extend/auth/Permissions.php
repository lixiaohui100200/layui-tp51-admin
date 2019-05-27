<?php
// +----------------------------------------------------------------------
// | udzanPro [ MAKE THINGS BETTER ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://udzan.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: asuma(lishuaiqiu) <weibo.com/770878450>
// +----------------------------------------------------------------------

namespace auth;

class Permissions 
{
	/**
     * 检查权限
     */
	public function check($name, $uid, $relation = 'or', $type = 1, $mode = 'url')
	{
		static $auth;
		if(!isset($auth) || !is_object($auth)){
			$auth = new \auth\src\Auth();
		}

		return $auth->check($name, $uid, $relation, $type, $mode);
	}
}