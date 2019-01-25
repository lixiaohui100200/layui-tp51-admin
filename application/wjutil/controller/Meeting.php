<?php
namespace app\wjutil\controller;
use Db;
use think\Request;

class Meeting extends Base
{
    public function index()
    {
     	echo "Meeting";
    }

    public function signIn()
    {
    	$meeting_id = 1;

    	$mySign = Db::query("SELECT * FROM extra_source_meeting_sign a LEFT JOIN extra_source_meeting b ON a.meeting_id=b.id WHERE a.user_id = :user_id AND b.id = :meeting_id LIMIT 1", ['user_id' => $this->userId, 'meeting_id' => $meeting_id]);

    	if($mySign){
    		$this->assign('sign', 1);
			$this->assign('phone', $this->userPhone);  		
    	}else{
    		$this->assign('sign', 0);
    	}

    	$this->assign('username', $this->userName);
    	return $this->fetch();
    }

    public function doneSign()
    {
    	$data = [
    		'user_id' => $this->userId,
    		'meeting_id' => 1,
    		'create_time' => time(),
    		'sign_num' => $this->userPhone,
            'is_join_lucky' => 0
    	];

    	$result = Db::name('extra_source_meeting_sign')->strict(false)->insert($data);
    	!$result && exit($this->res_json('102', '签到失败'));

    	exit($this->res_json('100', ''));
    }

    public function luckyDraw()
    {
        return $this->fetch();
    }

    public function numPool()
    {
        $result = Db::table('extra_source_meeting_sign')->field('sign_num')->where('is_join_lucky', '<>', 1)->select();

        !$result && exit($this->res_json('102', '数据获取失败'));

        $sum = count($result);
        $round  = rand(0, $sum-1);
        $final = $result[$round]['sign_num'];

        if($final > 0){
            Db::name('extra_source_meeting_sign')->where('sign_num', $final)->update(['is_join_lucky' => 1]);
        }
        
        exit($this->res_json('100', $final));
    }
}
