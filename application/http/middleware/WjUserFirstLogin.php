<?php
namespace app\http\middleware;
use Session;
use Url;
use util\Redis;
use Db;

class WjUserFirstLogin
{
    public function handle($request, \Closure $next, $name)
    {
    	$userDingOpenId = Session::get('ding.openid');
    	if(!Redis::hGet('wj_user_ding', 'openid'.$userDingOpenId)){
            $user = Db::table('extra_source_user')->field('phone')->where(['ding_open_id' => $userDingOpenId])->find();
            if($user){
                Redis::hSet('wj_user_ding', 'openid'.$userDingOpenId, $user['phone']);
            }else{
                if($request->isAjax()){
                    header('Ajax-Mark: redirect');
                    header("Redirect-Path: ".Url::build('wxUserFirstLogin'));
                }else{
                    return redirect('wxUserFirstLogin')->remember();
                }
                exit(); //执行跳转后进行业务隔离阻断，防止程序继续执行
            }
    	}else{
            if(Session::has('from_redirect')){
                Session::delete('from_redirect');
            }
        }

    	return $next($request);
    }
}
