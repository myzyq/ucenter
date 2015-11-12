<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * AJAX操作控制器
  * @author d-boy
  * $Id: ajax.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	var $coder; //编码工具
 	const _AJAX_FLAG_OK = 'ok' ; //成功标志
 	const _AJAX_FLAG_ERR = 'err' ; //错误标志
 	const _AJAX_TAG_MSG = 'msg' ; //提示信息
 	const _AJAX_TAG_FLAG = 'flag' ; //标志TAG
 	const _AJAX_PARAM_BK = 'callback' ; //回调函数
 	const _AJAX_TAG_DATA = 'data'; //数据TAG
 	
 	
 	//var $conn; //数据库连接
 	function __construct() {
 		parent::__construct(); 
 		$this->coder = new UCCode();		
 		include_once FUN_ROOT . 'appuserip.fun.php';
 		include_once FUN_ROOT . 'groupapp.fun.php';
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){ 	 
 	 	//$rs = array(self::_AJAX_TAG_FLAG =>)
 	}
 	
 	/**
 	 * 一个组可访问的应用
 	 */
 	function _apps_of_one_group(){
 		if(empty($_REQUEST['gid'])) $this->out($this->gen_resulet(false, '参数不正确' , array()));
 		$gid = $_REQUEST['gid'] ;
 		$callback = empty($_REQUEST[self::_AJAX_PARAM_BK]) ? '' : $_REQUEST[self::_AJAX_PARAM_BK]; //回调函数
 		$appsOfOneGroup = get_group_app($this->conn, $gid) ;
 		$rs = $this->gen_resulet(true, '', $appsOfOneGroup); 
 		$this->out($rs, $callback);
 	}
 	
    /**
 	 * 几个组可访问的应用
 	 */
 	function _apps_of_groups(){
 		$callback = empty($_REQUEST[self::_AJAX_PARAM_BK]) ? '' : $_REQUEST[self::_AJAX_PARAM_BK]; //回调函数
 		if(empty($_REQUEST['gids'])) {$this->out($this->gen_resulet(true, '' , array()),$callback); exit();}
 		
 		$gids = $_REQUEST['gids'] ;
 		//print_r($gids);
 		
 		$apps = array();
 		foreach ($gids as $gid) {
 			$appsOfOneGroup = get_group_app($this->conn, $gid) ;
 			foreach($appsOfOneGroup as $a) {
 				$item = array('id' => $a['id'], 'app_name' => $a['app_name'], 'app_url' => $a['app_url']) ;
 				if(!in_array($item, $apps)) {
 					$apps[] = $item;
 				}
 			}
 		}
 		$rs = $this->gen_resulet(true, '', $apps); 
 		//print_r($rs);
 		$this->out($rs, $callback);
 	}
 	
 	/**
 	 * 生成结果
 	 * @param $flag 标志
 	 * @param $msg 提示
 	 * @param $data 数据
 	 * @return 要输出的信息
 	 */
 	private function gen_resulet($flag = false, $msg = '', $data = array()) {
 		$result = array();
 		$result[self::_AJAX_TAG_FLAG] = $flag ? self::_AJAX_FLAG_OK : self::_AJAX_FLAG_ERR;
 		$result[self::_AJAX_TAG_MSG] = $msg;
 		$result[self::_AJAX_TAG_DATA] = $data;
 		return $result;
 	}
 	
 	/**
 	 * 输出
 	 * @param $data 要输出的数据
 	 * @return ajax数据
 	 */
 	private function out($data, $callback = '') {
 		$json = empty($callback) ? $this->coder->serialize($data) 
 				: $callback . '(' . $this->coder->serialize($data) . ');'; 
 		echo $json;
 	}
 }
 
?>