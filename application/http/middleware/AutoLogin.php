<?php
namespace app\http\middleware;
use Session;
use Url;
use Db;

class AutoLogin
{
    /**
     * 支持完整的路由规则，或者模块/控制器/方法。若需要精确到方法，请使用完整的路由地址（模块/控制器/方法）
     *
     * @var array
     */
    protected $except = [
        'wjutil/Login',
        'wj/lucky',
        'wjutil/Meeting/numPool',
        'wjutil/index/index'
    ];

    public function handle($request, \Closure $next, $name)
    {
        if($this->inExceptArray($request)){
            return $next($request);
        }

    	$userphone = Session::get('user.userphone');
    	if(!$userphone){
    		$where = "";
    		if($request->InApp == 'WeChat'){
    			$where = ['open_id' => Session::get('wechat')['original']['openid']];
    		}else if($request->InApp == 'DingTalk'){
    			$where = ['ding_open_id' => Session::get('ding.openid')];
    		}else{
    			$where = ['id' => 0];
    		}

            $user = Db::table('extra_source_user')->field('id,phone,name')->where('unid', 'IN' , '78,91')->where($where)->find();
            
            if($user){
                Session::set('user.userphone', $user['phone']);
                Session::set('user.name', $user['name']);
                Session::set('user.id', $user['id']);
            }else{
                if($request->isAjax()){
                    header('Ajax-Mark: redirect');
                    header("Redirect-Path: ".Url::build('wjutil/login/autologin'));
                }else{
                    return redirect('wjutil/login/autologin')->remember();
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

    protected function inExceptArray($request)
    {
        foreach ($this->except as $v) {
            if(strtolower($v) == $request->path() || $this->checkRoute($request, $v)){
                return true;
            }
        }

        return false;
    }

    protected function checkRoute($request, $pattern)
    {
        $patternArr = explode('/', $pattern);
        if(count($patternArr) == 3){
            if(strtolower($patternArr[0]) == strtolower($request->module()) && strtolower($patternArr[1]) == strtolower($request->controller()) && strtolower($patternArr[2]) == strtolower($request->action())){
                return true;
            }
        }else if(count($patternArr) == 2){
            if($patternArr[0] == $request->module() && $patternArr[1] == $request->controller()){
                return true;
            }
        }else{
            if($patternArr[0] == $request->module()){
                return true;
            }
        }

        return false;
    }
}
