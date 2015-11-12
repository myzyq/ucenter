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
 		include_once FUN_ROOT . 'link.fun.php';
 	}

 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){
 	 	global $curr_admin;
 	 	$viewData = array();
 	 	$conn = $_ENV['db'];
 	 	if ($curr_admin['group_id'] != 7 && $curr_admin['group_id'] != 5) {
 	 		return parent::view('frame_main');
 	 	}
		$viewData['data'] = links_group_by_category($conn); //查询数据
		//close_db($conn);
 		return parent::view('link_index', $viewData);
 	}

 }

?>