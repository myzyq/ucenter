<?php
 defined('U_CENTER') or exit('Access Denied');
 /**
  * INC 基类
  * @author d-boy
  * $Id: base.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class BaseInc {
 	var $conn;
 	function __construct() {
 		$this->conn = $_ENV['db'];
 	}
 	
 	/**
 	 * 各INC必须有的方法_index 首页
 	 * @return index 页面
 	 */
 	 function _index(){
 	 	;
 	 }
 	
 	/**
 	 * 引入视图
 	 * @param $tmpl 模板名
 	 * @param $viewData 数据
 	 */
 	function view($tmpl, $viewData = array()) {
 		view($tmpl, $viewData);
 	}
 	
 	/**
 	 * 显示提示信息
 	 * @param $title 提示的TITLE
 	 * @param $message 信息
 	 * @param $expri 过期时间
 	 */
 	function message($title = '', $message = '', $forward = '', $expri = 0){
 		$viewData = array('title' => $title, 'message' => $message, 'forward' => $forward, 'expri' => $expri);
 		$this->view('message', $viewData);
 	}
 }
?>