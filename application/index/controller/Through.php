<?php
namespace app\index\controller;
use Config;
use think\Request;
use util\Redis;
use this\AliSms;

class Through
{
    public function index()
    {
        echo "can pass";
    }

    /**
	*	发送验证码
	*/
	public function sendMsgCode(Request $request)
	{
		$phone = $request->post('phone');
		$msg = rand(100000, 999999);
		$data = array(
            'keyId' => Config::get('this.alisms_key'),
            'keySecret' => Config::get('this.alisms_secret'),
            'phone' => $phone,
            'sign' => '质链SAAS云平台',
            'snscode' => Config::get('this.alisms_snscode')
        );
    	$alisms = AliSms::sendSms($data, ['code' => $msg]);

    	if($alisms->Code == "OK"){
    		Redis::set('loginCode_'.$phone, $msg, 290);
    		exit($this->res_json('100', $alisms->Code));
    	}

    	exit($this->res_json('101', $alisms->Code));
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
}