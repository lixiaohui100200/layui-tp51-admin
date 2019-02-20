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
                $this->wechat($request);
            }
        } else if (preg_match('~alipay~i', $request->header('user-agent'))) {
            $request->InApp = 'Alipay';
        } else if (preg_match('~dingtalk~i', $request->header('user-agent'))) {
            $request->InApp = 'DingTalk';
            if(!Session::has('ding.openid')){
                $this->dingtalk($request);
            }
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

    public function dingtalk($request)
    {
        if($request->get('code')){
            if($request->get('state') == Redis::get('ding_state')){
                $c = new \DingTalkClient(\DingTalkConstant::$CALL_TYPE_OAPI, \DingTalkConstant::$METHOD_POST, \DingTalkConstant::$FORMAT_JSON);
                $req = new \OapiSnsGetuserinfoBycodeRequest;
                $req->setTmpAuthCode($request->get('code'));
                $resp=$c->executeWithAccessKey($req, "https://oapi.dingtalk.com/sns/getuserinfo_bycode", config('this.ding_appid'), config('this.ding_appsecret'));

                Session::set('ding.openid', $resp->user_info->openid);
                Session::set('ding.nick', $resp->user_info->nick);
            }else{
                return view('public/tips', ['type' => 'pride', 'code' => '请不要非法入侵']);
            }
        }else{
            $redirect_uri = http_scheme().'://'.$request->server('SERVER_NAME').$request->server('REQUEST_URI');
            $state = rand(100, 999).'1';
            Redis::set('ding_state', $state, 30);

            $query = array(
                'appid' => config('this.ding_appid'),
                'response_type' => 'code',
                'scope' => 'snsapi_auth',
                'state' => $state,
                'redirect_uri' => $redirect_uri
            );

            $ding_redirect = "https://oapi.dingtalk.com/connect/oauth2/sns_authorize?".http_build_query($query);
            
            header('location:'.$ding_redirect);
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