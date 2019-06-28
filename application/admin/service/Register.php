<?php
namespace app\admin\service;
use Session;
use Config;
use Cookie;
use Db;
// +----------------------------------------------------------------------
// | 注册/登录类库
// +----------------------------------------------------------------------
// | Author: asuma(lishuaiqiu) <sqiu_li@163.com>
// +----------------------------------------------------------------------
// | Time: 2019-05-08
// +----------------------------------------------------------------------
class Register 
{
	protected $key = '';
	protected $cookie_key = '';

	public function __construct()
	{
		$this->key = Config::get('auth_key');
		$this->cookie_key = Config::get('auth_key_incookie');
	}

	/**
	*  判断用户登录状态
	*/
	public function isLogined()
	{
		if(Session::has($this->key.'.uid')){
			return true;
		}else{
			$user = $this->getUserInfoFromCookie();
			return $user ? $this->login($user) : false;
		}
	}

	/**
	*  验证登录/注册
	*  @param $post 表单提交信息
	*  @param $boolval 为true返回值布尔类型
	*  @param $loginUser 用户信息，facade方法使用会报致命错误
	*  成功返回用户信息
	*/
	public function check(array $post, bool $boolval = false, &$loginUser = [])
	{
		$validate = new \app\admin\validate\Register;
		if(!$validate->scene('login')->check($post)){
			exit(res_json_str(-1, $validate->getError()));
		}

		$loginUser = Db::table('admin_user')->field('id,name,login_name,phone,email,password,head_img,status')->where('login_name|email|phone', '=', $post['username'])->where('status' ,'<>', -1)->findOrEmpty();

		empty($loginUser) && exit(res_json_str(-2, '账号不存在'));
		md5safe($post['password']) != $loginUser['password'] && exit(res_json_str(-3, '密码错误'));

		$loginUser['status'] == -2 && exit(res_json_str(-4, '账号已被冻结'));
		$loginUser['status'] == 0 && exit(res_json_str(-5, '账号正在审核'));

		return $boolval ? true : $loginUser;
	}

	/**
	*  注册用户的登录状态
	*  @param $user 用户信息
	*  @param $remembered 是否记住登录状态
	*/
	public function login(array $user, $remembered=false)
	{
		if(empty($user)){
			return false;
		}

		$key = $this->key;
		Session::set("$key.uid", $user['id']);
		Session::set("$key.uname", $user['name']);
		Session::set("$key.ulogin", $user['login_name']);

		//记住登录状态
		if($remembered){
			$expire = 24*60*60; //登录状态最长有效时间为24小时
			$pwd = rand_str(4).$user['password'].rand_str(4);
			$cookie_value = i_base64encode($user['id'].'_'.$user['login_name'].'_'.$pwd);
			Cookie::set($this->cookie_key, $cookie_value, $expire);
		}

		return $user;
	}

	/**
	*  从cookie读取用户信息
	*/
	public function getUserInfoFromCookie()
	{
		if(Cookie::has($this->cookie_key)){
			$userCookie = i_base64decode(Cookie::get($this->cookie_key));
			$secret = explode('_', $userCookie);
			
			if(count($secret) != 3){
				return false;
			}

			static $user = [];
			if(!isset($user['id'])){
				$pwd = substr($secret[2], 4, -4);
				$user = Db::table('admin_user')->field('id,name,login_name,head_img')->where('id', '=', $secret[0])->where('login_name', '=', $secret[1])->where('password','=', $pwd)->findOrEmpty();
			}

			return $user ?: false;
			
		}

		return false;
	}

	/**
	*  注销登录
	*/
	public function logout()
	{
		//清除Session
		Session::delete($this->key);
		//清除Cookie
		Cookie::delete($this->cookie_key);
	}
}