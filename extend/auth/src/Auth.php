<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614 <weibo.com/luofei614>　
// +----------------------------------------------------------------------
// | Modify: asuma(lishuaiqiu) <weibo.com/770878450>　2019-05-23
// +----------------------------------------------------------------------
namespace auth\src;

class Auth{

    //默认配置
    protected $_config = array(
        'auth_on'           => true,                // 认证开关
        'auth_type'         => 1,                   // 认证方式，1为实时认证；2为登录认证。
        'auth_group'        => 'auth_group',        // 用户组数据表名
        'auth_group_access' => 'auth_group_access', // 用户-用户组关系表
        'auth_rule'         => 'auth_rule',         // 权限规则表
        'auth_user'         => 'admin'              // 用户信息表
    );

    public function __construct() {
        if (config('auth_config')) {
            //可设置配置项 auth_config, 此配置项为数组。
            $this->_config = array_merge($this->_config, config('auth_config'));
        }
    }

    /**
      * 检查权限
      * @param name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
      * @param uid  int           认证用户的id
      * @param relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
      * @param int mark           区别不同的check行为，一般不需设置
      * @param string mode        执行check的模式 
      * @return boolean           通过验证返回true;失败返回false
     */
    public function check($name, $uid, $relation = 'or', $mark = 1, $mode = 'url') {
        if (!$this->_config['auth_on']) {
            return true;
        }

        $authList = $this->getAuthList($uid, $mark); //获取用户需要验证的所有有效规则列表
        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = [$name];
            }
        }

        $list = []; //保存验证通过的规则名
        if ($mode == 'url') {
            $REQUEST = unserialize(strtolower(serialize($_REQUEST)));
        }

        foreach ($authList as $auth) {
            $query = preg_replace('/^.+\?/U', '', $auth);
            if ($mode == 'url' && $query != $auth) {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth = preg_replace('/\?.*$/U', '', $auth);

                if (in_array($auth, $name) && $intersect == $param) {  //如果节点相符且url参数满足
                    $list[] = $auth;
                }
            } else if (in_array($auth, $name)) {
                $list[] = $auth;
            }
        }

        if ($relation == 'or' and ! empty($list)) {
            return true;
        }

        $diff = array_diff($name, $list);
        if ($relation == 'and' and empty($diff)) {
            return true;
        }

        return false;
    }

    /**
     * 根据用户id获取用户组,返回值为数组
     * @param  uid int     用户id
     * @return array       用户所属的用户组 array(
     *     array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
     *     ...)
     */
    public function getGroups($uid, $mark=1) {
        static $groups = array();

        if (isset($groups[$uid])) return $groups[$uid];

        $cacheKey = 'group_'.$mark.'_'.$uid;
        $user_groups = \think\Db::name($this->_config['auth_group_access'])
            ->alias('a')
            ->join($this->_config['auth_group']." g", "g.id=a.group_id")
            ->where("a.uid='$uid' and g.status='1'")
            ->field('uid,group_id,title,rules')
            ->cache($cacheKey, 24*60*60)
            ->select();
            
        $groups[$uid] = $user_groups ? $user_groups : [];

        return $groups[$uid];
    }

    /**
     * 获得权限列表
     * @param integer $uid  用户id
     * @param integer $mark 
     */
    protected function getAuthList($uid, $mark) {
        static $_authList = array(); //保存用户验证通过的权限列表
        
        if (isset($_authList[$uid.$mark])) {
            return $_authList[$uid.$mark];
        }
        if($this->_config['auth_type'] == 2 && isset($_SESSION['_auth_list_'.$uid.$mark])){
            return $_SESSION['_auth_list_'.$uid.$mark];
        }

        //读取用户所属用户组
        $groups = $this->getGroups($uid, $mark);
        $ids = array();//保存用户所属用户组设置的所有权限规则id
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid.$mark] = array();
            return array();
        }

        $map = [
            ['id', 'in', $ids],
            ['status', '=', 1]
        ];

        $cacheKey = 'rule'.$mark.'_'.md5(http_build_query($map));
        //读取用户组所有权限规则
        $rules = \think\Db::name($this->_config['auth_rule'])->field('condition,name')->cache($cacheKey, 24*60*60, 'auth_rule')->where($map)->select();

        //循环规则，判断结果。
        $authList = array();
        foreach ($rules as $rule) {
            if (!empty($rule['condition'])) { //根据condition进行验证
                $user = $this->getUserInfo($uid);//获取用户信息,一维数组

                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $authList[] = strtolower($rule['name']);
                }
            } else {
                //只要存在就记录
                $authList[] = strtolower($rule['name']);
            }
        }

        $_authList[$uid.$mark] = $authList;
        if($this->_config['auth_type'] == 2){
            //规则列表结果保存到session
            $_SESSION['_auth_list_'.$uid.$mark] = $authList;
        }
        
        return array_unique($authList);
    }

    /**
     * 获得用户资料,根据自己的情况读取数据库
     */
    protected function getUserInfo($uid) {
        static $userinfo = array();

        if(!isset($userinfo[$uid])){
            $cacheKey= md5('adminUser_'.$uid);
            $userinfo[$uid] = \think\Db::name($this->_config['auth_user'])->where(array('id' => $uid))->cache($cacheKey, 24*60*60, 'admin_user')->find();
        }

        return $userinfo[$uid];
    }

}
