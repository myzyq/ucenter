<?php
/**
 * API 入口  整个API采用JSON格式做传输 不采用SOAP
 * @author d-boy
 * $Id: api.php 230 2010-12-10 03:22:21Z lunzhijun $
 */

define('U_CENTER', TRUE);

define('UC_ROOT', str_replace('\\', '/', dirname(__FILE__))); //网站目录物理地址

//引入配置文件
if (!@include_once(UC_ROOT . '/config.php')) {
	exit("缺少配置文件，请检查！！");
}
date_default_timezone_set('UTC');

define('INC_ROOT', UC_ROOT . '/include/inc/'); //inc目录
define('FUN_ROOT', UC_ROOT . '/include/fun/'); //fun目录
define('API_ROOT', UC_ROOT . '/api/'); //API目录

define('FLAG_FLAG' , 'flag') ;//返回结果标志
define('FLAG_MSG' , 'msg') ; //返回结果的异常和提示等信息
define('FLAG_ERR' , 'err'); //FLAG 错误
define('FLAG_OK' , 'ok') ; //处理成功

include_once UC_ROOT . '/common/param_key.class.php';
include_once UC_ROOT . '/common/param_helper.class.php';
include_once UC_ROOT . '/common/mysql.class.php';
include_once UC_ROOT . '/common/uccode.class.php';
include_once UC_ROOT . '/common/customdes.class.php';

include_once FUN_ROOT . 'common.fun.php';

include_once FUN_ROOT . 'conn.fun.php' ;
include_once FUN_ROOT . 'app.fun.php' ;
include_once API_ROOT . 'baseapi.api.php';

$magic_quote = get_magic_quotes_gpc();
$paramhelper = new ParamHelper(array());

if(empty($magic_quote)) {
	$_GET = checkform(new_addslashes($_GET));
	$_POST = checkform(new_addslashes($_POST));
}

$todo = empty($_REQUEST[ParamKey::key_todo]) ? '' : $_REQUEST[ParamKey::key_todo];
$app = empty($_REQUEST[paramKey::key_app]) ? 0 : $_REQUEST[paramKey::key_app];

//找到这个APP的信息
$conn = conn_db();
$appinfo = get_one_app($conn, $app);


if(empty($appinfo)) { //没找到应用信息
    echo $paramhelper->serialize(array(FLAG_MSG => '应用验证失败,不存在这个应用', FLAG_FLAG => FLAG_ERR));
    exit;
}

$paramhelper->set_key($appinfo['token']);
$paramhelper->set_paramstr($todo);
$paramhelper->recover_param();
//echo "::$todo<br/>";
//echo "RRECOVER::{$paramhelper->paramstr}<br/>";
//print_r($paramhelper->get_param());
$app = $paramhelper->get_param_by_key(ParamKey::key_app); //app
$token = $paramhelper->get_param_by_key(ParamKey::key_token) ; //token

if(empty($app)) {
    echo $paramhelper->serialize(array(FLAG_MSG => '应用验证失败,参数不正确', FLAG_FLAG => FLAG_ERR));
    exit;
}


//校验口令
if($paramhelper->md5($token) != $appinfo['token']) {
    echo $paramhelper->serialize(array(FLAG_MSG => '应用验证失败', FLAG_FLAG => FLAG_ERR));
    exit;
}



$m = $paramhelper->get_param_by_key(ParamKey::key_model);
$a = $paramhelper->get_param_by_key(ParamKey::key_action);

if(empty($m) || empty($a)) {
	echo $paramhelper->serialize(array(FLAG_MSG => '没有找到函数', FLAG_FLAG => FLAG_ERR));
	exit;
}

$model = "{$m}.api.php" ;
$method = "$a" ;

if (!@include_once(API_ROOT . $model )) {
	echo $paramhelper->serialize(array(FLAG_MSG => '没有找到函数', FLAG_FLAG => FLAG_ERR));
	exit;
}


define('TOKEN', 'token'); //代表TOKEN的参数
define('APP' , 'app') ; //代表app_id的参数
define('MD5' , 'md') ; //防窜改的参数

//TODO 后期如有性能上的要求，加上连接池
/*验证APP合法性*/

$inc = new Api(array('conn'=>$conn), $paramhelper);

//校验参数完成性

//close_db($conn);

/*运行函数*/
if(method_exists($inc, $method)) {
	$inc->$method();
}else {
	echo $paramhelper->serialize(array(FLAG_MSG => '没有找到函数', FLAG_FLAG => FLAG_ERR));
	exit;
}

?>