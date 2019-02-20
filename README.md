
本框架基于 ThinkPHP 5.1（LTS版本）二次开发，支持更新官方框架
===============

使用本框架需提前准备以下环境：

 + php5.6以上。推荐使用`php7.1`
 + Mysql5.5以上。推荐使用`mysql5.7`
 + 安装php-redis扩展

新增的功能：

 + 常用的工具方法如随机字符串、安全的base64加密解密、手机号验证、日志调试等
 + 常用的浏览器检测中间件,微信/钉钉自动授权登录
 + 使用单例模式构造的Redis类
 + 集成阿里短信SDK
 + 集成微信开发SDK -- EasyWeChat

## 安装步骤

1.使用git安装基础框架

~~~
git clone http://gitlab.weijin365.com/lisq/asuma-tp51-beta.git my-project(项目名称自定义)
~~~

2.使用Composer安装依赖库

~~~
cd my-project
composer install
~~~

3.同时需要加载手动引入的第三方类库
~~~
composer dump-autoload
~~~

4.复制环境变量文件

~~~
cp .env.example .env
~~~

5.更新Thinkphp框架（如果需要）

~~~
composer update topthink/framework
~~~

> 可以使用php自带webserver快速测试
> 切换到根目录后，启动命令：php think run


## 在线手册

+ [完全开发手册](https://www.kancloud.cn/manual/thinkphp5_1/content)

> 本框架基于thinkphp5.1官方框架开发，可直接浏览官方手册


## 版权信息

基于Thinkphp官方源码二次开发

ThinkPHP遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2006-2018 by ThinkPHP (http://thinkphp.cn)

All rights reserved。

ThinkPHP® 商标和著作权所有者为上海顶想信息科技有限公司。

更多细节参阅 [LICENSE.txt](LICENSE.txt)
