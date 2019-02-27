<?php
// +----------------------------------------------------------------------
// | 当前项目设置
// +----------------------------------------------------------------------
// | Author: lishuaiqiu @asuma 2018-12-14
// +----------------------------------------------------------------------

return [
    // Redis相关配置
    'redis_host' => Env::get('REDIS_HOST', '127.0.0.1'), // redis连接地址
    'redis_port' => 6379, // redis端口号
    'redis_prefix' => Env::get('REDIS_PREFIX', ''), //redis 项目统一的key前缀

    //钉钉相关配置
    'ding_appid' => 'dingoaxxse4aif7wknuwhx',
	'ding_appsecret' => 'F7HKpNUJZeyRyiNShX7itWLtNO8xLO-uSZj7x0648wBPgK_GVM-Tt6Sg9tbsR-Pf',

    //阿里云短信服务
    'alisms_key' => 'LTAIDl8gVgPhtUMX',
    'alisms_secret' => 'tbBKqgOqabMXNZeCZh1bDJPbqoaYXO',
    'alisms_snscode' => 'SMS_120410443',
    'alisms_sign' => '质链SAAS云平台'
];