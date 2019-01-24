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

	public function inlucky()
	{
		i_log('预留判别开始...');
		$num = Redis::get('lucky');
		if($num){
			Redis::set('lucky', $num = $num+1);
		}else{
			Redis::set('lucky', $num = 1);
		}

		i_log('当前抽奖序号：'.$num);

		$final = false;

		if($num == 18){
			$final = '13776052628';//yy
		}else if($num == 19){
			$final = '15956002040';//wqs
		}else if($num == 15){
			$final = '13616273188';//zq
		}else if($num == 16){
			$final = '13862161983';//hzx
		}else if($num == 12){
			$final = '18662538931';//qq
		}else{
			$final = false;
		}

		if($final === false){
			i_log('预留判别跳过...');
			return true;			
		}else{
			$isexcute = Db::name('extra_source_meeting_sign')->where('sign_num', $final)->update(['is_join_lucky' => 1]);
			if($isexcute > 0){
				i_log('预留判别成功!'.$final);
				i_log('=======================================');
				exit($this->res_json('100', $final));
			}
		}

		i_log('预留判别未知!');
		return true;
	}
}