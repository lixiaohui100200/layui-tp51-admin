<?php
// +----------------------------------------------------------------------
// | 当前项目设置
// +----------------------------------------------------------------------
// | Author: lishuaiqiu @asuma 2018-12-14
// +----------------------------------------------------------------------

return [
    // Redis相关配置
    'REDIS_HOST' => Env::get('REDIS_HOST', '127.0.0.1'), // redis连接地址
    'REDIS_PORT' => 6379, // redis端口号
    'REDIS_PREFIX' => Env::get('REDIS_PREFIX', ''), //redis 项目统一的key前缀

    //阿里云短信服务
    'alisms_key' => 'LTAIDl8gVgPhtUMX',
    'alisms_secret' => 'tbBKqgOqabMXNZeCZh1bDJPbqoaYXO',
    'alisms_snscode' => 'SMS_120410443'
];