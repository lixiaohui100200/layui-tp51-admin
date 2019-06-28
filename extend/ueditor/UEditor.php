<?php
namespace ueditor;

//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
date_default_timezone_set("Asia/chongqing");
error_reporting(E_ERROR);
header("Content-Type: text/html; charset=utf-8");

use think\facade\Env;
use think\facade\Request;

/**
 * @author modify: Asuma(lishuaiqiu) 2019-06-28
 */
class UEditor 
{
	private $_config = NULL;
	public function __construct()
	{
		$this->_config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(Env::get('EXTEND_PATH')."/ueditor/config/config.json")), true);	
	}

	public function action()
	{
		$get = Request::get();
		$CONFIG = $this->_config;

		switch ($get['action']) {
		    case 'config':
		        $result =  json_encode($CONFIG);
		        break;

		    /* 上传图片 */
		    case 'uploadimage':
		    /* 上传涂鸦 */
		    case 'uploadscrawl':
		    /* 上传视频 */
		    case 'uploadvideo':
		    /* 上传文件 */
		    case 'uploadfile':
		        $result = include(Env::get('EXTEND_PATH')."/ueditor/src/action_upload.php");
		        break;

		    /* 列出图片 */
		    case 'listimage':
		    /* 列出文件 */
		    case 'listfile':
		        $result = include(Env::get('EXTEND_PATH')."/ueditor/src/action_list.php");
		        break;

		    /* 抓取远程文件 */
		    case 'catchimage':
		        $result = include(Env::get('EXTEND_PATH')."/ueditor/src/action_crawler.php");
		        break;

		    default:
		        $result = json_encode(array(
		            'state'=> '请求地址出错'
		        ));
		        break;
		}

		/* 输出结果 */
		if (isset($get["callback"])) {
		    if (preg_match("/^[\w_]+$/", $get["callback"])) {
		        echo htmlspecialchars($get["callback"]) . '(' . $result . ')';
		    } else {
		        echo json_encode(array(
		            'state'=> 'callback参数不合法'
		        ));
		    }
		} else {
		    echo $result;
		}
	}
}