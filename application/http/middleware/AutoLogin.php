<?php
// +----------------------------------------------------------------------
// | 自动注册登录中间件
// +----------------------------------------------------------------------
// | Author: asuma(lishuaiqiu) <sqiu_li@163.com>
// +----------------------------------------------------------------------

namespace app\http\middleware;
use Session;
use Url;

class AutoLogin
{
    /**
     * 跳转地址，支持符合规则的路由或模块/控制器/方法
     *
     */
    protected $redirect_url = 'login';  // 必设项，检测到未登录时的跳转地址

    /**
     * 排除的验证地址
     * 支持完整的路由规则，或者模块/控制器/方法。若需要精确到方法，请使用完整的路由地址（模块/控制器/方法）
     *
     * @var array
     */
    protected $except = [
        //'Login',
        //'Through'
    ];

    public function handle($request, \Closure $next, $name)
    {
        if($this->inExceptArray($request)){
            return $next($request);
        }

    	//$userphone = Session::get('user.userphone');
    	if(!$userphone){
    		
            //登录业务逻辑
            // .....
            
            if($user){
                //示例
                //Session::set('user.id', $user['id']);
            }else{
                if($request->isAjax()){
                    header('Ajax-Mark: redirect');
                    header("Redirect-Path: ".Url::build($this->redirect_url));
                }else{
                    return redirect($this->redirect_url)->remember();
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
            if(strtolower($patternArr[0]) == strtolower($request->module()) && strtolower($patternArr[1]) == strtolower($request->controller())){
                return true;
            }
        }else{
            if(strtolower($patternArr[0]) == strtolower($request->module())){
                return true;
            }
        }

        return false;
    }
}
