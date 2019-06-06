<?php
// +----------------------------------------------------------------------
// | 当前项目设置
// +----------------------------------------------------------------------
// | Author: lishuaiqiu @asuma 2018-12-14
// +----------------------------------------------------------------------

return [
    //表单令牌名称
    'form_token' => '__token__',

    // \extend\util\Redis类的相关配置
    'redis_host' => Env::get('REDIS_HOST', '127.0.0.1'), // redis连接地址
    'redis_port' => 6379, // redis端口号
    'redis_prefix' => Env::get('REDIS_PREFIX', ''), //redis 项目统一的key前缀
    'redis_password' => Env::get('REDIS_PASSWORD', ''), //redis 密钥

    //钉钉相关配置
    'ding_appid' => '',
	'ding_appsecret' => '',

    //阿里云短信服务
    'alisms_key' => '',
    'alisms_secret' => '',
    'alisms_snscode' => '',
    'alisms_sign' => ''
];