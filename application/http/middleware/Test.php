<?php

namespace app\http\middleware;

class Test
{
    public function handle($request, \Closure $next)
    {
    	$response = $next($request);

    	i_log('后置');
    	
    	return $response;
    }
}
