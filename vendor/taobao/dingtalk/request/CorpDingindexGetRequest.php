<?php
/**
 * dingtalk API: dingtalk.corp.dingindex.get request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class CorpDingindexGetRequest
{
	/** 
	 * 统计日期
	 **/
	private $statDates;
	
	private $apiParas = array();
	
	public function setStatDates($statDates)
	{
		$this->statDates = $statDates;
		$this->apiParas["stat_dates"] = $statDates;
	}

	public function getStatDates()
	{
		return $this->statDates;
	}

	public function getApiMethodName()
	{
		return "dingtalk.corp.dingindex.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->statDates,"statDates");
		RequestCheckUtil::checkMaxListSize($this->statDates,5,"statDates");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
