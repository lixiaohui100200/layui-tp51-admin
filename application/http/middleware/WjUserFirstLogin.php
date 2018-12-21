<?php
namespace app\http\middleware;
use Session;
use Url;
use util\Redis;

class WjUserFirstLogin
{
    public function handle($request, \Closure $next, $name)
    {
    	$userDingOpenId = Session::get('ding.openid');
    	if(!Redis::hGet('wj_user_ding', 'openid'.$userDingOpenId)){
    		if($request->isGet()){
				return redirect('wxUserFirstLogin');
    		}else{
    			header('location:'. Url::build('wxUserFirstLogin'), true, 301);
    		}
    	}

    	return $next($request);
    }
}
