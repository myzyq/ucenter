<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 员工信息操作控制器
  * @author d-boy
  * $Id: member.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	
 	function __construct() {
 		parent::__construct();
 		include_once FUN_ROOT . 'member.fun.php' ;
 		include_once FUN_ROOT . 'user.fun.php' ;
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){
 	 	$conn = $_ENV['db']; //连接数据库连接
 	 	$viewData = array();
 	 	$viewData = search_member_data(false, $conn);
 	 	//echo "OK";
 	 	$viewData['msg'] = empty($_REQUEST['msg']) ? array() : $_REQUEST['msg'];
 	 	$viewData['alluser'] = search_user_by_keys($conn, array(), 0,0 );
 	 	//print_r($db);
 	 	//close_db($db);
 		parent::view('member_index', $viewData);
 	}
 	
 	/**
 	 * 保存用户信息
 	 */
 	function _save() {
 		$member = $_REQUEST['info'] ;
 		$conn = $_ENV['db'];			
 		
 		$id = save_member_data($conn, $member); //保存员工

 		$msg = '';
 		if(empty($id)) {
 			$msg = 'msg[flag]=false&msg[msg]=' . urlencode('员工信息保存失败，请重试！');
 		}
 		
 		header( 'Location: enter.php?m=member&a=index&' . $msg ) ;
 	}
 	
 	/**
 	 * 更新配置信息
 	 */
 	function _update() {
 	
 		$this->include_fun_file();
 		$info = $_POST['info'];
		$viewData = array();
		$id = $info['id'];
		$conn = $_ENV['db'];
		
 		$rs = update_member($conn, $info) ; 		
 		if(empty($rs)) {
 			$viewData['msg'] = array('flag'=>false, 'msg' => '更新员工信息失败！');
 		}else {
 			$viewData['msg'] = array('flag'=>true, 'msg' => '更新成功！');
 		}
 		$viewData['data'] = get_one_member($conn, $id);
 		close_db($conn);
 		//print_r($viewData);
 		parent::view('member_edit', $viewData);
 	}
 	
 	/**
 	 * 引入函数文件
 	 */
 	function include_fun_file() {
 		include_once FUN_ROOT . 'user.fun.php';
 	}
 	
 	/**
 	 * 编辑员工信息
 	 */
	function _edit() {
		$id = $_REQUEST['id'] ;//得到ID
		$conn = $_ENV['db']; //打开数据库连接
		
		$viewData = array() ;//页面所需的数据
		$data = get_one_member($conn, $id);
		if(empty($data)) {
			$viewData['data'] = array();
			$viewData['msg'] = array('flag'=>false, 'msg'=>'没有找到您想要的信息！');
		}else {
			$viewData['data'] = $data;
		}
		close_db($conn) ;//关闭数据库连接
		parent::view('member_edit', $viewData);
	}
	
	/**
	 * 批量删除
	 */
	function _bath_delete(){
		$ids = $_REQUEST['ids'];
		$conn = $_ENV['db'];
		$count = batch_delete_member($conn,$ids);
		$msg = 'msg[flag]=true&msg[msg]=' . urlencode('共'. count($ids) .'个，成功:'. count($count) . '个'); 
		close_db($conn);
		header('location:enter.php?m=member&a=index&' . $msg);
	}
 	
 }
 
?>