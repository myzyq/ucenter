<?php
/**
 * API基类
 * @author Administrator
 * $Id: baseapi.api.php 230 2010-12-10 03:22:21Z lunzhijun $
 */
class baseApi {
	var $conn ; //数据库连接
	var $uccode ; //编码类
		
	function __construct($config = array()) {
		if(!empty($config['conn']))		
			$this->conn = $config['conn'];
		else 
			$this->conn = conn_db();	
		$this->uccode = new UCCode();
	}

	/**
	 * 返回JSON序列化后的数据
	 * @param $data 原始数据
	 * @return 序列化后的数据
	 */
	function gen_output($data = array()) {
		return $this->uccode->serialize($data);
	}
	
	/**
	 * 引入分页控件
	 */
	function include_pager(){
		include_pager();
	}
	
	/**
	 * 错误信息
	 * @param  $msg 信息
	 * @return 错误信息
	 */
	function err($msg) {
		return array(FLAG_MSG => $msg, FLAG_FLAG => FLAG_ERR);
	}
	
	/**
	 * 返回操作成功的信息
	 * @param $msg 信息
	 * @param $other 其他信息
	 * @return 成功信息的数组
	 */
	function ok($msg = '' , $other = array()) {
		$result =  $other;
		$result[FLAG_MSG] = $msg ;
		$result[FLAG_FLAG] = FLAG_OK;
		return $result;
		
	}
	
	
	
}
?>