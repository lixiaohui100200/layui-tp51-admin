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

    public function panel()
    {
    	return view();
    }

    public function test()
    {
        return view();
    }

    public function ueditor($action="")
    {
        if($action == ""){
            return view('public/ueditor');
        }else{
            echo $action;    
        }
    }

    public function emptyAuth($msg="还没有获得一些权限")
    {
    	return view('/public/error', ['icon' => '#xe6af', 'error' => $msg]);
    }
}
