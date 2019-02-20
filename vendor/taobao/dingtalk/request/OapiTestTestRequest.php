<?php
/**
 * dingtalk API: dingtalk.oapi.test.test request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class OapiTestTestRequest
{
	/** 
	 * 1
	 **/
	private $input;
	
	private $apiParas = array();
	
	public function setInput($input)
	{
		$this->input = $input;
		$this->apiParas["input"] = $input;
	}

	public function getInput()
	{
		return $this->input;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.test.test";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
