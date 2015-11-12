<?php
require_once UC_ROOT . '/common/uccode.class.php';
require_once UC_ROOT . '/common/ucsocket.class.php';
require_once FUN_ROOT . 'app.fun.php';

if(!defined('NOTE_TABLE')) define('NOTE_TABLE',table_name(UCENTER_DBNAME, 'notelist',  UCENTER_TABLE_PRE)); //应用数据表名

/**
 * 同步操作函数库
 * @author d-boy
 * $Id: sync.class.php 239 2010-12-23 09:32:18Z lunzhijun $
 */
class Sync{

	var  $conn ; //数据库连接
	protected  $coder ; //编码类
	protected  $socket; //网络通信类
	private $parhelper;

	public function __construct(){
		$this->conn = conn_db();
		$this->coder = new UCCode();
		$this->socket = new UCSocket();

	}

	private function init_parhelper(){
		if (!$this->parhelper) {
		  $this->parhelper  = new ParamHelper("");
		}
		$this->parhelper->set_paramstr('');
		$this->parhelper->set_param(array());
	}


	/**
	 * 同步操作触发
	 */
	public function dosync(){

	   //register_shutdown_function(array($this, "_sync"));
       $this->_sync();
	}

	/**
	 * 实际的同步操作
	 */
	function _sync(){
		$f = fopen('synclog.txt', 'a+');
     //	fwrite($f, "\n" . "start _sync()" . "\t\t" . date('Y-m-d H:i:s'));
	//	try{
		//未执行的同步任务
		$task = $this->_activity_notes();
		if(empty($task)) return true;
		//print_r($task);
		//所有应用信息
		$apps = $this->_all_apps() ;
		//print_r($apps);
		if(empty($apps)) return true;



		//同步信息
		foreach($task as $t) {
			$paramarr = $this->coder->unserialize($t['param']);
			$doapps = empty($paramarr['app']) ? array() : $paramarr['app'] ;
			foreach($apps as $app) {
				if(!empty($doapps) && !in_array($app['id'], $doapps)){
					continue;
				}
				$url = $this->_gen_sync_url($app['api_addr'] , $t['param'], $app['token']);
				//fwrite($f, "\n" . $url . "\t\t" . date('Y-m-d H:i:s'));
				$this->socket->fopen2($url);
				fwrite($f, "\n" . $url . "\t\t" . date('Y-m-d H:i:s'));
			}
			$this->_done_sync($t['id']); //更新状态
		}
//		}catch(Exception $e){
//			fwrite($f, "\n" . $e-getMessage() . "\t\t" . date('Y-m-d H:i:s'));
//		}
		fclose($f);
	}

	/**
	 * 返回所有未执行的note
	 * @return 还未执行的note
	 */
	 function _activity_notes() {
		$sql = "select * from " . NOTE_TABLE . " where stat = 0 and uc_sign='" . UCSIGN . "'";
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
	 * 组装同步参数
	 * @param $note 通知参数
	 * @param $t 应用TOKEN
	 * @param $apps 应用列表 array('1','2')
	 * @return 有效的同点参数串
	 */
	 function _gen_sync_param($note, $t, $apps = array()) {
	 	$this->init_parhelper();
	 	$param = $this->parhelper->unserialize($note);
	 	$this->parhelper->set_param($param);
	 	$this->parhelper->append_param(ParamKey::key_token, $t);

	 	$this->parhelper->set_key($t);
	 	//print_r($note);
	 	//print_r($this->parhelper->get_param());
	 	$code = $this->parhelper->serialize_param();
		return $code ;
	}

	/**
	 * 组装同步URL
	 * @param  $url URL
	 * @param  $param 参数
	 * @return 有效的同步URL
	 */
	 function _gen_sync_url($url, $note, $t) {
		$url = $url . '?' . ParamKey::key_todo . '=' . urlencode($this->_gen_sync_param($note, $t));
		return $url;
	}

	/**
	 * 组装用户同步param
	 * @param  $url API URL
	 * @param  $user 用户信息
	 * @param  $token 应用TOKEN
	 * @param  $app  应用ID列表 array('1','2')
	 * @return 同步的URL
	 */
	 function _gen_sync_user_param($user, $app = array()) {
	 	$this->init_parhelper();
	 	$this->parhelper->append_param(ParamKey::key_action, ParamKey::SYNC_USER);
	 	$this->parhelper->append_param(ParamKey::key_uid, $user['id']);
	 	$this->parhelper->append_param(ParamKey::key_user_name, base64_encode($user['name']));
	 	//$this->parhelper->append_param(ParamKey::key_user_name, $user['name']);
	 	$this->parhelper->append_param(ParamKey::key_password, $user['password']);
	 	$this->parhelper->append_param(ParamKey::key_email, $user['email']);
	    if(!empty($app)){
            $this->parhelper->append_param('app' , $app);
        }
	 	$str = $this->parhelper->get_param_serialize();
	 	//$str = urlencode($str);
	 	return $str;
	}

	/**
	 * 添加同步用户信息的任务
	 * @param $uid 用户ID
	 * @param $apps 应用ID array('1','2')
	 */
	public function add_sync_user_note($uid, $apps = array()){
		$user = $this->_get_user($uid);
		if(empty($user)) return false;
		$note = array();
		$note['operation'] = 'syncuser';
		$note['stat'] = 0;

		$note['param'] = $this->_gen_sync_user_param($user, $apps);
		$note['createtime'] = date('Y-m-d H:i:s');

		//多系统同步
		$id = 0;
		$signs = $this->_get_ucsigns()	;

		if (!empty($signs) ){
			foreach($signs as $sign){
				 $note['uc_sign'] = $sign;
			     $id += $this->addnote($note);
			}
		}

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
		//$note['param'] = urlencode($note['param']);
		$note['createtime'] = date('Y-m-d H:i:s');
		//多系统同步
        $signs = $this->_get_ucsigns()  ;
        if (!empty($signs) ){
            foreach($signs as $sign){
              $note['uc_sign'] = $sign;
		      $id = $this->addnote($note);
            }
        }
	}

	/**
	 * 返回同步触发器
	 */
	public function gen_triggers(){
		global $UCENTERS;
		$triggers = array();
		if(!empty($UCENTERS)) {
			foreach($UCENTERS as $k => $v){
			     $triggers[] = "{$v}sync.php";
			}
		}
		return $triggers;
	}

	/**
	 * 返回同步的触发器
	 */
	public function gen_triggers_img(){
		$tiggers = $this->gen_triggers();
		$code = "";
		if(!empty($tiggers)){
			foreach($tiggers as $t){
				$code .= "<img src='$t'  style='display:none;width:0;height:0' />";
			}
		}
		return $code;
	}

	/**
	 * 删除用户同步任务的参数
	 * @param $uid 用户ID
	 * @return 删除用户同步任务的参数
	 */
	private function _gen_del_user_param($uid) {
		/**return $this->_app_action(self::_MODEL_USER, self::_ACTION_DELUSER)
			   . '&' . self::_PARAM_I . '=' . urlencode($this->coder->gen_64_code($uid));
	    **/
		$this->init_parhelper();
        $this->parhelper->append_param(ParamKey::key_action, ParamKey::SYNC_DELUSER);
        $this->parhelper->append_param(ParamKey::key_uid, $uid);
        $str = $this->parhelper->get_param_serialize();
        return $str;
	}

	/**
	 * 返回所有的应用信息
	 * @return 应用信息
	 */
	private function _all_apps() {
		$apps = search_apps($this->conn, array('flag'=>0, 'uc_sign' => UCSIGN), 0, 0);
		return $apps;
	}

	/**
	 * 返回用户信息
	 * @param  $uid 用户ID
	 * @return 返回用户信息
	 */
	private function _get_user($uid) {
		$user = get_one_user($this->conn, $uid);
		return $user;
	}

	/**
	 * 返回所有的UCSIGN
	 */
	private function _get_ucsigns(){
		/*include_once FUN_ROOT . 'settings.fun.php';
        $sign = get_one_setting($this->conn, 'ucsign');
        if(empty($sign)){
            return array();
        }
        $signs = explode(';', $sign['v']);
        */
		global $UCENTERS;
        $signs = array_keys($UCENTERS);
        return $signs;
	}



}