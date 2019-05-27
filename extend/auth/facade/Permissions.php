<?php
namespace auth\facade;
use think\Facade;

/**
 * 权限类门面
 */
class Permissions extends Facade
{
	
	protected static function getFacadeClass()
    {
    	return 'auth\src\Auth';
    }
}
