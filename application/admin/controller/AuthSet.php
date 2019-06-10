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

                $whereor = [
                    'login_name' => $data['login_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone']
                ];

                $loginUser = Db::table('admin_user')->field('id,name,login_name,phone,email,password,head_img,status')->whereOr($whereor)->fetchSql(true)->where('status' ,'<>', -1)->select();
                dump($loginUser);die;
                
                $result = Db::table('admin_user') -> insert($data);
                !$result && exit(res_json_native(-3, '添加失败'));
                
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
        $get = $this->request->get();

        $where = [
            ['status', '<>', '-1'],
            ['is_menu', '=', (isset($get['is_menu']) && !empty($get['is_menu'])) ? 1 : ''],
            ['name', 'LIKE', $get['name'] ?? ''],
            ['title', 'LIKE', $get['title'] ?? '']
        ];

        $formWhere = $this->parseWhere($where);

        $count = Db::table('auth_rule')->where($formWhere)->count('id');

        if(empty($count)){
            return table_json([], 0);
        }

        // 查询所有规则，用以排序子父级关系，并存入缓存(tag:auth_rule)
        $cacheKey = md5(http_build_query($formWhere));
        $admin = Db::table('auth_rule')->where($formWhere)->order(['sorted', 'id'])->cache($cacheKey, 3600, 'auth_rule')->select();

        $mark = count($formWhere);
        if(($where[1][2] && $mark > 2) || (!$where[1][2] && $mark > 1)) {
            $modsTree = $admin;
        }else{
            $tree = new \util\Tree($admin);
            $modsTree = $tree->table($admin);
        }

        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 10;

        $list = array_slice($modsTree, ($page-1)*$limit, $limit);
        
        return table_json($list, count($modsTree));
    }

    public function authAdd()
    {
        return $this->fetch();
    }

    public function modsTree()
    {
        $where['status'] = 1;
        if($this->request->post('type') == 3){
            $where['type'] = [1,2];
        }else{
            $where['type'] = 1;
        }

        $mods = Db::table('auth_rule')->field('id,title,name,pid')->where($where)->order('sorted,id')->select();

        if(empty($mods)){
            return null;
        }
        
        $tree = new \util\Tree($mods);
        $modsTree = $tree->leaf();

        return json($modsTree);
    }

    public function pullRule(Request $request)
    {
        try {
            $post = $request->post();
            !checkFormToken($post) && exit(res_json_native('-2', '请勿重复提交'));

            $data = [
                'name' => off_xss(trim($post['authname'])),
                'title' => off_xss(trim($post['authtitle'])),
                'type' => (int)$post['type'],
                'run_type' => (int)$post['run_type'],
                'status' => $post['status'] ?? -2,
                'sorted' => (int)$post['sorted'],
                'pid' => (int)$post['pId'],
                'is_menu' => $post['is_menu'] ?? 0,
                'icon' => $post['icon'] ?? '',
                'is_logged' => $post['is_log'] ?? 0,
                'remark' => off_xss(trim($post['desc']))
            ];

            $validate = \think\Validate::make([
                'name' => 'require|max:20',
                'title' => 'require|max:30',
                'remark' => 'max:200',
            ],[
                'name.require' => '请填写规则标识',
                'name.max'     => '规则标识最多不能超过20个字符',
                'title.require'=> '请填写权限名',
                'title.max'    => '权限名最多不能超过30个字符',
                'remark'       => '描述最多不能超过200个字符',
            ]);

            if(!$validate->check($data)){
                return res_json(-3, $validate->getError());
            }

            $result = Db::table('auth_rule') -> insert($data);
            !$result && exit(res_json_native(-1, '添加失败'));

            destroyFormToken($post);
            \think\facade\Cache::clear('auth_rule'); //清除规则缓存，让列表实时生效
            return res_json(1);
        } catch (\Exception $e) {
            $msg = false !== strpos($e->getMessage(), '1062') ? '权限标识重复' : $e->getMessage();
            return res_json(-100, $msg);
        }
        
    }

    public function changeLogStatus()
    {
        $id = (int)$this->request->post('id');
        $is_logged = $this->request->post('is_logged');

        $is_logged = $is_logged == 'true' ? 1 : 0;
        $res = Db::table('auth_rule')->where('id', '=', $id)->update(['is_logged' => $is_logged]);
        \think\facade\Cache::clear('auth_rule'); //清除规则缓存，让列表实时生效
        !$res && exit(res_json_native(-3, '切换失败'));

        return res_json(1);
    }

    public function changeWeight()
    {
        $post = $this->request->post();
        $post['is_menu'] != 1 && exit(res_json_native(-1, '非菜单无法设置权重'));

        $post['id'] && $res = Db::table('auth_rule')->where('id', '=', (int)$post['id'])->update(['sorted' => (int)$post['newVal']]);
        !$res && exit(res_json_native(-3, '修改失败'));
        \think\facade\Cache::clear('auth_rule'); //清除规则缓存，让列表实时生效

        return res_json(1);
    }

    public function changeRuleStatus()
    {
        $id = (int)$this->request->post('id');
        $uid = $this->request->uid;
        $pwd = $this->request->post('password');

        $cacheKey= md5('user_'.$uid);
        $uid && $user = Db::table('admin_user')->where('id', '=', $uid)->cache($cacheKey, 86400, 'admin_user')->find();
        empty($user) && exit(res_json_native(-1, '用户信息获取失败，请重新登录'));
        
        switch ($this->request->post('status')) {
            case 'true':
                $status = 1;
                break;
            case 'delete':
                $user['password'] != md5safe($pwd) && exit(res_json_native(-2, '密码错误'));
                $info = Db::table('auth_rule')->field('id,name')->where('pid' , $id)->select();
                !empty($info) && exit(res_json_native(-2, '请先删除子权限'));
                $status = -1;
                break;
            default:
                $status = -2;
                break;
        }

        if($status == -1){
            $id && $res = Db::table('auth_rule')->delete($id);
            !$res && exit(res_json_native(-3, '删除失败'));
        }else{
            $id && $res = Db::table('auth_rule')->where('id', '=', $id)->update(['status' => $status]);
            !$res && exit(res_json_native(-3, '状态切换失败'));
        }
        
        \think\facade\Cache::clear('auth_rule'); //清除规则缓存，让列表实时生效

        return res_json(1);
    }

    public function authEdit()
    {
        $id = (int)$this->request->get('rule');
        $id && $info = Db::table('auth_rule')->where(['id' => $id])->find();

        isset($info) && $this->assign('info', $info);
        
        return $this->fetch();
    }

    public function editRule(Request $request)
    {
        try {
            $post = $request->post();
            !checkFormToken($post) && exit(res_json_native('-2', '请勿重复提交'));

            $data = [
                'title' => off_xss(trim($post['authtitle'])),
                'status' => $post['status'] ?? -2,
                'sorted' => (int)$post['sorted'],
                'is_menu' => $post['is_menu'] ?? 0,
                'icon' => $post['icon'] ?? '',
                'is_logged' => $post['is_log'] ?? 0,
                'remark' => off_xss(trim($post['desc']))
            ];

            $validate = \think\Validate::make([
                'title' => 'require|max:30',
                'remark' => 'max:200',
            ],[
                'title.require'=> '请填写权限名',
                'title.max'    => '权限名最多不能超过30个字符',
                'remark'       => '描述最多不能超过200个字符',
            ]);

            if(!$validate->check($data)){
                return res_json(-3, $validate->getError());
            }

            $result = Db::table('auth_rule')->where('id', (int)$post['rule_id'])->update($data);
            !$result && exit(res_json_native(-1, '修改失败'));

            destroyFormToken($post);
            \think\facade\Cache::clear('auth_rule'); //清除规则缓存，让列表实时生效
            return res_json(1);
        } catch (\Exception $e) {
            return res_json(-100, $e->getMessage());
        }
        
    }
}