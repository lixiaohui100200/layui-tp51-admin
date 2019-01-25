<?php
namespace app\wjutil\controller;

class Index
{
    public function index()
    {
        return 'weijin util集合';
    }

    public function allyusers()
    {
    	$res = \Db::table('extra_source_user')->alias('a')->field("a.id,a.name,b.is_join_lucky,FROM_UNIXTIME(b.create_time, '%H:%i:%S') create_time, CASE WHEN b.is_join_lucky=1 THEN 'lucky' WHEN b.is_join_lucky IS NULL THEN '未签到' END status")->leftjoin('extra_source_meeting_sign b', 'a.id = b.user_id')->where('unid', 'IN', '78,91')->order('a.id desc')->select();
    	return view('allyusers', ['users' => $res]);
    }
}