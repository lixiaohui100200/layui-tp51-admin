<?php
// +----------------------------------------------------------------------
// | App检测并且自动获取授权 中间件
// +----------------------------------------------------------------------
// | Author: asuma(lishuaiqiu) <sqiu_li@163.com>
// +----------------------------------------------------------------------

namespace app\http\middleware;
use util\Redis;
use Session;
use EasyWeChat\Factory;

class InAppCheck
{
    public function handle($request, \Closure $next)
    {
    	if (preg_match('~micromessenger~i', $request->header('user-agent'))) {
            $request->InApp = 'WeChat';
            if(!Session::has('wechat.original.openid')){
                $this->wechat($request); //微信授权
            }
        } else if (preg_match('~alipay~i', $request->header('user-agent'))) {
            $request->InApp = 'Alipay';
        } else if (preg_match('~dingtalk~i', $request->header('user-agent'))) {
            $request->InApp = 'DingTalk';
        } else{
        	$request->InApp = 'Normal';
        }
        
    	return $next($request);
    }

    public function wechat($request)
    {
        $config = [
            'app_id' => config('wechat.official_account')['default']['app_id'],
        ];
        
        if($request->get('code')){
            $config['secret'] = config('wechat.official_account')['default']['secret'];
            $app = Factory::officialAccount($config);
            $user = $app->oauth->user();
            Session::set('wechat.openid', $user['id']);
            Session::set('wechat.userinfo', $user['original']);
        }else{
            $config['oauth'] = [
                'scopes'   => ['snsapi_userinfo'], //snsapi_base  or snsapi_userinfo
                'callback' => $this->getTargetUrl($request),
            ];
            $app = Factory::officialAccount($config);
            header("location: ". $app->oauth->redirect()->getTargetUrl());
            exit(); //执行跳转后进行业务隔离阻断，防止程序继续执行
        }
    }

    protected function getTargetUrl($request)
    {
        $param = $request->get();
        if (isset($param['code'])) {
            unset($param['code']);
        }
        if (isset($param['state'])) {
            unset($param['state']);
        }
        return $request->baseUrl() . (empty($param) ? '' : '?' . http_build_query($param));
    }
}