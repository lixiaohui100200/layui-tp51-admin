<?php
namespace app\index\controller;
use Db;
use Request;
use util\Redis;

class Ding extends Base
{
	protected $middleware = [
		'WjUserFirstLogin' => ['except' => ['wxUserFirstLogin', 'checkPhone', 'markLinkLogin']]
	];

	public function index(){
		echo 'ok';
	}

	public function openWeijinShop()
	{
		if($this->wx_openid){
			$wx_openid = 'W'.i_base64encode(strrev($this->wx_openid)).'J';
			$redirect_url = 'https://v1.91zlian.com/shop/weijin?ddcode='.$wx_openid;
			header('location:'.$redirect_url, TRUE, 301);
		}else{
			return $this->defaultTpl('看到这里请联系管理员', 'success');
		}
	}

	public function topping()
	{
		$this->assign('user', $userInfo=[]);
		$this->assign('top8', $top8=[]);
		return $this->fetch('top');
		$this->wx_openid && $userInfo = Db::transaction(function(){
			Db::query('SET @x = 0');
			$user = Db::query('SELECT a.*,b.headimgurl FROM (SELECT open_id,name,remain_points,@x:=@x+1 as rownum FROM extra_source_user WHERE unid = :unid ORDER BY remain_points DESC)a LEFT JOIN web_chat_user b ON a.open_id=b.mp_openid AND b.unid = :unid_out WHERE a.open_id = :open_id', ['unid' => $this->unid, 'unid_out' => $this->unid, 'open_id' => $this->wx_openid]);
			return $user[0];
		});

		if(empty($userInfo)){
			return $this->defaultTpl('貌似出现一些错误', 'little');
		}

		$top8 = Db::table("extra_source_user")->alias('eu')->field('eu.id,eu.open_id,eu.nickname,eu.name,eu.remain_points,wu.headimgurl')->leftjoin('web_chat_user wu', 'eu.open_id = wu.mp_openid AND wu.unid = '.$this->unid)->where('eu.unid' , '=', $this->unid)->order('eu.remain_points', 'DESC')->limit(8)->selectOrFail();

		$this->assign('user', $userInfo);
		$this->assign('top8', $top8);
		return $this->fetch('top');
	}

	public function wxUserFirstLogin(Request $request)
    {
        return view('ding/firstlogin');
    }

	public function checkPhone()
	{
		$phone = $this->request->post('phone');
		!checkPhone($phone) && exit($this->res_json('-1', '手机号不正确'));

		$user = Db::table('extra_source_user')->field('id,name')->where(['phone' => $phone, 'unid' => $this->unid])->find();
		empty($user) && exit($this->res_json('-1', '该手机号尚未注册微金小店'));

		exit($this->res_json('100', $user));
	}

	public function markLinkLogin()
	{
		$post = $this->request->post();
		exit(json_encode($post));
	}

	/**
	* 已废弃
	*/
	public function openWeijinShopThird()
	{
		$takeContent = httpGet('http://bak.30vi.cn/urlx.php?url=https://v1.91zlian.com/shop/weijin');
        $takeContent = strstr($takeContent, '<script>location.href=');
        $needPart = trim(str_replace(['<script>location.href="', '" </script>', '</body>', '</html>'], '', $takeContent));
        if(empty($needPart)){
        	exit('由于微信平台限制，app暂时不可跳转');
        }

	    $userAgent = input('server.HTTP_USER_AGENT');
		if(stripos($userAgent, 'iPhone')){
	        header('location:'.$needPart);
		}else if(stripos($userAgent, 'ali')){
			return view('openwx', [
			    'redirect'  => 'ali'
			]);
		}else{
			return view('openwx', [
			    'redirect'  => $needPart
			]);
		}
	}
}