<?php

/**
 * 请求入参
 * @author auto create
 */
class GroupInfoModifyRequest
{
	
	/** 
	 * 群ID，由创建群接口返回
	 **/
	public $chatid;
	
	/** 
	 * 修改后的群名称
	 **/
	public $group_name;
	
	/** 
	 * 修改后的群主，若为空或与当前群主相同，则不会对群主进行变更操作。
	 **/
	public $group_owner;	
}
?>