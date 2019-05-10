<?php
namespace app\admin\controller;

use think\Request;
use app\admin\facade\Register;
use Config;
use Session;

class Login 
{
    public function index()
    {
        return view();
    }

    public function checkLogin(Request $request)
    {
        $loginUser = [];
        if(app('register')->check($request->post(), $loginUser)){
            !Register::login($loginUser, $request->post('remembered')) && exit(res_json_str(0, '登录失败'));;
        }
        
        return res_json(1);
    }
    
    public function logout()
    {
        Register::logout();
        return redirect('/admin/login');
    }

    public function register()
    {
        return view();
    }

    public function forget()
    {
        return view();
    }
}