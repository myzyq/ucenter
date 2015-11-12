<?php
/**
 * 入口文件
 * @author d-boy
 * $Id: enter.php 231 2010-12-10 03:53:11Z lunzhijun $
 */
error_reporting(0);
define('U_CENTER', TRUE);



define('UC_ROOT', str_replace('\\', '/', dirname(__FILE__))); //网站目录物理地址

//引入配置文件
if (!@include_once(UC_ROOT . '/config.php')) {
	exit("缺少配置文件，请检查！！");
}

header('Content-type: text/html; charset=' . CHARSET);

date_default_timezone_set(TIMEZONE);

#DEBUG ? error_reporting(E_ALL) : error_reporting(0);

define('THEME_RESOURCE', ROOT_PATH . 'themes/' . THEME . '/'); //主题资源目录
define('THEME_TEMPL', UC_ROOT . '/views/' . THEME . '/'); //模板目录
define('INC_ROOT', UC_ROOT . '/include/inc/'); //inc目录
define('FUN_ROOT', UC_ROOT . '/include/fun/'); //fun目录
define('JS_ROOT' , ROOT_PATH . 'js/');

include_once UC_ROOT . '/common/param_key.class.php';
include_once UC_ROOT . '/common/param_helper.class.php';
require_once UC_ROOT . '/common/mysql.class.php';
include_once UC_ROOT . '/common/uccode.class.php';
include_once UC_ROOT . '/common/customdes.class.php';
require_once FUN_ROOT . 'common.fun.php';
require_once FUN_ROOT . 'conn.fun.php' ;

unset($GLOBALS, $_ENV);
$_ENV['db'] = conn_db();

$magic_quote = get_magic_quotes_gpc();

if(empty($magic_quote)) {
	$_GET = checkform(new_addslashes($_GET));
	$_POST = checkform(new_addslashes($_POST));
}

include INC_ROOT . 'base.inc.php';

$m = empty($_REQUEST['m']) ? 'login' :  $_REQUEST['m']; //MODEL
$a = empty($_REQUEST['a']) ? 'index' :  $_REQUEST['a'] ; //函数
$curr_admin = array();



//检查登录情况
if(!in_array($m ,array('login', 'api')) ) {
	if(!has_login()) {
		echo "<script type='text/javascript'>alert('您还没有登录，先登录');parent.location.href='enter.php?m=login&a=index';</script>";
		exit;
	}

	$id = get_cookie(COOKIE_NAME);
	include_once FUN_ROOT . 'admin.fun.php' ;
	$curr_admin = get_admin_user($_ENV['db'], $id);
	if(empty($curr_admin)) {
		echo "<script type='text/javascript'>alert('您还没有登录，先登录');parent.location.href='enter.php?m=login&a=index';</script>";
		exit;
	}
}

$model = "{$m}.inc.php" ;
$method = "_$a" ;

if (!@include_once(INC_ROOT . $model )) {
	exit("Module not found!");
}

$inc = new Inc();
include_once UC_ROOT . '/include/sync.class.php' ;
$_ENV['sync'] = new Sync();

if(method_exists($inc, $method)) {
	$inc->$method();
}else {
	exit('Action not found!');
}

?>
