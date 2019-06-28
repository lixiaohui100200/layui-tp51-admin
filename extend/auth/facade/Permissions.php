<?php
// +----------------------------------------------------------------------
// | QingCMS [ MAKE THINGS BETTER ]
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
