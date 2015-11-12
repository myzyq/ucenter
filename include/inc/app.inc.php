<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 用户信息操作控制器
  * @author d-boy
  * $Id: app.inc.php 110 2010-03-18 10:35:55Z lunzhijun $
  */
 class Inc extends BaseInc {
 	
 	function __construct() {
 		parent::__construct();
 		include_once FUN_ROOT . 'login.fun.php';
 		include_once FUN_ROOT . 'app.fun.php';
 		//include_once FUN_ROOT . 'groupapp.fun.php';
 		//include_once FUN_ROOT . 'group.fun.php';
 		include_once FUN_ROOT . 'user.fun.php';
 		include_once FUN_ROOT . 'appuserip.fun.php';
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){ 	 
 	 	$viewData = array();
 	 	$conn = $_ENV['db'];
 	 	global $curr_admin	;
 	 	$clientip = get_client_ip();
		$viewData['data'] = apps_the_user_can_login($conn, $curr_admin['id'], $clientip); //查询数据
		//close_db($conn);
 		parent::view('app_index', $viewData);
 	}
 	
 }
 
?>