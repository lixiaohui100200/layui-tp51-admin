<?php

/**
 * result
 * @author auto create
 */
class PageResult
{
	
	/** 
	 * list
	 **/
	public $list;
	
	/** 
	 * 表示下次查询的游标，当返回结果没有该字段时表示没有更多数据了
	 **/
	public $next_cursor;	
}
?>