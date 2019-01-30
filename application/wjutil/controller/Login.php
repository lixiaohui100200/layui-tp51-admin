<?php
namespace app\wjutil\controller;
use think\Controller;
use Db;
use util\Redis;

class Login extends Controller
{
    public function index()
    {
    	echo "login";
    }

    public function autologin()
    {
    	return $this->fetch();
    }

    public function checkPhone()
	{
		$phone = $this->request->post('phone');
		!checkPhone($phone) && exit($this->res_json('-1', '手机号不正确'));

		$user = Db::table('extra_source_user')->field('id,name')->where('unid', 'IN' , '78,91')->where(['phone' => $phone])->find();
		empty($user) && exit($this->res_json('100', '耘和同事您好，请继续录入您的姓名'));

		exit($this->res_json('100', $user));
	}

    public function markLinkLogin()
    {
        $phone = $this->request->post('phone');
        $name = $this->request->post('name');
        empty($phone) && exit($this->res_json('101', '未检测到手机号'));

        Redis::get('loginCode_'.$phone) != $this->request->post('sixcode') && exit($this->res_json('102', '验证码不正确'));;

        $data = [
            'open_id' => session('wechat')['id'],
            'nickname' => session('wechat')['nickname'] ? session('wechat')['nickname'] : $name,
            'name' => $name,
            'phone' => $phone,
            'remain_points' => 0,
            'company' => '耘和',
            'red_money' => 0,
            'position' => session('wechat')['original']['country'].(session('wechat')['original']['province'] ? '|'.session('wechat')['original']['province'] : '').( session('wechat')['original']['city'] ? '|'.session('wechat')['original']['city'] : ''),
            'unid' => 78,
            'ding_open_id' => session('ding.openid'),
        ];

        $result = Db::name('extra_source_user')->strict(false)->insert($data);
        !$result && exit($this->res_json('102', '登录失败'));

        exit($this->res_json('100', ''));
    }

    /**
    *   返回请求结果状态
    */
    public function res_json($code='101', $result='')
    {
        $data = ['code' => $code, 'result' => $result];
        return $this->json($data);
    }

    /**
    *   返回json字符串数据
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
