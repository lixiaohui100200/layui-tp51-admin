<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// | Author2: lishuaiqiu(asuma)
// +----------------------------------------------------------------------

// 应用公共文件

//error_reporting(E_ALL ^ E_NOTICE); // 除了 E_NOTICE，报告其他所有错误

/**
 * @author lishuaiqiu
 * 美化输出print_r
 */
function i_dump($param)
{
    echo '<pre>';
    print_r($param);
    echo '</pre>';
}

/**
 * 优化的base64encode
 * @author lishuaiqiu
 */
function i_base64encode($string) 
{
    $base64 = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_',''),$base64);
    return $data;
}

/**
 * 优化的base64decode
 * @author lishuaiqiu
 */
function i_base64decode($string) 
{
    $data = str_replace(array('-','_'),array('+','/'),$string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

/**
 * 记录日志输出文件，用于程序无法在浏览器打印调试时调用
 * @author lishuaiqiu 
 * @param  $output 输出的变量信息
 * @param  $filename 文件名
 * @param  $suffix 文件后缀名
 * @return 
 */
function i_log($output, $filename = '', $suffix = ".log")
{
    $logUrl = "../runtime/ilog/";
    $filename == "" && $filename = date('ymd');

    !is_dir($logUrl) && mkdir($logUrl , 0777 , true);
    
    $head_str = '['.date('H:i:s').'] '.think\facade\Request::module().'/'.think\facade\Request::controller().'/'.think\facade\Request::action().' '.ucfirst(gettype($output))."\r\n";

    if(!is_string($output)){
        try {
            $output = var_export($output, true);
        } catch (\Exception $e) {
            $output = print_r($output, true);
        }
    };

    file_put_contents($logUrl.$filename.$suffix , $head_str.$output."\r\n" ,FILE_APPEND);
}

/**
 * 升级的md5加密，防止暴力破解
 * +----------------------------------------------------------------------
 * | 加密步骤（加密过程中所有MD5加密后均为32字符十六进制数）：
 * +----------------------------------------------------------------------
 * | 1. 对原字符串进行MD5加密
 * +----------------------------------------------------------------------
 * | 2. 得到加密字符串后，从第九位开始（下标为8），共截取14位字符串
 * +----------------------------------------------------------------------
 * | 3. 使用约定的密钥key,key的第一个字符拼接到14位字符串首位，key的第二个字符拼接到末尾
 * +----------------------------------------------------------------------
 * | 4. 将拼接得到的16位字符串再次使用MD5加密
 * +----------------------------------------------------------------------
 * @author lishuaiqiu
 * @param $str 要加密的字符串
 * @param $key 自定义加密key 两位的字符串, 自行记录，以免忘记不可随意更改
 * @return 返回32位字符十六进制数
 */
function md5safe($str, $key = 'MY')
{
    if (!is_string($key) || strlen($key)<2) return false;

    $en_str = $key[0].substr(md5($str), 8, 14).$key[1];

    return md5($en_str);
}

/**
 * @author lishuaiqiu
 * @return 返回http或者https
 */
function http_scheme() 
{
    if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
        return 'https';
    } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
        return 'https';
    } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return 'https';
    }
    return 'http';
}

/**
 * 表单令牌验证，防止表单重复提交
 * @param $data 表单数据
 * @author lishuaiqiu
 */
function checkFormToken($data=[])
{
    $token = config('this.form_token');
    if(!isset($data[$token])){
        return false;
    }
    
    $session_token = \think\facade\Session::get($token);

    if(!$token || !$session_token){ //令牌无效
        return false;
    }
    
    if($session_token === $data[$token]){
        return true;
    }

    return false;
}

/**
 * 销毁缓存中的表单令牌
 * @param $data 表单数据
 * @author lishuaiqiu
 */
function destroyFormToken($data=[])
{
    $token = config('this.form_token');
    if(!isset($data[$token])){
        return false;
    }

    $session_token = \think\facade\Session::get($token);

    if(!$token || !$session_token){ //令牌无效
        return false;
    }

    if($session_token === $data[$token]){
        \think\facade\Session::delete('__token__');
        return true;
    }

    return false;
}

/**
 * @author lishuaiqiu
 * 手机号验证
 */
function checkPhone($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^15[^4]{1}\d{8}$|^16[68]{1}\d{8}$|^17[\d]{9}$|^18[\d]{9}$|^19[89]{1}\d{8}$#', $mobile) ? true : false;
}

/**
 * @author lishuaiqiu
 * 检查座机号码
 */
function checkLandline($landline){
    $phoneNum = preg_match('/^((\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$/', $landline);

    return $phoneNum ? true : false;
}

/**
 * @author lishuaiqiu
 * 生成随机字符串
 */
function rand_str($length=16)
{
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/**
 * @param $code 状态码
 * @author lishuaiqiu
 * Admin后台json数据全局统一返回格式
 */
function res_json(int $code=100, $result="")
{
    return json(['code' => $code, 'result' => $result]);
}

function res_json_str($code= 100, $result='')
{
    $data = ['code' => $code, 'result' => $result];
    return json()->data($data)->getContent();
}

/**
 * @param $code 状态码
 * @author lishuaiqiu
 * Admin后台table数据全局统一返回格式
 */
function table_json($data = [], $count = 0, $code = 0, $msg = "")
{
    $count <= 10 && $count = 0; //小于10条时隐藏layui分页功能
    return json(['code' => $code, 'msg' => $msg, 'count' => $count, 'data' => $data]);
}