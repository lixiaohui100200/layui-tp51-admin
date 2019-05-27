<?php
namespace app\admin\controller;

use think\Request;
use Db;

class AuthSet extends Base
{
    public function index()
    {
        echo "authset";
    }

    public function admins()
    {
    	return $this->fetch();
    }

    public function adminList()
    {
        $admin = Db::table('admin_user')->field('id,name,login_name,phone,email,FROM_UNIXTIME(create_time, "%Y-%m-%d") AS create_time,status')->select();
        $count = Db::table('admin_user')->count();
        
        return table_json($admin, $count);
    }

    public function addadmin()
    {
        return $this->fetch();
    }

    public function pulladmin(Request $request)
    {
        if(checkFormToken($request->post())){
            Db::startTrans();
            try {
                $data = [
                    'name' => $request->post('username'),
                    'login_name' => $request->post('loginname'),
                    'phone' => $request->post('phone'),
                    'email' => $request->post('email'),
                    'password' => md5safe('123456'),
                    'status' => $request->post('status') ?: 0,
                    'create_time' => time(),
                    'create_by' => 0
                ];
                
                $result = Db::table('admin_user') -> insert($data);
                !$result && exit(res_json(-3, '添加失败'));
                
                Db::commit();
                destroyFormToken($request->post());
                return res_json(1);
            } catch (\Exception $e) {
                Db::rollback();
                return res_json(-1);
            }
        }

        return res_json(-2, '请勿重复提交');
    }

    public function roles()
    {
    	return $this->fetch();
    }

    public function roleList()
    {
        $roles = Db::table('auth_group')->select();
        $count = Db::table('auth_group')->count('id');

        return table_json($roles, $count);
    }

    public function permissions()
    {
        return $this->fetch();
    }

    public function permissionsList()
    {
        $admin = Db::table('auth_rule')->field('id,name,title,status,sorted')->select();
        $count = Db::table('admin_user')->count();
        
        return table_json($admin, $count);
    }
}