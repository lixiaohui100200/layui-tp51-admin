<?php

/**
 * 任务列表
 * @author auto create
 */
class TaskTopVo
{
	
	/** 
	 * 开始时间
	 **/
	public $create_time;
	
	/** 
	 * 结束时间
	 **/
	public $finish_time;
	
	/** 
	 * 结果，分为AGREE（同意），REFUSE（拒绝），REDIRECTED（转交）
	 **/
	public $task_result;
	
	/** 
	 * 任务状态，分为NEW（未启动），RUNNING（处理中），PAUSED（暂停），CANCELED（取消），COMPLETED（完成），TERMINATED（终止）
	 **/
	public $task_status;
	
	/** 
	 * 任务节点id
	 **/
	public $taskid;
	
	/** 
	 * 任务处理人
	 **/
	public $userid;	
}
?>