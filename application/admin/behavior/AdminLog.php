<?php
namespace app\admin\behavior;
use think\Request;
use think\Db;
use think\Exception;
use think\facade\Log as ThinkLog;
/**
 * 记录权限行为日志
 */
class AdminLog 
{	
	public function run(Request $request, $param)
	{
		try {
			$data['auth_name'] = $request->controller().'/'.$request->action();
			$data['auth_title'] = $param[0];
			$data['auth_desc'] = $param[1];
			$data['ip'] = $request->ip();
			$data['record_time'] = date('Y-m-d H:i:s');

			$userInfo = session(config('auth_key'));
			$data['behavior_user'] = $userInfo['ulogin'];
			
			Db::name('operation_log')->insert($data);

		} catch (Exception $e) {
			ThinkLog::record('权限行为日志记录异常，异常信息：'.$e->getMessage(), 'error');
		}
	}
}