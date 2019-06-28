<?php
namespace util;
use Config;
use Exception;
/**
 * Redis 连接
 * @author li shuaiqiu(asuma)
 * @version beta 1.3 自动获取redis方法(重写了redis的部分方法)
 */
class Redis{
	static private $_connect; //redis实例对象
	static private $ishold = 0; //是否为长连接 0 connect ; 1 pconnect
	static private $config; //redis 相关配置

	// 构造函数声明为private, 防止直接创建对象
	private function __construct(){}

	// 阻止用户复制对象实例
	public function __clone ()
	{
		throw new Exception('Clone is not allow');
	}

	static private function config()
	{
		if(!isset(self::$config)){
			$config = array(
				'host' => Config::get('this.redis_host') ?: '127.0.0.1',
				'port' => Config::get('this.redis_port') ?: '6379',
				'password' => Config::get('this.redis_password') ?: '',
				'prefix' => Config::get('this.redis_prefix') ?: '',
				'hold' =>  Config::get('this.redis_hold') === true ? 'pconnect' : self::$ishold == 1 ? 'pconnect' : 'connect'
			);

			self::$config = $config;
		}
		return self::$config;
	}

	static public function connection()
	{
		try {
			if (!isset(self::$_connect)) {
				self::$_connect = new \Redis();
				self::config();

				$persistent = self::$config['hold'];
				self::$_connect->$persistent(self::$config['host'], self::$config['port']); // 连接
				self::$config['password'] && self::$_connect->auth(self::$config['password']); // 密码
			}
			return self::$_connect;
		} catch (Exception $ex ) {
			return false;
		}
	}

	/**
	 * 设置redis连模式 1 pconnect ; 0 connect
	 * @return 返回0或1
	 */
	static public function hold($ishold)
	{
		$ishold == 1 && self::$ishold = $ishold;
		return self::$ishold;
	}

	/**
	 * 获取实际的redis缓存key
	 */
	static  public function getRedisKey($key)
	{
		return self::$config['prefix'].$key;
	}

	/**
	 * 当调用本类的方法不存在时
	 * @return 返回相应redis方法的结果
	 */
	static public function __callStatic($name, $args)
	{
		try {
			$newargs = &$args;
			!empty($newargs) && $newargs[0] = self::$config['prefix'].$newargs[0];

			self::connection();
			$result = call_user_func_array(array(self::$_connect, $name), $args);

			if(!$result){
				//当调用的redis方法第一个参数并不为redis-key的情况，取消掉默认key命名前缀再次查询结果
				//一般情况下，不会进入此处判断
				$newargs[0] = str_replace(self::$config['prefix'], '', $newargs[0]);
				$result = call_user_func_array(array(self::$_connect, $name), $args);
			}
			
			return !$result ? false : $result;	
		} catch (Exception $e) {
			i_log('redis调用错误或方法不存在');
			return false;
		}
	}

	/**
	 * 判断key是否存在
	 * @return 存在返回true,否则返回false
	 */
	static public function exists($key) {
		self::connection ();
		$result = self::$_connect->exists(self::getRedisKey($key));
		return $result ? true : false;
	}

	/**
	 * 查找所有符合给定模式(pattern)的key
	 * @return 返回具体数据
	 */
	static public function keys($pattern="")
	{
		self::connection();
		if(!$pattern){
			$pattern = self::$config['prefix'];
		}else if($pattern == '*'){
			$pattern = '';
		}

		return self::$_connect->keys($pattern.'*');
	}

	/**
	 * 删除已存在的key
	 * @return 返回被删除的数量
	 */
	static public function del($key)
	{
		self::connection();
		return self::$_connect->del(self::getRedisKey($key));
	}

	/**
	 * String操作 设置指定key的值及其过期时间，单位：秒
	 * @return 成功返回true,否则返回false
	 */
	static public function set($key, $value, $expire=0)
	{
		self::connection();
		if ($expire > 0) {
			return self::$_connect->setex(self::getRedisKey($key), $expire, $value);
		} else {
			return self::$_connect->set(self::getRedisKey($key), $value);
		}
	}

	/**
	 * String操作 获取键值
	 * @return 成功返回对应值，否则返回false
	 */
	static public function get($key)
	{
		self::connection();
		return self::$_connect->get(self::getRedisKey($key));
	}

	/**
	 * Hash操作 将哈希表 key 中的字段 field 的值设为 value
	 * @return 如果字段是哈希表中的一个新建字段，并且值设置成功，返回 1 。 如果哈希表中域字段已经存在且旧值已被新值覆盖，返回 0
	 */
	static public function hSet($key, $field, $value)
	{
		self::connection();
		return self::$_connect->hSet(self::getRedisKey($key), $field, $value);
	}

	/**
	 * Hash操作 获取存储在哈希表中指定字段的值
	 * @return 成功返回对应值，否则返回nil
	 */
	static public function hGet($key, $field)
	{
		self::connection();
		return self::$_connect->hGet(self::getRedisKey($key), $field);
	}

	/**
	 * Hash操作 同时将多个 field-value (域-值)对设置到哈希表 key 中
	 * @return 成功返回true
	 */
	static public function hmSet($key, $fvs=array())
	{
		self::connection();
		return self::$_connect->hmSet(self::getRedisKey($key), $fvs);
	}

	/**
	 * Hash操作 获取所有给定字段的值
	 * @return 成功返回所有给定字段的值
	 */
	static public function hmGet($key, $fs=array())
	{
		self::connection();
		return self::$_connect->hmSet(self::getRedisKey($key), $fs);
	}

	/**
	 * Hash操作 获取在哈希表中指定 key 的所有字段和值
	 * @return 以列表形式返回哈希表的字段及字段值。 若 key 不存在，返回空列表
	 */
	static public function hGetall($key)
	{
		self::connection();
		return self::$_connect->hGetall(self::getRedisKey($key));
	}

	/**
	 * Hash操作 删除一个或多个哈希表字段
	 * @return 被成功删除字段的数量
	 */
	static public function hDel($key, $field)
	{
		self::connection();
		return self::$_connect->hDel(self::getRedisKey($key), $field);
	}
}