<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 对话框控制器
  * @author d-boy
  * $Id: dialog.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	
 	function __construct() {
 		parent::__construct();
 		include_once FUN_ROOT . 'dialog.fun.php'; //引入数据函数库
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){
 		;
 	}
 	
 	/**
 	 * 查询员工信息action 
 	 */
 	function _search_member() {
 		$viewData = dialog_searchmember_data();
 		parent::view('diaog_member', $viewData);	
 	}
 	
 	/**
 	 * 查询部门信息
 	 * @return 部门信息
 	 */
 	function _search_dept() {
 		$conn = $_ENV['db'];
 		$viewData = array();
 		$keys = empty($_REQUEST['keys']) ? array() : $_REQUEST['keys'] ;
 		$viewData['callback'] = empty($_REQUEST['callback']) ? 'get_select_dept' : $_REQUEST['callback'];
 		$viewData['keys'] = $keys;
 		$viewData['data'] = dialog_getdept_data($conn, $keys);
 		//close_db($conn);
 		parent::view('diaog_dept', $viewData);
 	}
 	
 	
 	
 }
 
?>