<?php
namespace app\admin\behavior;
use think\Request;
/**
 * 
 */
class Test 
{
	
	public function run(Request $request, $param)
	{
		try {
			throw new \Exception("Error Processing Request", 1);	
		} catch (\Exception $e) {
			i_log('catch');	
		}
	}
}