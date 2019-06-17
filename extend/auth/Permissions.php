<?php
// +----------------------------------------------------------------------
// | QingCMS [ MAKE THINGS BETTER ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://udzan.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: asuma(lishuaiqiu) <weibo.com/770878450>
// +----------------------------------------------------------------------

namespace auth;
use auth\src\Auth;

class Permissions extends Auth
{
	/**
     * 检查权限
     */
	public function check($name, $uid, $relation = 'or', $mark = 1, $mode = 'url')
	{
		$exceptIds = $this->_config['auth_check_except'] ? explode(",", $this->_config['auth_check_except']) : [];
		if($exceptIds && in_array($uid, $exceptIds)){
			if(in_array($uid, $exceptIds)){
				return true;
			}
		}

		if(!$this->_config['auth_list_only']){
			return parent::check($name, $uid, $relation, $mark, $mode);	
		}
		
		$rule = \Db::name($this->_config['auth_rule'])->where('name', $name)->cache($name, 30*24*60*60, 'auth_rule')->value('id');
		if($rule){
			return parent::check($name, $uid, $relation, $mark, $mode);
		}

		return true;
	}

	/**
     * 获取权限菜单列表
     */
	public function getmenu($uid, $mark = 1)
	{
		static $_menuList = array(); //保存用户验证通过的权限列表
        
        if (isset($_menuList[$uid.$mark])) {
            return $_menuList[$uid.$mark];
        }

		$exceptIds = $this->_config['auth_check_except'] ? explode(",", $this->_config['auth_check_except']) : [];
		if($exceptIds && in_array($uid, $exceptIds)){
			$map = [
	            ['status', '=', 1],
	            ['is_menu', '=', 1]
		    ];
		}else{
			//读取用户所属用户组
	        $groups = $this->getGroups($uid, $mark);
	        $ids = [];
	        
	        foreach ($groups as $v) {
	            $ids = array_merge($ids, explode(',', $v['rules']));
	        }
	        $ids = array_unique($ids); //去除重复的规则
	        
	        if (empty($ids)) {
	            return [];
	        }

	        $map = [
	            ['id', 'in', $ids],
	            ['status', '=', 1],
	            ['is_menu', '=', 1]
	        ];
		}

        $cacheKey = 'rule'.$mark.'_'.md5(http_build_query($map));
        $rules = \think\Db::name($this->_config['auth_rule'])->field('id,name,title,pid,icon,type')->cache($cacheKey, 30*24*60*60, 'auth_rule')->where($map)->select();
        
        $_menuList[$uid.$mark] = $rules;
        
        return $rules;
	}
}