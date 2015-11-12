<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * frame控制器
  * @author d-boy
  * $Id: frame.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
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
 		parent::view('frame_menu');
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