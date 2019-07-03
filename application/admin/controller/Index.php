<?php
namespace app\admin\controller;
use auth\facade\Permissions;

class Index
{
    public function index()
    {
    	$rules = Permissions::getMenu(request()->uid);

        $tree = new \util\Tree($rules);
        $menu = $tree->leaf();
    	
        return view('', ['menu' => $menu, 'nickname' => request()->uname]);
    }

    /**
    * 仅供Layui上传接口调用，不做实际上传
    * 通过base64实现与form表单同步提交
    */
    public function layuiUpload()
    {
        return res_json(100, 'layui虚拟调用，未做实际上传');
    }

    /**
    * 百度UEditor请求方法重写
    */
    public function ueditor($action="")
    {
        if($action == ""){
            return view('/public/error', ['icon' => '#xe6af', 'error' => '非法请求']);
        }else{
            return \ueditor\facade\UEditor::action();
        }
    }

    /**
    * 空权限时的访问页面
    */
    public function emptyAuth($msg="还没有获得一些权限")
    {
    	return view('/public/error', ['icon' => '#xe6af', 'error' => $msg]);
    }
}
