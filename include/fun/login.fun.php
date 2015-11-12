<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 登录操作函数库
 * @author d-boy
 * $Id: login.fun.php 88 2010-03-01 06:17:22Z lunzhijun $
 ========================*/

include_once FUN_ROOT . "user.fun.php";


/**
 * 登录操作
 * @param $name 用户名
 * @param $pwd 密码
 * @param $expri COOKIE 过期时间
 */
function login($name, $pwd, $expri){
    include_once UC_ROOT . "/uc_client/header.php";
    $result = array();
    //global $api;
    //echo $api;

//	var_dump($api);

    $data = $api->login("", $name, $pwd, $expri);
    if($api->is_ok($data)){
    	$result['flag'] = true;
    	$result['apis'] = $api->gen_login_sync($data);
    }else{
        $result['flag'] = false;
        $result['msg'] = "登录失败：" . $api->get_msg($data);
    }
    return $result;
}

/**
 * 登出
 */
function logout(){
	include_once UC_ROOT . "/uc_client/header.php";
	$user = curr_user();
	$result = array();
	if(empty($user)){
	   $result['flag'] = false;
	   $result['msg'] = "当前没有登录的用户";
	}
	 $data = $api->logout($user["id"], "");
	 if($api->is_ok($data)){
	 	$result = array("flag" => true , "msg" => "操作成功");
	 	$result['apis'] = $api->gen_logout_sync($data);
	 }else{
	   $result = array("flag" => false , "msg" => $api->get_msg($data));
	 }

	 return $result;
}


?>