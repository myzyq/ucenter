<?php
 defined('U_CENTER') or exit('Access Denied');

 /**
  * frame控制器
  * @author d-boy
  * $Id: frame.inc.php 3 2010-02-04 01:29:56Z lunzhijun $
  */
 class Inc extends BaseInc {

 	function __construct() {
 		parent::__construct();

 	}

 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){
 		parent::view('main');
 	}

 	/**
 	 * frame 头
 	 * @return 显示头
 	 */
 	function _header() {
 		global $curr_admin;
 		$viewData = array('userName' => $curr_admin['name']);
 		parent::view('frame_header', $viewData);
 	}

 	/**
 	 * fream 菜单
 	 * @return 显示菜单
 	 */
 	function _menu() {
 		global $curr_admin;
 		include_once FUN_ROOT . 'group.fun.php';
 		$viewData = array();
 		$conn = $_ENV['db'] ;
 		$viewData['user_group'] = get_one_group($conn, $curr_admin['group_id']);
 		parent::view('frame_menu', $viewData);
 	}

 	/**
 	 * 显示frame主体页
 	 * @return unknown_type
 	 */
 	function _main() {
 		include_once FUN_ROOT . 'frame.fun.php' ;
 		$viewData = main_data();

 		//quick menu
 		$viewData['quick_menu'] = array();

 		parent::view('frame_main', $viewData);
 	}
 }

?>