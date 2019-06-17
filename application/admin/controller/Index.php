<?php
namespace app\admin\controller;
use auth\facade\Permissions;

class Index
{
    public function index()
    {
    	$rules = Permissions::getmenu(request()->uid);

        $tree = new \util\Tree($rules);
        $menu = $tree->leaf();
    	
        return view('', ['menu' => $menu]);
    }

    public function panel()
    {
    	return view();
    }

    public function emptyAuth()
    {
    	return view('/public/error', ['icon' => '#xe6af', 'error' => '还没有获得一些权限']);
    }
}
