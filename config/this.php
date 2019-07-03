<?php
// +----------------------------------------------------------------------
// | 当前项目设置
// +----------------------------------------------------------------------
// | Author: lishuaiqiu @asuma 2018-12-14
// +----------------------------------------------------------------------

return [
    //表单令牌名称
    'form_token' => '__token__',

    //管理员初始密码
    'admin_init_pwd' => '123456',

    // \extend\util\Redis类的相关配置
    'redis_host' => Env::get('REDIS_HOST', '127.0.0.1'), // redis连接地址
    'redis_port' => 6379, // redis端口号
    'redis_prefix' => Env::get('REDIS_PREFIX', ''), //redis 项目统一的key前缀
    'redis_password' => Env::get('REDIS_PASSWORD', ''), //redis 密钥
];