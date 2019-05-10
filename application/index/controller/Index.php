<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
    	echo "ok index";
    }

    public function test()
    {
        $data = \think\facade\Request::post();
        if(checkFormToken($data)){
            
            //code...
            
            destroyFormToken($data);
            exit('1');
        }

        exit('-1');
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

    /**
    * 默认页渲染模板
    */
    public function defaultIndex()
    {
        return view('public/tips', ['type' => 'success', 'code' => '登录成功，请退出本页面']);
    }
}