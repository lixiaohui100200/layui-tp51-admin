<?php
namespace app\admin\controller;
use auth\facade\Permissions;
use think\Db;
use think\Request;
/**
 * 系统相关设置
 */
class SystemSet extends Base
{
	public function userInfo()
	{
		$uid = $this->request->uid;
		$cacheKey= md5('adminUser_'.$uid);
		$userInfo = $uid ? Db::name('admin_user')->where('id', '=', $uid)->cache($cacheKey, 30*24*60*60, 'admin_user')->find() : '';

		$roles = Permissions::getGroups($uid);
		$rolesArr = array_column($roles, 'title');
		$rolesStr = implode("，", $rolesArr);

		$this->assign('user', $userInfo);
		$this->assign('roles', $rolesStr);
		return $this->fetch();
	}

	public function updateUserInfo(Request $request)
	{
		$uid = $request->uid;
		empty($uid) && exit(res_json_native(-2, '非法修改'));

        if(checkFormToken($request->post())){
            $validate = new \app\admin\validate\Register;
            if(!$validate->scene('modify')->check($request->post())){
                exit(res_json_str(-1, $validate->getError()));
            }

            try {
                $data = [
                    'name' => $request->post('truename'),
                    'phone' => $request->post('phone'),
                    'email' => $request->post('email'),
                    'remark' => $request->post('remark')
                ];

                $where = $this->parseWhere([
                    ['email', '=', $data['email']],
                    ['phone', '=', $data['phone']]
                ]);

                $loginUser = Db::name('admin_user')
                ->field('id,name,login_name,phone,email')
                ->where(function($query) use($where){
                    $query->whereOr($where);
                })
                ->where('id', '<>', $uid)
                ->where('status', '<>', -1)
                ->select();

                in_array($data['phone'], array_column($loginUser, 'phone')) && exit(res_json_native(-3, '手机号已注册'));
                in_array($data['email'], array_column($loginUser, 'email')) && exit(res_json_native(-3, '邮箱已注册'));

                $update = Db::name('admin_user') ->where('id', $uid) -> update($data);
                $update === false && exit(res_json_native(-6, '修改失败'));

                \think\facade\Cache::clear('admin_user'); //清除用户数据缓存
                
                destroyFormToken($request->post());
                return res_json(1);
            } catch (\Exception $e) {
                return res_json(-5, '系统错误'.$e->getMessage());
            }
        }

        return res_json(-2, '请勿重复提交');
	}

	public function password()
	{
		return $this->fetch();
	}

	public function changePwd(Request $request)
	{
		$uid = $request->uid;
		empty($uid) && exit(res_json_native(-2, '非法修改'));

        if(checkFormToken($request->post())){
            $validate = new \app\admin\validate\Register;
            if(!$validate->scene('changepwd')->check($request->post())){
                exit(res_json_str(-1, $validate->getError()));
            }

            try {
                $data = [
                    'password' => md5safe($request->post('password'))
                ];

                $cacheKey= md5('adminUser_'.$uid);
                $userInfo = Db::name('admin_user')->where('id', '=', $uid)->cache($cacheKey, 30*24*60*60, 'admin_user')->find();

                $userInfo['password'] != md5safe($request->post('oldPassword')) && exit(res_json_native(-6, '当前密码错误'));

                $update = Db::name('admin_user') ->where('id', $uid) -> update($data);
                $update === false && exit(res_json_native(-6, '修改失败'));

                \think\facade\Cache::clear('admin_user'); //清除用户数据缓存
                
                destroyFormToken($request->post());
                return res_json(1);
            } catch (\Exception $e) {
                return res_json(-5, '系统错误'.$e->getMessage());
            }
        }

        return res_json(-2, '请勿重复提交');
	}

	public function notice()
	{
		return view('/public/error', ['icon' => '#xe6af', 'error' => '消息通知功能正在建设']);
	}
}