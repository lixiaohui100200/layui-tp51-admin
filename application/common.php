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
 * @author lishuaiqiu
 */
function i_base64encode($string) 
{
    $base64 = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_',''),$base64);
    return $data;
}

/**
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
    $logUrl = "../runtime/log/debug/";
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
 * 以get方式提交请求
 * @param $url
 * @return bool|mixed
 */
function httpGet($url)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== false) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
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
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[\d]{9}$|^18[\d]{9}$#', $mobile) ? true : false;
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
function randStr($length)
{
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}