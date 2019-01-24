<?php
namespace app\wjutil\controller;

class Index
{
    public function index()
    {
    	dump(\util\Redis::get('lucky'));
    	dump(\util\Redis::set('lucky', ''));
		dump(\util\Redis::get('lucky'));
        return 'weijin util集合';
    }
}
