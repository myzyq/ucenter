<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * IP信息操作控制器
  * @author d-boy
  * $Id: ip.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	//var $conn; //数据库连接
 	function __construct() {
 		parent::__construct();
		
 		include_once FUN_ROOT . 'ip.fun.php';
 		
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){ 	 
 	 	$viewData = array();
 	 	$conn = $_ENV['db'];
 	 	$viewData['data'] = all_ipgroup($conn);
 		parent::view('ip_index', $viewData);
 	}
 	
 	/**
 	 * 删除
 	 */
 	function _edit(){
 		if(empty($_REQUEST['id'])){
 			parent::message("", "参数错误,请重试");
 			return;
 		}
 		$id = intval($_REQUEST['id']);
 		$conn = $_ENV['db'];
 		$ipgroup = get_one_ip_group($conn, $id);
 		if(empty($ipgroup)){
 			parent::message("出错了", "您要修改的IP组不存在，可能已被删除了");
 			return;
 		}
 		$viewData = array('data' => $ipgroup);
 		parent::view('ip_edit', $viewData);
 	}
 	
 	/**
 	 * 保存新增的管理员信息
 	 */
 	function _save() {
 		if(empty($_REQUEST['info'])){
 			parent::message("", "参数错误,请重试");
 		} 	
 		$info =  $_REQUEST['info'];
 		$conn = $_ENV['db'];
 		$rs = insert_ip_group($conn, $info);
 		if(empty($rs)){
 			parent::message("失败","添加新的IP组出错");
 			return;
 		}
 		$forward = "enter.php?m=ip&a=index";
 	    parent::message("", "操作已成功", $forward);
 	}
 	
 	/**
 	 * 更新IP组信息
 	 */
 	function _update(){
 		if(empty($_REQUEST['id']) || empty($_REQUEST['info'])){
 			parent::message("", "参数出错，请重试");
 			return;
 		}
 		$id = intval($_REQUEST['id']);
 		$info = $_REQUEST['info'];
 		$conn = $_ENV['db'];
 		$result = update_ip_group($conn, $id, $info);
 		parent::message("","操作已成功，新信息已保存", "enter.php?m=ip&a=index");
 	}
 	
 	/**
 	 * 删除
 	 */
 	function _delete(){
 		if(empty($_REQUEST['id'])){
 			parent::message("","参数出错，请重试");
 			return;
 		}
 		$id = intval($_REQUEST['id']);
 		$conn = $_ENV['db'];
 		delete_ip($conn, $id);
 		parent::message("", "操作已成功", "enter.php?m=ip&a=index");
 	}
 	
 	/**
 	 * 添加IP组
 	 */
 	function _add(){
 	   parent::view('ip_add', array());
 	}
 	
 }
 
?>