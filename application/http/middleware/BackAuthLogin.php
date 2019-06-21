<?php
// +----------------------------------------------------------------------
// | 后台权限检测中间件
// +----------------------------------------------------------------------
// | Author: asuma(lishuaiqiu) <sqiu_li@163.com>
// +----------------------------------------------------------------------
// | Time: 2019-05-06
// +----------------------------------------------------------------------
namespace app\http\middleware;
use Session;
use Url;
use auth\facade\Permissions;

class BackAuthLogin
{
    /**
     * 跳转地址，支持符合规则的路由或模块/控制器/方法
     *
     */
    protected $redirect_url = '/admin/login';  // 必设项，检测到未登录时的跳转地址

    /**
     * 排除的验证地址
     * 优先匹配路由规则，或者模块/控制器/方法。若需要精确到方法，请使用完整的模块路由地址（例：模块/控制器/方法）
     * 支持 模块，模块/控制器，模块/控制器/方法
     * @var array
     */
    protected $except = [
        'admin/login',
    ];

    public function handle($request, \Closure $next, $name)
    {
        if($this->inExceptArray($request)){
            return $next($request);
        }

    	if(!app('register')->isLogined()){
            //用户未登录后跳转
            if($request->isAjax()){
                //返回head头 ajax的url请求由js接收跳转
                return response()->header([
                    'Ajax-Mark' => ' redirect',
                    'Redirect-Path' => Url::build($this->redirect_url)
                ]);
            }else{
                return redirect($this->redirect_url);
            }
    	}else{
            $userInfo = Session::get(config('auth_key'));
            $node = $request->controller().'/'.$request->action();
            
            // 权限检测
            if(!Permissions::check($node, $userInfo['uid'])){
                if($request->isAjax()){
                    return res_json(-101, '没有权限操作哦');
                }else{
                    return view('/public/error', ['icon' => '#xe6af', 'error' => '没有权限访问哦']);
                }
            }

            $request->uid = $userInfo['uid'];
            $request->uname = $userInfo['uname'];
            $request->ulogin = $userInfo['ulogin'];
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
