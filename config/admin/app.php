<?php
//配置文件
return [
	/* 注册登录session key */
	'auth_key' => 'udzan_asuma',
	'auth_key_incookie' => 'udzan_asuma_c',
	
	/* 权限配置 */
	'auth_config' => [
		'auth_on'           => true,                // 认证开关
		'auth_list_only'   	=> true,				//是否只对规则表的权限检测，若否则验证全部路由
		'auth_check_except' => '1',					//不需要验证权限的用户ID，多个ID以,分隔
	    'auth_type'         => 1,                   // 认证方式，1为实时认证；2为登录认证。
	    'auth_group'        => 'auth_group',        // 用户组数据表名
	    'auth_group_access' => 'auth_group_access', // 用户-用户组关系表
	    'auth_rule'         => 'auth_rule',         // 权限规则表
	    'auth_user'         => 'admin_user'         // 用户信息表
	]
];