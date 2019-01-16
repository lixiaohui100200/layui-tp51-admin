<?php
namespace app\http\middleware;
use util\Redis;
use Env;
use Session;

class InAppCheck
{
    public function handle($request, \Closure $next)
    {
    	if (preg_match('~micromessenger~i', $request->header('user-agent'))) {
            $request->InApp = 'WeChat';
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

    public function dingtalk($request)
    {
        if($request->get('code')){
            if($request->get('state') == Redis::get('ding_state')){
                $c = new \DingTalkClient(\DingTalkConstant::$CALL_TYPE_OAPI, \DingTalkConstant::$METHOD_POST, \DingTalkConstant::$FORMAT_JSON);
                $req = new \OapiSnsGetuserinfoBycodeRequest;
                $req->setTmpAuthCode($request->get('code'));
                $resp=$c->executeWithAccessKey($req, "https://oapi.dingtalk.com/sns/getuserinfo_bycode", Env::get('DING_APPID'), Env::get('DING_APPSECRET'));

                Session::set('ding.openid', $resp->user_info->openid);
                Session::set('ding.nick', $resp->user_info->nick);
            }else{
                return view('public/tips', ['type' => 'pride', 'code' => '请不要非法入侵']);
            }
        }else{
            $redirect_uri = HTTP_FRONT.'://'.DOMAIN_NAME.$request->server('REQUEST_URI');
            $state = rand(100, 999).'1';
            Redis::set('ding_state', $state, 30);

            $query = array(
                'appid' => Env::get('DING_APPID'),
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
}