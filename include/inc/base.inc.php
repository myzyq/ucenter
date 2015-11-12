<?php
 defined('U_CENTER') or exit('Access Denied');
 /**
  * INC 基类
  * @author d-boy
  * $Id: base.inc.php 58 2010-02-24 10:04:13Z lunzhijun $
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
 }
?>