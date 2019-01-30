<?php
namespace app\index\controller;
use Think\Db;
use util\Redis;

class Index
{
    public function index()
    {
        session('wechat.original', ['city' => 44]);

        dump(session('wechat')['original']['city'] ? '|'.session('wechat')['original']['city']: '');
    	echo "ok index";
    }

    public function getr()
    {
        //dump(Redis::hDel('wj_user_ding', 'openidYiPoCnMJWk19Dork4r4KcagiEiE'));
        dump(Redis::hGetall('wj_user_ding'));
    }

    /**
    * 跳回上一个页面
    * @param $default 默认跳转的url，可接受get中的default
    */
    public function redirectLast($rd_url="defaultIndex")
    {
        session('from_redirect', true);
        return redirect()->restore($rd_url);
    }

    public function defaultIndex()
    {
        return view('public/tips', ['type' => 'success', 'code' => '登录成功，请退出本页面']);
    }
}