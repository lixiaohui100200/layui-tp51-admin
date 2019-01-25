<?php
namespace app\wjutil\controller;
use think\Controller;
use Session;
use util\Redis;
use Db;

class Base extends Controller
{
	protected $unid = 91;
	protected $userPhone = "";
	protected $userName = "";
	protected $userId = "";

	public function initialize()
	{
		$this->userPhone = Session::get('user.userphone');
		$this->userName = Session::get('user.name');
		$this->userId = Session::get('user.id');
	}

	/**
	*	返回请求结果状态
	*/
	public function res_json($code='101', $result='')
	{
		$data = ['code' => $code, 'result' => $result];
		return $this->json($data);
	}

	/**
	*	返回json字符串数据
	*/
	public function json($data)
	{
		return json()->data($data)->getContent();
	}

	/**
	* @param $code 错误信息
    * @param type 显示卡通的表情 error 哭；success 笑；little 委屈；pride 撇嘴；surprised 惊讶; none 不显示
    * 缺省页面 lishuaiqiu @2018-12-18
    */
	public function defaultTpl($code="404|页面飞走了！", $type="error")
	{
		return $this->fetch('public/tips', ['type' => $type, 'code' => $code]);
	}
}