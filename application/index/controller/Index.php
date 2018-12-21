<?php
namespace app\index\controller;
use Think\Db;
use SmsDemo;
use util\Redis;
use this\DySms;

class Index
{
    public function index()
    {
        $data = array(
            'keyId' => 'LTAIDl8gVgPhtUMX',
            'keySecret' => 'tbBKqgOqabMXNZeCZh1bDJPbqoaYXO',
            'phone' => '18662538931',
            'sign' => '质链SAAS云平台',
            'snscode' => 'SMS_120410443'
        );
    	dump(DySms::sendSms($data, ['code' => rand(100000, 999999)]));die;
        $str = 'AXLFahJjFH9Ue05jiPXuH8wiEiE';
        dump($str);
        dump(strrev('W'.$str.'J'));
        dump($aa = 'W'.i_base64encode(strrev($str)).'J');
        dump(substr($aa, 1, strlen($aa)-2));
    	echo "ok index";
    }
}