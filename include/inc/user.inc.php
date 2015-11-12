<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 用户信息操作控制器
  * @author d-boy
  * $Id: user.inc.php 128 2010-05-07 03:59:00Z lunzhijun $
  */
 class Inc extends BaseInc {
 	
 	function __construct() {
 		parent::__construct();
 		include_once FUN_ROOT . 'user.fun.php';	
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){ 	 
 	 	$viewData = array();
 		parent::view('pwd_index', $viewData);
 	}
 	
    /**
     * 修改密码
     * @see include/inc/BaseInc#_index()
     */
     function _editpwd(){      
        $viewData = array();
        parent::view('pwd_index', $viewData);
    }
 	
    /**
     * 保存密码
     */
 	function _savepwd(){
 		$pwd = empty($_REQUEST["pwd"]) ? "" : $_REQUEST["pwd"];
 		$rpwd =  empty($_REQUEST["rpwd"]) ? "" : $_REQUEST["rpwd"];
 		$oldpwd = empty($_REQUEST["oldpwd"]) ? "" : $_REQUEST["oldpwd"];
 		$viewData = array();
 		$conn = $_ENV['db'];
 		$user = curr_user();
 		$userinfo = get_one_user($conn, $user['id']);
 		
 		if(empty($oldpwd) || md5($oldpwd) != $userinfo["password"]) {
 			 $viewData['msg'] = array("flag" => false, "msg" => "原密码不正确");
 		}elseif(empty($pwd) || strlen($pwd) < 6 || strlen($pwd) > 15 ){
 		     $viewData['msg'] = array("flag" => false, "msg" => "密码只能在6到15位之间");
 		}elseif($pwd != $rpwd){
 			 $viewData['msg'] = array("flag" => false, "msg" => "两次新密码不一至");
 		}else{
 			 $rs = change_password($pwd);
 			 $viewData["msg"] = $rs;
 			 
 		}
 		parent::view('pwd_index', $viewData);
 	}
 }
 	
 	
?>