<?php 
/**
 * CLIENT API 初始化及入口文件
 * @author  d-boy
 * @copyright $Id$
 */
//date_default_timezone_set('Asia/Shanghai');
if(!defined('CURR_PATH')) define('CURR_PATH', str_replace('\\', '/', dirname(__FILE__))); //当前目录物理地址

include_once CURR_PATH . '/../header.php';
include_once CURR_PATH . '/client_api.int.php';
include_once CURR_PATH . '/api.class.php';
include_once CURR_PATH . '/client_api.imp.php';
include_once CURR_PATH . '/../common/cookie.class.php';

$todo = $_REQUEST[ParamKey::key_todo];
//$f = fopen('/home/htdocs/userlog.txt','w');
//fwrite($f,'api');
//fclose($f);
if(empty($todo)) {
	echo "err no param " ;
	exit;
}

$clientapi = new ClientApiImp();
//'app'=>app, 'token'=>token, 'api'=>api, 'param' => param
$config = array(
    'app' => APPID,
    'token' => APPTOKEN,
    'api' => $clientapi,
    'param' => $todo
);
$api = new Api($config);
header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA div COM NAV OTC NOI DSP COR"'); 
$api->go();

?>