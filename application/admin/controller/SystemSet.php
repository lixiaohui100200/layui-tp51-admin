<?php
namespace app\admin\controller;
use auth\facade\Permissions;
/**
 * 
 */
class SystemSet extends Base
{
	public function userInfo()
	{
		// dump($this->request->uid);die;
		// $userInfo = Permissions::getUserInfo($this->request->uid);
		// dump($userInfo);die;
		return $this->fetch();
	}

	public function notice()
	{
		echo "消息通知功能正在建设";
	}
}