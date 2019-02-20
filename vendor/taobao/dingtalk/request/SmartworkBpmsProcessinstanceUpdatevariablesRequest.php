<?php
/**
 * dingtalk API: dingtalk.smartwork.bpms.processinstance.updatevariables request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class SmartworkBpmsProcessinstanceUpdatevariablesRequest
{
	/** 
	 * 审批实例id
	 **/
	private $processInstanceId;
	
	/** 
	 * 评论
	 **/
	private $remark;
	
	/** 
	 * 表单参数列表
	 **/
	private $variables;
	
	private $apiParas = array();
	
	public function setProcessInstanceId($processInstanceId)
	{
		$this->processInstanceId = $processInstanceId;
		$this->apiParas["process_instance_id"] = $processInstanceId;
	}

	public function getProcessInstanceId()
	{
		return $this->processInstanceId;
	}

	public function setRemark($remark)
	{
		$this->remark = $remark;
		$this->apiParas["remark"] = $remark;
	}

	public function getRemark()
	{
		return $this->remark;
	}

	public function setVariables($variables)
	{
		$this->variables = $variables;
		$this->apiParas["variables"] = $variables;
	}

	public function getVariables()
	{
		return $this->variables;
	}

	public function getApiMethodName()
	{
		return "dingtalk.smartwork.bpms.processinstance.updatevariables";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->processInstanceId,"processInstanceId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
