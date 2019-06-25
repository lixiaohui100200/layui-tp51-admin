<?php

namespace app\admin\validate;

use think\Validate;

class Register extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'username' => 'require',
        'loginname' => 'require|alphaDash|length:3,16',
        'phone' => 'mobile',
        'email' => 'email',
        'password' => 'require|length:6,12',
        'repassword' => 'require|confirm:password',
        'remark' => 'length:2,250'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'username.require' => '用户名必填项',
        'loginname.require' => '登录名必填项',
        'loginname.alphaDash' => '登录名只能是字母、数字和下划线_及破折号-组合',
        'loginname.length' => '登录名长度为3到16位',
        'phone.mobile' => '手机格式不正确',
        'email.email' => '邮箱格式不正确',
        'password.require' => '密码为必填项',
        'password.length' => '密码长度为6到12位',
        'repassword.require' => '请再次输入密码',
        'repassword.confirm' => '两次输入密码不一致',
        'remark.length' => '备注长度在2到250位'
    ];

    protected $scene = [
        'login' => ['username', 'password'],
        'register' => ['loginname', 'phone', 'email'],
        'modify' => ['phone', 'email', 'remark'],
        'changepwd' => ['password', 'repassword']
    ];
}
