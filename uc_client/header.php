<?php
/**
 * 头文件，于与引用常规库文件
 * @author:d-boy
 * $Id$
 */

if(!defined('API_PATH')) define('API_PATH', str_replace('\\', '/', dirname(__FILE__))); //CLIENT目录物理地址
if(!defined('API_COMMON_PATH'))  define('API_COMMON_PATH' , API_PATH . '/common/') ; //COMMON 目录

include_once API_PATH . '/../config.php';
include_once API_COMMON_PATH . 'ucsocket.class.php';
include_once API_COMMON_PATH . 'uccode.class.php';
include_once API_COMMON_PATH . 'customdes.class.php';
include_once API_COMMON_PATH . 'param_helper.class.php';
include_once API_COMMON_PATH . 'param_key.class.php';
include_once API_PATH . '/ucapi.class.php';

$initparam = array(
          'ucapi' => UC_WEB,
          'app' => APPID,
          'token' => APPTOKEN,
        );
        
$api = new UCApi($initparam); //API操作类

?>