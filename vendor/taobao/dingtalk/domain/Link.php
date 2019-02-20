<?php

/**
 * 消息类型，此时固定为:link
 * @author auto create
 */
class Link
{
	
	/** 
	 * 点击消息跳转的URL
	 **/
	public $message_url;
	
	/** 
	 * 图片URL
	 **/
	public $pic_url;
	
	/** 
	 * 消息内容。如果太长只会部分展示
	 **/
	public $text;
	
	/** 
	 * 消息标题
	 **/
	public $title;	
}
?>