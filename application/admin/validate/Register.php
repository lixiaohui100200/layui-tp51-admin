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
        'phone' => 'require|mobile',
        'password' => 'require|length:6,12',
        'repassword' => 'require|confirm:password'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'username.require' => '用户名必填项',
        'username.chsAlphaNum' => '用户名只能为字母、汉字或数字以及组合',
        'phone.require' => '手机为必填项',
        'phone.mobile' => '手机格式不正确',
        'password.require' => '密码为必填项',
        'password.length' => '密码长度为1到12位',
        'repassword.require' => '请再次输入密码',
    ];

    protected $scene = [
        'login' => ['username', 'password']
    ];

    public function repeatPass($value,$rule,$data=[])
    {
        if($value != $data['password']){
            return '两次输入密码不一致';
        }

        return true;
    }
}
