<?php
// +----------------------------------------------------------------------
// | QingCMS [ MAKE THINGS BETTER ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://udzan.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: asuma(lishuaiqiu) <weibo.com/770878450>
// +----------------------------------------------------------------------
namespace auth\facade;
use think\Facade;

/**
 * 权限类门面
 */
class Permissions extends Facade
{
	
	protected static function getFacadeClass()
    {
    	return 'auth\Permissions';
    }
}
