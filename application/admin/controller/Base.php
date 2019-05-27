<?php

namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    
    protected function initialize()
    {
        // echo "init ";
    }
    
    /**
     * @param $code 状态码
     * @author lishuaiqiu
     * Admin后台table数据全局统一返回格式
     */
    public function table_json($data = [], $count = 0, $code = 0, $msg = "")
    {
        $count <= 10 && $count = 0; //小于10条时隐藏layui分页功能
        return json(['code' => $code, 'msg' => $msg, 'count' => $count, 'data' => $data]);
    }

    /**
     * @param $code 状态码
     * @author lishuaiqiu
     * Admin后台json数据全局统一返回格式
     */
    public function res_json(int $code=100, $result="")
    {
        return json(['code' => $code, 'result' => $result]);
    }

    /**
    *   返回请求结果状态
    */
    public function res_json_str($code='101', $result='')
    {
        $data = ['code' => $code, 'result' => $result];
        return $this->json($data);
    }

    /**
    *   返回json字符串数据
    */
    public function json($data)
    {
        return json()->data($data)->getContent();
    }

}
