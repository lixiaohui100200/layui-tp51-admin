<?php
namespace app\index\controller;
use think\Controller;
use Session;
use util\Redis;
use Db;

class Base extends Controller
{
	protected $dingName = "";
	protected $dingOpenid = "";
	protected $unid = 91;
	protected $wx_openid = "";

	public function initialize()
	{
		$this->dingName = Session::get('ding.nick');
		$this->dingOpenid = Session::get('ding.openid');
		$this->userPhone = Redis::hGet('wj_user_ding', 'openid'.$this->dingOpenid);

		if(Session::has('user.wx_openid')){
			$this->wx_openid = Session::get('user.wx_openid');
		}else{
			if($this->dingOpenid && $this->userPhone){
				$user = Db::query("SELECT open_id,name,phone FROM extra_source_user WHERE phone = :phone AND unid = :unid LIMIT 1", ['phone' => $this->userPhone, 'unid' => $this->unid]);

				Session::set('user.wx_openid', $this->wx_openid = $user[0]['open_id']);
			}
		}
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