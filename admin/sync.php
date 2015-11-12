<?php
/**
 * 同步信息入口
 * @author d-boy
 * $Id: sync.php 230 2010-12-10 03:22:21Z lunzhijun $
 */

define('U_CENTER', TRUE);



define('UC_ROOT', str_replace('\\', '/', dirname(__FILE__))); //网站目录物理地址

//引入配置文件
if (!@include_once(UC_ROOT . '/config.php')) {
    exit("缺少配置文件，请检查！！");
}

date_default_timezone_set('Asia/Shanghai');

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
ignore_user_abort(true);
include_once UC_ROOT . '/include/sync.class.php' ;
$sync = new Sync();
$sync->dosync();

?>