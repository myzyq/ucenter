<?php defined('U_CENTER') or exit('Access Denied'); 
 /*==============================
  * frame inc 所用数据操作函数
  * @author d-boy
  * $Id: frame.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
  ==============================*/

	/**
	 * frame首页所用显示信息
	 * @return 首页所用显示信息
	 */
	function main_data() {
		$db = conn_db();
		include_once FUN_ROOT . 'app.fun.php';
		include_once FUN_ROOT . 'user.fun.php';
		include_once FUN_ROOT . 'group.fun.php';
		include_once FUN_ROOT . 'dep.fun.php';
		$viewData = array();
		
		//统计数据		
		$apps = count_apps($db);
		$users = count_users($db);
		$groups = count_group($db);
		$deps = count_depts($db);
		
		$viewData['stat'] = array(
			array('title' => '应用数' , 'link' => 'enter.php?m=app&a=index', 'count' => $apps),
			array('title' => '用户数' , 'link' => 'enter.php?m=user&a=index', 'count' => $users),
			array('title' => '用户组' , 'link' => 'enter.php?m=user&a=index', 'count' => $groups),
			array('title' => '部门' , 'link' => 'enter.php?m=user&a=index', 'count' => $deps),
		);
		
		//系统信息
		$mysqlv = get_mysql_version($db);
		$server = PHP_OS . '/PHP v' . PHP_VERSION . @ini_get('safe_mode') ? ' Safe Mode' : NULL;
		$fileupload = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : '<font color="red">'.$lang['no'].'</font>';
		$magic_quote_gpc = get_magic_quotes_gpc() ? 'On' : 'Off';
		$allow_url_fopen = ini_get('allow_url_fopen') ? 'On' : 'Off';	
		$dbsize = _get_db_size($db);
		$viewData['sys'] = array(
			array('title' => '操作系统及 PHP', 'info' => PHP_OS . '/PHP v' . PHP_VERSION ),
			array('title' => '服务器软件', 'info' => $_SERVER['SERVER_SOFTWARE'] ),
			array('title' => '主机名', 'info' => "{$_SERVER['SERVER_NAME']} ({$_SERVER['SERVER_ADDR']}:{$_SERVER['SERVER_PORT']})" ),
			array('title' => 'Mysql版本', 'info' => $mysqlv),
			array('title' => '当前数据库尺寸', 'info' => $dbsize),
			array('title' => '上传许可', 'info' => $fileupload),
			array('title' => 'magic_quote_gpc', 'info' => $magic_quote_gpc),
			array('title' => 'allow_url_fopen', 'info' => $allow_url_fopen),
		);
		
		close_db($db);
		//print_r($viewData);
		return $viewData;
	}
	
	/**
	 * 返回数据库大小
	 * @param $db 数据库连接对象
	 * @return 数据库大小
	 */
	function _get_db_size($db) {
		$tables = show_table_status($db, UCENTER_TABLE_PRE) ;
		$dbsize = 0; 
		foreach($tables as $table) {
			$dbsize += $table['Data_length'] + $table['Index_length'];
		}
		return $dbsize; 
	}
?>