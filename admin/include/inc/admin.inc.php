<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 管理员息操作控制器
  * @author d-boy
  * $Id: admin.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	//var $conn; //数据库连接
 	function __construct() {
 		parent::__construct();
		
 		include_once FUN_ROOT . 'admin.fun.php';
 		
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){ 	 
 	 	$viewData = array();
 	 	
 	 	$keys = $viewData['keys'] = empty($_REQUEST['keys']) ? array() : $_REQUEST['keys'];
 	 	$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);
 	 	$pageSize = empty($_REQUEST['pagesize']) ? 50 : intval($_REQUEST['pagesize']);
 	 	$viewData['msg'] = empty($_REQUEST['msg']) ? array() : $_REQUEST['msg'];
 	 	
 	 	$total = count_admins($this->conn, $keys);//总数 	
 	 			
		
		include_pager(); //引入分页控件
		$myPager = new Pager(array('page' => $page, 'total' => $total, 'pagesize' => $pageSize, 'always' => false)); //分页控初始化
		//返回当前页最小和最大记录号
		$start = $myPager->get_start_no();
		//$end = $myPager->get_end_no();
 	 	
		$viewData['data'] = search_admins($this->conn, $keys, $start, $pageSize);
		
 		parent::view('admin_index', $viewData);
 	}
 	
 	/**
 	 * 保存新增的管理员信息
 	 */
 	function _save() { 	
 		$username = $_REQUEST['admin_name'] ;
 		$msg = save_admin($this->conn, $username);
 		$flag = $msg['flag'] ? '1' : '0';
 		$param = "msg[flag]={$flag}&msg[msg]=" . urlencode($msg['msg']);
 		header("location:enter.php?m=admin&a=index&$param");
 	}
 	
 	/**
 	 * 批量删除信息
 	 */
 	function _batch_delete() {
 		$ids = $_REQUEST['ids'];
 		$accs = batch_delete_admin($this->conn, $ids);
 		
 		$msg = 'msg[flag]=1&msg[msg]=' . urlencode('成功删除' . count($accs) . '个，共' . count($ids) . '个');
 		
 		header("location:enter.php?m=admin&a=index&$msg");
 	}
 	
 	function __destruct() {
 		//close_db($this->conn);
 	}
 	
 }
 
?>