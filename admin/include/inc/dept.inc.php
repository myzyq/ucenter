<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 部门信息操作控制器
  * @author d-boy
  * $Id: dept.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	
 	function __construct() {
 		parent::__construct();
 		include_once FUN_ROOT . 'dep.fun.php' ;
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){ 	 	
 	 	$viewData = dept_index_data();
 		parent::view('dept_index', $viewData);
 	}
 	
 	/**
 	 * 保存用户信息
 	 */
 	function _save() {
 		$info = $_POST['info']; //添加的用户信息
 		
 		$conn = $_ENV['db'] ;
 		$msg = save_dept($conn, $info); 			
 		//close_db($db);
 		$msgstr = "msg[flag]={$msg['flag']}&msg[msg]={$msg['msg']}";
 	
 		header("location:enter.php?m=dept&a=index&{$msgstr}");
 	}
 	
 	/**
 	 * 更新部门信息
 	 */
 	function _update() {
 	
 		
 		$info = $_POST['info'];

 		$conn = $_ENV['db'];
 		$id = $info['id'];
 		$viewData['msg'] = update_dept_data($conn, $info) ;
 		
 		$data = get_one_dept($conn,$id) ;
 		$viewData['data'] = $data;
 		//close_db($conn);
 		//print_r($viewData);
 		parent::view('dept_edit', $viewData);
 	}
 	
	/**
	 * 编辑部门信息
	 * @return unknown_type
	 */
 	function _edit() {
 		$info = $_REQUEST['id'] ;
 		$viewData = array();
 		$conn = $_ENV['db'];
 		$data = get_one_dept($conn,$info) ;
 		//close_db($conn);
 		if(empty($data)) {
 			$viewData['msg'] = array('flag'=>FALSE,'msg'=>'没有找到您想要找的部门！');
 			$viewData['data'] = array();
 		}else {
 			$viewData['data'] = $data;
 		}
 		parent::view('dept_edit', $viewData);
 	}
 	
 	/**
 	 * 批量删除
 	 */
	function _bath_delete() {
		$ids = $_REQUEST['ids'];
		//print_r($ids);
		$conn = $_ENV['db'];
		$rows = batch_delete_dept($conn, $ids);
		$msg = '';
		$flag = FALSE;
		if(empty($rows)) {
			$msg = '删除失败！';
		}else{
			$flag = TRUE;
			$msg = '删除成功！';
		}
		//close_db($conn);
		$msg = urlencode($msg);
		header("location:enter.php?m=dept&a=index&msg[flag]=$flag&msg[msg]=$msg");
	}
 	
 }
 
?>