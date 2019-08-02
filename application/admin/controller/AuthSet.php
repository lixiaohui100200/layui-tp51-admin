<?php
namespace app\admin\controller;

use think\Request;
use Db;
use think\facade\Hook;

class AuthSet extends Base
{
    public function admins()
    {
        $roles = Db::name('auth_group')->field('id,title')->cache('allroles', 24*60*60, 'admin_role')->where('status', 1)->select();
        $this->assign('roles', $roles);
    	return $this->fetch();
    }

    public function adminList()
    {
        $get = $this->request->get();

        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 10;

        $where = [
            ['login_name|email|phone', 'LIKE', $get['username'] ?? ''],
            ['name', 'LIKE', $get['truename'] ?? ''],
        ];

        $where = $this->parseWhere($where);

        $query = Db::name('admin_user')->alias('a')->fieldRaw('id,name,login_name,phone,email,FROM_UNIXTIME(create_time, "%Y-%m-%d") AS create_time,status,groupNames')->page($page, $limit)->where('status', '<>', '-1')->order('id');

        $leftTable = Db::name('auth_group_access')->field([
            'acc.uid',
            'GROUP_CONCAT(acc.group_id)' => 'groupIds',
            'GROUP_CONCAT(g.title)' => 'groupNames'
        ])->alias('acc')->leftjoin('auth_group g', 'acc.group_id=g.id')->where('g.status', '<>', '-1')->group('acc.uid')->buildSql();

        $query->leftjoin($leftTable.'b', 'a.id=b.uid');
        $query->where($where);

        if($get['role'] ?? ''){
            $query->whereRaw('CONCAT(",", groupIds, ",") LIKE :groupIds', ['groupIds' => '%,'.$get['role'].',%']);
        }

        $admin = $query->select();

        $countQuery = Db::name('admin_user')->alias('a')->where($where)->where('status', '<>', '-1');
        if($get['role'] ?? ''){
            $countQuery->leftjoin($leftTable.'b', 'a.id=b.uid');
            $countQuery->whereRaw('CONCAT(",", groupIds, ",") LIKE :groupIds', ['groupIds' => '%,'.$get['role'].',%']);
        }
        $count = $countQuery->count();
        
        return table_json($admin, $count);
    }

    public function adminEdit()
    {
        if($this->request->get('id')){
            $cacheKey= md5('adminUser_'.(int)$this->request->get('id'));
            $adminInfo = Db::name('admin_user')->where('id', (int)$this->request->get('id'))->cache($cacheKey, 24*60*60, 'admin_user')->find();
            $hasRole = Db::name('auth_group_access')->where('uid', (int)$this->request->get('id'))->select();
            $hasRoleId = array_column($hasRole, 'group_id');
        }

        $roles = Db::name('auth_group')->field('id,title')->cache('allroles', 24*60*60, 'admin_role')->where('status', 1)->select();

        $this->assign('admin', $adminInfo ?? []);
        $this->assign('hasrole', $hasRoleId ?? []);
        $this->assign('roles', $roles);
        return $this->fetch();
    }

    public function pulladmin(Request $request)
    {
        if(checkFormToken($request->post())){
            $validate = new \app\admin\validate\Register;
            if(!$validate->scene('register')->check($request->post())){
                exit(res_json_str(-1, $validate->getError()));
            }

            Db::startTrans();
            try {
                $data = [
                    'login_name' => $request->post('loginname'),
                    'name' => $request->post('truename'),
                    'phone' => $request->post('phone'),
                    'email' => $request->post('email'),
                    'password' => md5safe(config('this.admin_init_pwd')),
                    'status' => $request->post('status') ?: 0,
                    'create_time' => time(),
                    'create_by' => $request->uid
                ];

                $where = $this->parseWhere([
                    ['login_name', '=', $data['login_name']],
                    ['email', '=', $data['email']],
                    ['phone', '=', $data['phone']]
                ]);

                $loginUser = Db::name('admin_user')
                ->field('id,name,login_name,phone,email')
                ->where(function($query) use($where){
                    $query->whereOr($where);
                })
                ->where('status', '<>', -1)
                ->select();

                in_array($data['login_name'], array_column($loginUser, 'login_name')) && exit(res_json_native(-3, '用户名已存在'));
                in_array($data['phone'], array_column($loginUser, 'phone')) && exit(res_json_native(-3, '手机号已注册'));
                in_array($data['email'], array_column($loginUser, 'email')) && exit(res_json_native(-3, '邮箱已注册'));

                $new_id = Db::name('admin_user') -> insertGetId($data);
                !$new_id && exit(res_json_native(-6, '添加失败'));

                $roleArr= explode(',', $request->post('roles'));
                foreach ($roleArr as $v) {
                    $access[] = [
                        'uid' => $new_id,
                        'group_id' => $v
                    ];
                }

                $result = Db::name('auth_group_access')->insertAll($access);

                if(!$result){
                    Db::rollback();
                    return res_json(-4, '添加失败');
                }

                Hook::listen('admin_log', ['权限', '添加了管理员'.$data['login_name']]);
                
                Db::commit();
                destroyFormToken($request->post());
                return res_json(1);
            } catch (\Exception $e) {
                Db::rollback();
                return res_json(-5, '系统错误');
            }
        }

        return res_json(-2, '请勿重复提交');
    }

    public function updateAdmin(Request $request)
    {
        empty($request->post('admin_id')) && exit(res_json_native(-2, '非法修改'));

        if(checkFormToken($request->post())){
            $validate = new \app\admin\validate\Register;
            if(!$validate->scene('register')->check($request->post())){
                exit(res_json_str(-1, $validate->getError()));
            }

            Db::startTrans();
            try {
                $data = [
                    'login_name' => $request->post('loginname'),
                    'name' => $request->post('truename'),
                    'phone' => $request->post('phone'),
                    'email' => $request->post('email')
                ];

                if($request->post('admin_id') != 1){
                    $data['status'] = $request->post('status') ?: 0;
                }

                if($request->post('isReSetPwd') ?? ''){
                    $data['password'] = md5safe(config('this.admin_init_pwd'));
                }

                $where = $this->parseWhere([
                    ['login_name', '=', $data['login_name']],
                    ['email', '=', $data['email']],
                    ['phone', '=', $data['phone']]
                ]);

                $loginUser = Db::name('admin_user')
                ->field('id,name,login_name,phone,email')
                ->where(function($query) use($where){
                    $query->whereOr($where);
                })
                ->where('id', '<>', $request->post('admin_id'))
                ->where('status', '<>', -1)
                ->select();

                in_array($data['login_name'], array_column($loginUser, 'login_name')) && exit(res_json_native(-3, '用户名已存在'));
                in_array($data['phone'], array_column($loginUser, 'phone')) && exit(res_json_native(-3, '手机号已注册'));
                in_array($data['email'], array_column($loginUser, 'email')) && exit(res_json_native(-3, '邮箱已注册'));

                $update = Db::name('admin_user') ->where('id', $request->post('admin_id')) -> update($data);
                !is_numeric($update) && exit(res_json_native(-6, '修改失败'));

                $roleArr= explode(',', $request->post('roles'));
                foreach ($roleArr as $v) {
                    $access[] = [
                        'uid' => $request->post('admin_id'),
                        'group_id' => $v
                    ];
                }

                if($request->post('admin_id') != 1){
                    Db::name('auth_group_access')->where('uid', (int)$request->post('admin_id'))->delete();
                    $result = Db::name('auth_group_access')->insertAll($access);

                    $cacheKey = 'group_1_'.$request->post('admin_id');
                    \think\facade\Cache::rm($cacheKey); //清除用户组缓存，权限实时生效
                    \think\facade\Cache::clear('admin_user'); //清除用户数据缓存

                    if(!$result){
                        Db::rollback();
                        return res_json(-4, '添加失败');
                    }
                }
                
                Hook::listen('admin_log', ['权限', '修改了管理员'.$data['login_name'].'的信息']);

                Db::commit();
                destroyFormToken($request->post());
                return res_json(1);
            } catch (\Exception $e) {
                Db::rollback();
                return res_json(-5, '系统错误'.$e->getMessage());
            }
        }

        return res_json(-2, '请勿重复提交');
    }

    public function changeAdminStatus()
    {
        $id = (int)$this->request->post('id');
        $uid = $this->request->uid;
        
        switch ($this->request->post('status')) {
            case 'true':
                $status = 1;
                break;
            case 'delete':
                $status = -1;
                break;
            default:
                $status = -2;
                break;
        }

        $id && $res = Db::name('admin_user')->where('id', '=', $id)->update(['status' => $status]);

        if($status == -1){
            !$res && exit(res_json_native(-3, '删除失败'));
            Hook::listen('admin_log', ['权限', '删除了管理员'.$this->request->post('name')]);
        }else{
            !$res && exit(res_json_native(-3, '状态切换失败'));
            Hook::listen('admin_log', ['权限', $status == -2 ? '冻结了管理员'.$this->request->post('name').'的账号' : '开启了管理员'.$this->request->post('name').'的账号']);
        }
        
        \think\facade\Cache::clear('admin_user'); //清除用户数据缓存
        return res_json(1);
    }

    public function roles()
    {
    	return $this->fetch();
    }

    public function roleList()
    {
        $get = $this->request->get();

        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 10;

        $where = [
            ['status', '<>', '-1']
        ];

        $formWhere = $this->parseWhere($where);
        $cacheKey = 'role_'.md5(http_build_query($formWhere));
        
        $count = Db::name('auth_group')->where($formWhere)->cache($cacheKey.'_count', 24*60*60, 'admin_role')->count('id');
        $roles = Db::name('auth_group')->where($formWhere)->order('id')->page($page, $limit)->cache($cacheKey.'_'.$page.'_'.$limit, 24*60*60, 'admin_role')->select();

        return table_json($roles, $count);
    }

    public function roleAdd(Request $request)
    {
        if($request->get('id')){
            $roleInfo = Db::name('auth_group')->where('id', (int)$request->get('id'))->find();

            $this->assign('role', $roleInfo);
            return $this->fetch('role_edit');
        }

        return $this->fetch();
    }

    public function allrules()
    {
        $rules = \Db::name('auth_rule')->where('status', 1)->order(['sorted', 'id'])->cache('use_rules', 24*60*60, 'auth_rule')->select();

        $tree = new \util\Tree($rules);
        $mods = $tree->leaf();
        
        return json(['code' => 0, 'data' => $mods]);
    }

    public function rulesChecked()
    {
        $rulesArr = explode(",", $this->request->get('rules'));

        $allrules = Db::name('auth_rule')->where('status', 1)->order(['sorted', 'id'])->cache('use_rules', 24*60*60, 'auth_rule')->select();

        foreach ($allrules as &$v) {
            in_array($v['id'], $rulesArr) && $v['checked'] = true;
        }

        $tree = new \util\Tree($allrules);
        $mods = $tree->leaf();
        return json(['code' => 0, 'data' => $mods]);
    }

    public function addNewRole(Request $request)
    {
        try {
            $post = $request->post();
            !checkFormToken($post) && exit(res_json_native(-2, '请勿重复提交'));

            $rules = $post['rules'] ?? [];
            empty($rules) && exit(res_json_native(-3, '请为角色选择规则节点'));
            sort($rules);
            $rules = implode(",", $rules);

            $data = [
                'title' => off_xss(trim($post['rolename'])),
                'rules' => $rules,
                'status' => $post['status'] ?? -2,
                'remark' => $post['desc']
            ];

            $validate = \think\Validate::make([
                'title' => 'require|max:30',
                'remark' => 'max:200',
            ],[
                'title.require'=> '请填写角色名',
                'title.max'    => '角色名最多不能超过30个字符',
                'remark'       => '描述最多不能超过200个字符',
            ]);

            if(!$validate->check($data)){
                return res_json(-4, $validate->getError());
            }

            if($post['role_id'] ?? ''){
                $result = Db::name('auth_group') ->where('id', $post['role_id']) -> update($data);
                !is_numeric($result) && exit(res_json_native(-1, '修改失败'));
                Hook::listen('admin_log', ['权限', '修改了角色组'.$data['title'].'的信息']);
            }else{
                $result = Db::name('auth_group') -> insert($data);
                !$result && exit(res_json_native(-1, '添加失败'));
                Hook::listen('admin_log', ['权限', '添加了角色组'.$data['title']]);
            }
            
            \think\facade\Cache::clear('admin_role'); //清除规则缓存，让列表实时生效
            destroyFormToken($post);
            return res_json(1);
        } catch (\Exception $e) {
            return res_json(-100, $e->getMessage());
        }
    }

    public function changeRoleStatus()
    {
        $id = (int)$this->request->post('id');
        $uid = $this->request->uid;
        $pwd = $this->request->post('password');

        $cacheKey= md5('adminUser_'.$uid);
        $uid && $user = Db::name('admin_user')->where('id', '=', $uid)->cache($cacheKey, 24*60*60, 'admin_user')->find();
        empty($user) && exit(res_json_native(-1, '用户信息获取失败，请重新登录'));
        
        switch ($this->request->post('status')) {
            case 'true':
                $status = 1;
                break;
            case 'delete':
                $user['password'] != md5safe($pwd) && exit(res_json_native(-2, '密码错误'));
                $status = -1;
                break;
            default:
                $status = -2;
                break;
        }

        $id && $res = Db::name('auth_group')->where('id', '=', $id)->update(['status' => $status]);

        if($status == -1){
            !$res && exit(res_json_native(-3, '删除失败'));
            Hook::listen('admin_log', ['权限', '删除了角色组'.$this->request->post('name')]);
        }else{
            !$res && exit(res_json_native(-3, '状态切换失败'));
            Hook::listen('admin_log', ['权限', ($status == -2 ? '关闭了角色组' :'开启了角色组').$this->request->post('name')]);
        }
        
        \think\facade\Cache::clear('admin_role'); //清除规则缓存，让列表实时生效
        return res_json(1);
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

        $cacheKey = 'rule_'.md5(http_build_query($formWhere));
        $count = Db::name('auth_rule')->where($formWhere)->cache($cacheKey.'_count', 24*60*60, 'auth_rule')->count('id');

        if(empty($count)){
            return table_json([], 0);
        }

        // 查询所有规则，用以排序子父级关系，并存入缓存(tag:auth_rule)
        $rules = Db::name('auth_rule')->where($formWhere)->order(['sorted', 'id'])->cache($cacheKey, 24*60*60, 'auth_rule')->select();

        $mark = count($formWhere);
        if(($where[1][2] && $mark > 2) || (!$where[1][2] && $mark > 1)) {
            $modsTree = $rules;
        }else{
            $tree = new \util\Tree($rules);
            $modsTree = $tree->table();
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

        $mods = Db::name('auth_rule')->field('id,title,name,pid')->where($where)->order('sorted,id')->select();

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
                'sorted' => $post['run_type'] == 2 ? 99 : (int)$post['sorted'],
                'pid' => (int)$post['pId'],
                'is_menu' => $post['is_menu'] ?? 0,
                'icon' => $post['icon'] ?? '',
                'is_logged' => $post['is_log'] ?? 0,
                'remark' => off_xss(trim($post['desc']))
            ];

            $validate = \think\Validate::make([
                'name' => 'require|max:50',
                'title' => 'require|max:30',
                'remark' => 'max:200',
            ],[
                'name.require' => '请填写规则标识',
                'name.max'     => '规则标识最多不能超过50个字符',
                'title.require'=> '请填写权限名',
                'title.max'    => '权限名最多不能超过30个字符',
                'remark'       => '描述最多不能超过200个字符',
            ]);

            if(!$validate->check($data)){
                return res_json(-3, $validate->getError());
            }

            $result = Db::name('auth_rule') -> insert($data);
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
        $res = Db::name('auth_rule')->where('id', '=', $id)->update(['is_logged' => $is_logged]);
        \think\facade\Cache::clear('auth_rule'); //清除规则缓存，让列表实时生效
        !$res && exit(res_json_native(-3, '切换失败'));

        return res_json(1);
    }

    public function changeWeight()
    {
        $post = $this->request->post();
        $post['is_menu'] != 1 && exit(res_json_native(-1, '非菜单无法设置权重'));

        $post['id'] && $res = Db::name('auth_rule')->where('id', '=', (int)$post['id'])->update(['sorted' => (int)$post['newVal']]);
        !$res && exit(res_json_native(-3, '修改失败'));
        \think\facade\Cache::clear('auth_rule'); //清除规则缓存，让列表实时生效

        return res_json(1);
    }

    public function changeRuleStatus()
    {
        $id = (int)$this->request->post('id');
        $uid = $this->request->uid;
        $pwd = $this->request->post('password');

        $cacheKey= md5('adminUser_'.$uid);
        $uid && $user = Db::name('admin_user')->where('id', '=', $uid)->cache($cacheKey, 24*60*60, 'admin_user')->find();
        empty($user) && exit(res_json_native(-1, '用户信息获取失败，请重新登录'));
        
        switch ($this->request->post('status')) {
            case 'true':
                $status = 1;
                break;
            case 'delete':
                $user['password'] != md5safe($pwd) && exit(res_json_native(-2, '密码错误'));
                $info = Db::name('auth_rule')->field('id,name')->where('pid' , $id)->select();
                !empty($info) && exit(res_json_native(-2, '请先删除子权限'));
                $status = -1;
                break;
            default:
                $status = -2;
                break;
        }

        if($status == -1){
            $id && $res = Db::name('auth_rule')->delete($id);
            !$res && exit(res_json_native(-3, '删除失败'));
        }else{
            $id && $res = Db::name('auth_rule')->where('id', '=', $id)->update(['status' => $status]);
            !$res && exit(res_json_native(-3, '状态切换失败'));
        }
        
        \think\facade\Cache::clear('auth_rule'); //清除规则缓存，让列表实时生效

        return res_json(1);
    }

    public function authEdit()
    {
        $id = (int)$this->request->get('rule');
        $id && $info = Db::name('auth_rule')->where(['id' => $id])->find();

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
                'name' => off_xss(trim($post['authname'])),
                'status' => $post['status'] ?? -2,
                'sorted' => $post['sorted'] ?? 99,
                'pid' => (int)$post['pId'],
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

            $result = Db::name('auth_rule')->where('id', (int)$post['rule_id'])->update($data);
            !is_numeric($result) && exit(res_json_native(-1, '修改失败'));

            destroyFormToken($post);
            \think\facade\Cache::clear('auth_rule'); //清除规则缓存，让列表实时生效
            return res_json(1);
        } catch (\Exception $e) {
            return res_json(-100, $e->getMessage());
        }
    }

    public function operationLog()
    {
        return $this->fetch();
    }

    public function logList()
    {
        $get = $this->request->get();
        
        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 10;

        $where = [];
        if(isset($get['username']) && !empty($get['username'])){
            $where[] = ['behavior_user', '=', $get['username']];
        }
        
        $countQuery = Db::name('operation_log')->where($where);
        $query = Db::name('operation_log')->where($where)->order('id DESC')->page($page, $limit);

        if(isset($get['datetime']) && !empty($get['datetime'])){
            $date = explode('~', $get['datetime']);
            $get['start'] = $date[0];
            $countQuery->whereTime('record_time', 'between', [$date[0].' 00:00:00', $date[1].' 23:59:59']);
            $query->whereTime('record_time', 'between', [$date[0].' 00:00:00', $date[1].' 23:59:59']);
        }

        $count = $countQuery->count('id');
        $logs = $query->select();

        return table_json($logs, $count);
    }

    public function batchDeleteLogs()
    {
        $ids = $this->request->post('ids');
        empty($ids) && exit(res_json_native(-1, '请选择要删除的数据'));

        $uid = $this->request->uid;
        $pwd = $this->request->post('password');

        $cacheKey= md5('adminUser_'.$uid);
        $uid && $user = Db::name('admin_user')->where('id', '=', $uid)->cache($cacheKey, 24*60*60, 'admin_user')->find();
        empty($user) && exit(res_json_native(-1, '用户信息获取失败，请重新登录'));

        $user['password'] != md5safe($pwd) && exit(res_json_native(-2, '密码错误'));

        $result = Db::name('operation_log')->where('id', 'IN', $ids)->delete();
        !$result && exit(res_json_native(-1, '删除失败'));

        return res_json(1);
    }
}