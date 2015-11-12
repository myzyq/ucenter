<?php
/**
 * 常用函数
 * @author d-boy
 * $Id: common.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 */
defined('U_CENTER') or exit('Access Denied');


//SQL ADDSLASHES
function new_addslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = new_addslashes($val);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}

//SQL ADDSLASHES
function checkform($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = checkform($val);
		}
	} else {
		$string = mysql_escape_string($string);
	}
	return $string;
}

/**
 * 返回模板
 * @param $tmp 模板名
 * @param $master 母板名
 * @param $viewData 数据
 * @return 引入模板的地址
 */
function view($tmp, $viewData = array(), $master = '') {
	$target =  THEME_TEMPL . "{$tmp}.tpl.php";
	//print_r($viewData);
	if(empty($master)) {
		include $target;
	}else {		
		include $master;
	}
}


/**
 * 返回 link css HTML代码
 * @param $file 文件名
 * @param $path 目录名
 * @return link css HTML代码
 */
function css_link($file, $path) {
	return "<link rel='stylesheet' href='{$path}{$file}' type='text/css' media='all' />";
}

/**
 * 返回引入JS的 Html代码
 * @param $file 文件名
 * @param $path 目录名
 * @return 引入js的代码
 */
function script_link($file, $path) {
	return "<script type='text/javascript' src='{$path}{$file}'></script>";
}

/**
 * 返回 content-type HTML代码
 * @param  $type content-type
 * @param  $charset 字符集
 * @return content-type HTML代码
 */
function content_type($type = 'text/html', $charset = 'gb2312') {
	return "<meta http-equiv='Content-Type' content='{$type}; charset={$charset}' />";
}



/**
  * 产生随机字串，可用来自动生成密码 
  * 默认长度6位 字母和数字混合
  * @param $format ALL NUMBER CHAR 字串组成格式
  * @param $len 长度
  * @return 字母和数字混合串
  */
function rand_str($len=6, $format='ALL') { 
	switch($format) { 		
		case 'CHAR':
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@~=!$*'; break;
		case 'NUMBER':
			$chars = '0123456789'; break;
		case 'ALL':
		default :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@~=!$*'; 
		break;
	}
	mt_srand((double)microtime()*1000000*getmypid()); 
	$password="";
	while(strlen($password) < $len)
		$password .= substr($chars,(mt_rand()%strlen($chars)),1);
		
	return $password;
}

/**
 * 引入分页类
 */
function include_pager() {
	include UC_ROOT . '/common/pager.class.php' ;
}

/**
 * 得到客户端IP
 * @return IP地址
 */
function  get_client_ip(){
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
           $ip = getenv("HTTP_CLIENT_IP");
       else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
           $ip = getenv("HTTP_X_FORWARDED_FOR");
       else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
           $ip = getenv("REMOTE_ADDR");
       else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
           $ip = $_SERVER['REMOTE_ADDR'];
       else
           $ip = "unknown";
   return($ip);
}




/**
 * 写COOKIE
 * @param $var COOKIE名
 * @param $value COOKIE值
 * @param $time 过期时间
 */
function set_cookie($var, $value = '', $time = 0) {
	$time = $time == 0 ? 0  : time() + intval($time) ;
	$var = COOKIE_PRE . $var;
	$value = CustomDES::encrypt_decrypt($value, CustomDES::ENCODE, AUTH_KEY);
	setcookie($var, $value, $time, COOKIE_PATH, COOKIE_DOMAIN);
}

/**
 * 返回当前的URL
 */
function curr_page_url() {  
   $pageURL = 'http';  
  if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}  
      $pageURL .= "://";  
  if ($_SERVER["SERVER_PORT"] != "80") {  
   $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];  
  } else {  
   $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];  
  }  
  return $pageURL;  
 }  

/**
 * 清除COOKIE
 * @param  $var COOKIE名
 */
function clear_cookie($var) {
	$time = time() - 3000 ;
	$var = COOKIE_PRE . $var;
	$value = '';
	setcookie($var, $value, $time, COOKIE_PATH, COOKIE_DOMAIN);
}

/**
 * 读COOKIE
 * @param  $var COOKIE名
 * @return COOKIE内容，解密后的
 */
function get_cookie($var) {
	$var = COOKIE_PRE . $var;
	$value = '' ;
	if(isset($_COOKIE[$var])) {
		$value = CustomDES::encrypt_decrypt($_COOKIE[$var], CustomDES::DECODE , AUTH_KEY)  ;
	}
	return $value;	
}

/**
 * 是否有用户登录
 * @return 是否有用户登录
 */
function has_login() {
	$cookie = get_cookie(COOKIE_NAME) ;
	//echo $cookie;
	return !empty($cookie);
}


?>