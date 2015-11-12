<?php

/**
 * 权限验证
 * @author toryzen
 * $Id: enter.php 116 2014-06-04$
 */
ob_start();
define('U_CENTER', TRUE);
define('UC_ROOT', str_replace('\\', '/', dirname(__FILE__))); //网站目录物理地址
//引入配置文件
if (!@include_once(UC_ROOT . '/config.php')) {
	exit("缺少配置文件，请检查！！");
}
header('Content-type: text/html; charset=' . CHARSET);
date_default_timezone_set(TIMEZONE);
DEBUG ? error_reporting(E_ALL) : error_reporting(0);
define('THEME_RESOURCE', ROOT_PATH . 'themes/' . THEME . '/'); //主题资源目录
define('THEME_TEMPL', UC_ROOT . '/views/' . THEME . '/'); //模板目录
define('INC_ROOT', UC_ROOT . '/include/inc/'); //inc目录
define('FUN_ROOT', UC_ROOT . '/include/fun/'); //fun目录
define('JS_ROOT' , ROOT_PATH . 'js/');
require_once UC_ROOT . '/common/mysql.class.php';
require_once UC_ROOT . '/common/common.fun.php';
require_once UC_ROOT . '/uc_client/common/customdes.class.php';
require_once FUN_ROOT . 'conn.fun.php' ;
require_once FUN_ROOT . 'user.fun.php' ;
unset($GLOBALS, $_ENV);
$_ENV['db'] = conn_db();
$magic_quote = get_magic_quotes_gpc();


if($_GET['token']){
    //二次访问,用token换取email
    $vl = select($_ENV['db'],"SELECT * FROM center_auth WHERE token='".$_GET['token']."' AND flag = 1 LIMIT 1");
    if($vl){
        query($_ENV['db'],"UPDATE center_auth SET flag = 0  WHERE token='".$_GET['token']."' ");
        $return['flag']     = 1;
        $return['uid']      = $vl[0]['uid'];
        $return['username'] = $vl[0]['username'];
        $return['email']    = $vl[0]['email'];
        $return['password'] = $vl[0]['password'];
        $return['keys']     = $vl[0]['keys'];
    }else{
        $return['flag']=0;
    }
    echo json_encode($return);
}else{
    //首次访问获取Token
    $callback = $_GET['callback']; //来路地址
    $keys     = $_GET['keys'];     //标识
    $token    = md5(uniqid());     //Token
    $curr_admin = array();
    $user = curr_user();
    if($user){
        $curr_admin = get_one_user($_ENV['db'], $user['id']);
        $email = $curr_admin['email'];
        $hasAuth = select($_ENV['db'],"SELECT user_id FROM app_user_ip WHERE flag=0 AND user_id = ".$user['id']." AND app_id = (
                SELECT id FROM application WHERE api_addr = 'http://".str_replace("//","/",str_replace("uc_login","",str_replace("uc_login.html","",$callback)))."')");
        if($hasAuth){
            $hasUser = select($_ENV['db'],"SELECT uid,username,email_passwd FROM center_app.employee WHERE Email = '".$email."' AND del_info = 1 AND outdate = '0000-00-00' LIMIT 1");
            if($hasUser){
                query($_ENV['db'],"INSERT INTO center_auth (uid,username,email,password,callback,token,`keys`) values('".$hasUser[0]['uid']."','".$hasUser[0]['username']."','".$email."','".$hasUser[0]['email_passwd']."','$callback','$token','$keys')");
                header('Location:http://'.$callback."?token=".$token);
            }else{
                echo "<script>alert('用户不存在');window.location.href=('http://ucenter.gyyx.cn');</script>";
            }
        }else{
            echo "<script>alert('无权访问');window.location.href=('http://ucenter.gyyx.cn');</script>";
        }
    }else{
        header('Location:http://ucenter.gyyx.cn');
    }
}
ob_end_flush();
?>
