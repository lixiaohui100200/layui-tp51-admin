
iAsuma/layui-tp51-admin(又名QingCMS，轻CMS) 纯净轻盈的后台管理系统
===============

优化或新增的功能：

 + 常用的工具方法如随机字符串、优化的MD5密码加密、安全的base64加密解密、手机号验证、日志调试等（位于应用目录下的common.php里）
 + 常用的应用与浏览器检测中间件, 微信自动授权登录
 + 使用单例模式构造的Redis类

内置扩展：

 + 集成微信开发SDK -- EasyWeChat（[手册传送门](https://www.easywechat.com/docs/master/overview)）
 > 若不需要，删除composer.json中的`"overtrue/wechat": "~4.0" ` (安装前) 或 `composer remove overtrue/wechat` (安装后)

 使用本框架需提前准备以下环境：

 + php7.0以上
 + Mysql5.7以上
 + Redis

## 安装步骤

1.git下载项目源文件

~~~
git clone https://github.com/iAsuma/layui-tp51-admin.git my-project
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

4.修改.env环境变量配置文件

~~~
vi .env #根据项目实际情况进行修改
~~~
~~~
DB_HOST = 127.0.0.1
DB_NAME = my_db
DB_USER = root
DB_PWD = 123456
~~~

5.完成。 根据自身情况部署web环境

~~~
http://your-domain.com/admin
~~~

> 使用本系统默认视为已熟悉PHP Web开发，熟悉Thinkphp5.1，熟悉LNMP开发项目，请自行部署Web访问环境
> 安装后请使用域名访问本系统，或者放在Web环境根目录

## other

* 加载手动引入的第三方类库（如果部分引入的类库无法加载时执行）
~~~
composer dump-autoload
~~~

* 更新Thinkphp框架（如果需要最新的thinkphp时执行）

~~~
composer update topthink/framework
~~~

* 若需要使用阿里短信功能
~~~
git clone https://github.com/iAsuma/sms-extend.git
~~~

* 若需要使用钉钉授权中间件
~~~
git clone https://github.com/iAsuma/dingtalk-extend.git
~~~

## 部分功能

### 权限日志
记录管理后台日志有两种方式
1. 在权限中的权限管理添加异步权限，并开启记录日志开关
2. 在相应的业务代码中添加`admin_log`钩子
~~~
Hook::listen('admin_log', ['权限名称', '权限行为描述']); 
~~~
示例：
~~~
Hook::listen('admin_log', ['登录', '登录页登录系统']); //监听登录行为
~~~
>在权限管理中开启了记录日志，不可在相关权限中再次添加记录日志的钩子

### Redis 
使用Redis扩展，除了thinkphp的Cache类，还可以引用 \extend\util\Redis 扩展类

~~~
Redis::方法(args1 [,args2...]);
~~~
示例：
~~~
Redis::get('key');
~~~

### 使用百度UEditor

在模板中需要使用富文本编辑器的地方引入以下代码
~~~
{include file="public/ueditor" name="" content=''}
~~~

>`name`为form表单域字段名称  `content`为编辑器初始化的内容，没有内容请为空

如需修改文件上传主目录，只需修改.env环境变量`FILE_ROOT_PATH`和`UEDITOR_UPLOAD_PATH`

### 上传文件扩展

修改.env 
~~~
FILE_ROOT_PATH = . #文件根目录
FILE_UPLOAD_PATH = /uploads #上传文件主目录
~~~
获取表单上传文件
~~~
$file = app('upload')->file();
$file = app('upload')->file('image'); #获取name为image的FILE文件信息
~~~
上传文件
~~~
app('upload')->move($file);
app('upload')->move($file, false); 保留文件名
app('upload')->move($file, 'XXX的文件'); 自定义文件名
app('upload')->move($file, true, false); 自动生成文件名，但不覆盖同名文件
~~~
base64图片编码字符串转图片
~~~
app('upload')->base64ToImage($_POST['base64Img_formFiled']);
~~~
单文件上传示例
~~~
<form action="/index/index/upload" enctype="multipart/form-data" method="post">
<input type="file" name="image" /> <br> 
<input type="submit" value="上传" /> 
</form>
~~~
~~~
public function upload(){
    // 获取表单上传文件 例如上传了001.jpg
    $file = app('upload')->file('image');
    // 上传文件
    $info = app('upload')->move($file);
    if($info){
        // 成功上传后 获取上传信息
        // 输出 jpg
        echo $info->getExtension();
        // 输出 20190701/42a79759f284b767dfcb2a0197904287.jpg
        echo $info->getSaveName();
        // 输出 /20190701/42a79759f284b767dfcb2a0197904287.jpg
        **echo $info->savePath; //新增变量**
        // 输出 42a79759f284b767dfcb2a0197904287.jpg
        echo $info->getFilename(); 
    }else{
        // 上传失败获取错误信息
        echo $file->getError();
    }
}
~~~
多文件上传示例
~~~
<form action="/index/index/upload" enctype="multipart/form-data" method="post">
<input type="file" name="image[]" /> <br> 
<input type="file" name="image[]" /> <br> 
<input type="file" name="image[]" /> <br> 
<input type="submit" value="上传" /> 
</form>
~~~
~~~
public function upload(){
    // 获取表单上传文件
    $files = app('upload')->file('image');
    foreach($files as $file){
        // 上传文件
        $info = app('upload')->move($file);
        if($info){
            **echo $info->savePath;**
        }else{
            echo $file->getError();
        }    
    }
}
~~~

## 作者

+ Asuma (阿斯玛)
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
