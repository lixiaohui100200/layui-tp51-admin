<?php
// +----------------------------------------------------------------------
// | 常量定义文件
// +----------------------------------------------------------------------
// | Author: lishuaiqiu @asuma 2018-12-14
// +----------------------------------------------------------------------
define('HTTP_FRONT', http_front()); //获取当前请求是http还是https
define('DOMAIN_NAME', Request::server('SERVER_NAME')); //获取当前域名

//taobao类库常量定义
define("TOP_SDK_WORK_DIR", "../runtime/temp/");
define("TOP_SDK_DEV_MODE", true);