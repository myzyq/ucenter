<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 对话框操作函数库
 * @author d-boy
 * $Id: dialog.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/


	/**
	 * 查询员工信息
	 */
	function dialog_searchmember_data() {
		include_member(); 
		return search_member_data(true);
	}
	
	function dialog_getdept_data($conn, $keys) {
		include_dept();
		$data = search_depts($conn, $keys, 0, 0) ;
		return $data;
	}
	
	/**
	 * 引入员工操作函数库
	 */
	function include_member() {
		include_once FUN_ROOT . 'member.fun.php' ;
	}
	
	/**
	 * 引入部门操作函数库
	 */
	function include_dept() {
		include_once FUN_ROOT . 'dep.fun.php' ;
	}
	
?>