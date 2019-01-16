<?php
namespace app\index\controller;
use Think\Db;
use util\Redis;

class Index
{
    public function index()
    {
    	echo "ok index";
    }

     public function hello()
    {
        $name = session('name');
        return 'hello,' . $name . '! <br/><a href="/index/index/restore">点击回到来源地址</a>';
    }

    public function restore()
    {
        // 设置session标记完成
        session('complete', true);
        // 跳回之前的来源地址
        return redirect()->restore();
    }

    public function getr()
    {
        //dump(Redis::hDel('wj_user_ding', 'openidYiPoCnMJWk19Dork4r4KcagiEiE'));
        dump(Redis::hGetall('wj_user_ding'));
    }

    public function my()
    {
        $my = session('ding.openid');
        $phone = Redis::hGet('wj_user_ding', 'openid'.$my);
        // echo "<h1>$my</h1>";
        echo "<h1>$phone</h1>";
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