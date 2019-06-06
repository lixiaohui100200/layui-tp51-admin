
asuma-tp51-admin(又名udzanPro) 是基于 ThinkPHP 5.1 + Layui 开发的(纯净的)后台管理系统
===============

优化或新增的功能：

 + 常用的工具方法如随机字符串、优化的MD5密码加密、安全的base64加密解密、手机号验证、日志调试等（位于应用目录下的common.php里）
 + 常用的应用与浏览器检测中间件, 微信/钉钉自动授权登录
 + 使用单例模式构造的Redis类

内置扩展：

 + 集成阿里短信SDK
 + 集成微信开发SDK -- EasyWeChat（[手册传送门](https://www.easywechat.com/docs/master/overview)）

 使用本框架需提前准备以下环境：

 + php7.0以上。推荐使用`php7.1`
 + Mysql5.5以上。推荐使用`mysql5.7`
 + 除常用的php扩展外，还需安装php-redis扩展（必须，本系统多处使用到了redis作为数据存储）

## 安装步骤

1.git下载项目源文件

~~~
git clone http://gitlab.weijin365.com/lisq/asuma-tp51-beta.git my-project(项目名称自定义)
~~~

2.使用Composer安装thinkphp框架以及依赖库

~~~
cd my-project
composer install
~~~

3.复制环境变量文件

~~~
cp .env.example .env
~~~

4.加载手动引入的第三方类库（可跳过，如果手动引入的类库无法加载时执行）
~~~
composer dump-autoload
~~~

5.更新Thinkphp框架（如果需要最新的thinkphp时执行）

~~~
composer update topthink/framework
~~~

> 使用本系统默认视为已熟悉PHP Web开发，熟悉Thinkphp5.1，熟悉LNMP开发项目，请自行部署Web访问环境
> 安装后请使用域名访问本系统，或者放在Web环境根目录

## 作者

+ [微博](https://weibo.com/770878450)
+ [个人网站](http://www.udzan.com/)

## 在线手册

+ [Thinkphp5.1完全开发手册](https://www.kancloud.cn/manual/thinkphp5_1/content)

+ [Layui官方文档](https://www.layui.com/doc/)


## 声明

本系统仅供交流学习使用，请勿作商业用途发布

基于开源的Thinkphp5.1官方源码二次开发

前端组件使用开源的Layui前端UI框架

若使用本系统涉及到layuiAdmin，请认真阅读[《layui 付费产品服务条款》](https://fly.layui.com/jie/26280/)，并自行到[layui官网](https://www.layui.com/admin/)下载源码

开源协议请参阅 [LICENSE.txt](LICENSE.txt)
