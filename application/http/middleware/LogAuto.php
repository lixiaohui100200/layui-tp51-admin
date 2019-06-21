<?php
// +----------------------------------------------------------------------
// | 权限行为自动记录日志中间件
// +----------------------------------------------------------------------
// | Author: asuma(lishuaiqiu) <sqiu_li@163.com>
// +----------------------------------------------------------------------
// | Time: 2019-06-21
// +----------------------------------------------------------------------
namespace app\http\middleware;
use think\facade\Hook;
use think\Db;

class LogAuto
{
    public function handle($request, \Closure $next)
    {
    	$response = $next($request);

    	$authname = $request->controller().'/'.$request->action();
    	$auth = Db::name('auth_rule')->field('id,name,title,is_logged,remark')->cache(md5($authname), 30*24*60*60, 'auth_rule')->where('name', $authname)->find();

    	if($auth['is_logged'] == 1){
    		Hook::listen('admin_log', [$auth['title'], $auth['remark']]); 
    	}
    	
    	return $response;
    }
}
