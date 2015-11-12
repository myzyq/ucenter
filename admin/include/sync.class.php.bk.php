<?php
require_once UC_ROOT . '/common/uccode.class.php';
require_once UC_ROOT . '/common/ucsocket.class.php';
require_once FUN_ROOT . 'app.fun.php';

if(!defined('NOTE_TABLE')) define('NOTE_TABLE',table_name(UCENTER_DBNAME, 'notelist',  UCENTER_TABLE_PRE)); //应用数据表名

/** 
 * 同步操作函数库
 * @author d-boy
 * $Id: sync.class.php.bk.php 230 2010-12-10 03:22:21Z lunzhijun $
 */
class Sync{

	var  $conn ; //数据库连接
	protected  $coder ; //编码类
	protected  $socket; //网络通信类
	
	const _MODEL_USER = 'user' ; //应用API用户模块
	const _ACTION_SYNCUSER = 'syncuser'; //同步用户信息ACTION
	const _ACTION_DELUSER = 'deluser'; //删除用户信息ACTION
	
	/*
	 * 参数
	 * u 用户名  base64加密
	 * i 用记ID base64加密
	 * t 应用的TOKEN base64加密的串
	 * p 用户密码md5 后再用base64加密
	 * e cookie过期时间 base64加密
	 * md md5参数串
	 * m 模块  base64加密  (user)
	 * a 方法  base64加密 (login, logout, syncuser)
	 * em 用户email base64加密
	 */
	const _PARAM_M = 'm' ; //参数名 模块
	const _PARAM_A = 'a' ; //参数名 动作
	const _PARAM_U = 'u' ; //参数名 用户名
	const _PARAM_I = 'i' ; //参数名 用户ID
	const _PARAM_T = 't' ; //参数名 应用TOKEN
	const _PARAM_P = 'p' ; //参数名 用户密码
	const _PARAM_E = 'e' ; //参数名 cookie过期时间
	const _PARAM_MD = 'md' ; //参数名 md5参数串
	const _PARAM_EM = 'em' ; //参数名 email
	
	public function __construct(){
		$this->conn = conn_db();
		$this->coder = new UCCode();
		$this->socket = new UCSocket();	 	
	}
	
	 /**
	  * 同步用户信息
	  * @param $uid 用户ID
	  * @return 同步结果
	  *//*
	public function sync_user($uid) {
		//查询应用信息 	
		$apps = $this->_all_apps() ;
		if(empty($apps)) return true;
		
		//查询用户信息
		$user = $this->_get_user($uid);
		if(empty($user)) return false;
		
		//同步信息
		foreach($apps as $app) {
			$url = $this->_url_syncuser($app['api_addr'], $user, $app['token']);
			//echo $url . '<br/>';
			$this->socket->fopen2($url);
		}
		return true;
	}*/
	
	/**
	 * 同步操作触发
	 */
	public function dosync(){
	//	$f = fopen('./synclog.txt', 'a+');
	//	fwrite($f, "\n start sync \t\t" . date('Y-m-d H:i:s'));
	//	fclose($f);
		register_shutdown_function(array($this, "_sync"));
	 //ignore_user_abort();
	//  set_time_limit(0);
	 // $this->_sync();
	}
	
	/**
	 * 实际的同步操作
	 */
	function _sync(){
	//	$f = fopen('/home/htdocs/synclog.txt', 'a+');
	//	fwrite($f, "\n" . "start _sync()" . "\t\t" . date('Y-m-d H:i:s'));
	//	try{
		//未执行的同步任务
		$task = $this->_activity_notes();
		if(empty($task)) return true;
		
		//所有应用信息
		$apps = $this->_all_apps() ;
		if(empty($apps)) return true;
		
		 
		
		//同步信息
		foreach($task as $t) {
			foreach($apps as $app) {
				$url = $this->_gen_sync_url($app['api_addr'] , $t['param'], $app['token']);
				
				$this->socket->fopen2($url);
				//fwrite($f, "\n" . $url . "\t\t" . date('Y-m-d H:i:s'));
			}
			$this->_done_sync($t['id']); //更新状态
		}
//		}catch(Exception $e){
//			fwrite($f, "\n" . $e-getMessage() . "\t\t" . date('Y-m-d H:i:s'));
//		}		
//		fclose($f);
	} 
	
	/**
	 * 返回所有未执行的note
	 * @return 还未执行的note
	 */
	 function _activity_notes() {
		$sql = "select * from " . NOTE_TABLE . " where stat = 0 ";
		$data = select($this->conn, $sql);
		return $data;
	}
	
	/**
	 * 同步任务结束
	 * @param  $noteid 同步任务ID
	 */
	 function _done_sync($noteid) {
		$noteid = intval($noteid);
		$sql = 'update ' . NOTE_TABLE . ' set stat = 1 where id = ' . $noteid;
		query($this->conn, $sql);
	}
	
	/**
	 * 添加同步任务
	 * @param $note 同步任务
	 */
	 function addnote($note) {
		return insert_data($this->conn, NOTE_TABLE, $note);
	}
	
	/**
	 * 组装NOTE参数
	 * @param  $m 模块
	 * @param  $a 动作
	 * @param  $other 其它参数串
	 * @return NOTE参数串
	 */
	 function _gen_note_param($m, $a, $other) {
		return $this->_app_action(self::_MODEL_USER, self::_ACTION_SYNCUSER)
			. '&' .  $other ;
	}
	
	/**
	 * 组装同步参数
	 * @param $note 通知参数
	 * @param $t 应用TOKEN
	 * @return 有效的同点参数串
	 */
	 function _gen_sync_param($note, $t) {
		return $note . '&' . $this->_app_token($t) ;
	}
	
	/**
	 * 组装同步URL
	 * @param  $url URL
	 * @param  $param 参数
	 * @return 有效的同步URL
	 */
	 function _gen_sync_url($url, $note, $t) {
		$url = $url . '?' . $this->_gen_sync_param($note, $t);
		return $url;
	}
	
	/**
	 * 组装用户同步param
	 * @param  $url API URL
	 * @param  $user 用户信息
	 * @param  $token 应用TOKEN
	 * @return 同步的URL
	 */
	 function _gen_sync_user_param($user) {
		return $this->_app_action(self::_MODEL_USER, self::_ACTION_SYNCUSER) 
			   . '&' . self::_PARAM_I . '=' . urlencode($this->coder->gen_64_code($user['id']))
			   . '&' . self::_PARAM_U . '=' . urlencode($this->coder->gen_64_code($user['name']))
			   . '&' . self::_PARAM_P . '=' . urlencode($this->coder->gen_64_code($user['password']))
			   . '&' . self::_PARAM_EM . '=' . urlencode($this->coder->gen_64_code($user['email']));
	}
	
	/**
	 * 添加同步用户信息的任务
	 * @param $uid 用户ID
	 */
	public function add_sync_user_note($uid){
		$user = $this->_get_user($uid);
		if(empty($user)) return false;
		$note = array();
		$note['operation'] = 'syncuser';
		$note['stat'] = 0;
		$note['param'] = $this->_gen_sync_user_param($user);
		$note['createtime'] = date('Y-m-d H:i:s');
		
		$id = $this->addnote($note);
		//$f = fopen('./synclog.txt', 'a+');
		//fwrite($f, "\n add a note {$id} \t\t" . date('Y-m-d H:i:s'));
		//fclose($f);
		return $id;
	}
	
	/**
	 * 添加删除用户同步任务
	 * @param  $uid 用户ID
	 */
	public function add_del_user_note($uid) {
		$node = array();
		$note['operation'] = 'deluser';
		$note['stat'] = 0;
		$note['param'] = $this->_gen_del_user_param($uid);
		$note['createtime'] = date('Y-m-d H:i:s');
		
		$id = $this->addnote($note);
	}
	
	/**
	 * 删除用户同步任务的参数
	 * @param $uid 用户ID
	 * @return 删除用户同步任务的参数
	 */
	private function _gen_del_user_param($uid) {
		return $this->_app_action(self::_MODEL_USER, self::_ACTION_DELUSER) 
			   . '&' . self::_PARAM_I . '=' . urlencode($this->coder->gen_64_code($uid));			
	}
	
	/**
	 * 返回所有的应用信息
	 * @return 应用信息
	 */
	function _all_apps() {
		$apps = search_apps($this->conn, array('flag'=>0), 0, 0);
		return $apps;
	} 
	
	/**
	 * 返回用户信息
	 * @param  $uid 用户ID
	 * @return 返回用户信息
	 */
	function _get_user($uid) {
		$user = get_one_user($this->conn, $uid);
		return $user;
	}
	
	/**
	 * 组装应用通信令牌
	 * @param  $token 口令MD5
	 * @return 通信令牌
	 */
	function _app_token($token = ''){
		return  self::_PARAM_T . '=' . urlencode($this->coder->gen_64_code($token));
	}
	
	/**
	 * 组装模块动作参数
	 * @param  $model 模块
	 * @param  $action 动作
	 * @return 组装模块动作参数
	 */
	function _app_action($model, $action) {
		return self::_PARAM_M . '=' . urlencode($this->coder->gen_64_code($model)) 
			   . '&' . self::_PARAM_A . '=' . urlencode($this->coder->gen_64_code($action));
	}
	
	
}