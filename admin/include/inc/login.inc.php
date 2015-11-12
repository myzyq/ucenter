<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 用户登录操作控制器
  * @author d-boy
  * $Id: login.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	
 	function __construct() {
 		parent::__construct();
 		include_once FUN_ROOT . 'login.fun.php';
 		include_once FUN_ROOT . 'user.fun.php';
 		include_once FUN_ROOT . 'app.fun.php';
 		include_once FUN_ROOT . 'appuserip.fun.php';
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){ 	  	 	
 		parent::view('login');
 	}
 	
 	/**
 	 * 管理员登录
 	 */
 	function _login() {
 		ob_start();
 		$viewData = array();
 		$viewData['username'] = $username = $_REQUEST['username'];
 		$password = $_REQUEST['password'];
		
 		if(empty($username)) {
 			$viewData['msg'] = '请填写用户名';
 			parent::view('login', $viewData);
 			return false;
 		}
 		
 		if(empty($password)) {
 			$viewData['msg'] = '请填写密码';
 			parent::view('login', $viewData);
 			return false;
 		}
 		
 		$ip = get_client_ip(); //客户端IP 		
 		$conn = $_ENV['db'];
 		
 		$msg = uc_admin_login($conn, $username, $password, $ip); //登录操作
 		//close_db($conn); 		
 		if(!empty($msg)) {
 			$viewData['msg'] = $msg;
 			parent::view('login', $viewData);
 			return false;
 		}else {
 			while (@ob_end_clean()); 			
 			header('location:enter.php?m=frame&a=index');
 		}
 	}
 	
 	/**
 	 * 登出操作
 	 */
 	function _logout() {
 		uc_logout();
 		header('location:enter.php?m=login&a=index');
 	}
 	
 	/**
 	 * 用户可登录的列表
 	 */
 	function _applist() {
 		$viewData = array();
 		
 		$uid = $_REQUEST['uid'];
 		$uid = base64_decode($uid);
 		if(empty($uid)) {
 			$viewData['msg'] = array('flag' => false ,'msg' => '您好像没有登录，请登录后重试');
 			return parent::view('applist', $viewData);	
 		}
 		$conn = $_ENV['db'];	
 		$user = get_one_user($conn, $uid);
 		if(empty($user)) {
 			$viewData['msg'] = array('flag' => false ,'msg' => '您好像没有登录，请登录后重试');
 			return parent::view('applist', $viewData);	
 		}
 		$clientip = get_client_ip();
 		
 		//查询该用户所有有权限访问的应用
		$userapps = get_user_apps($conn, $uid, true);
		//print_r($userapps);
		if(empty($userapps)) {//没有应用被允许访问
			$viewData['msg'] = array('flag' => false ,'msg' => '没有被允许的应用');	
			return parent::view('applist', $viewData);	
		}
		$allowed = array();
		//print_r($userapps);
		//找出这些应用中用户IP不受限的 
		foreach($userapps as $appinfo) {
			$ips = $this->_get_user_legal_ips($conn, $user['id'], $appinfo['id']);	
			//print_r($ips);	
			if($this->_check_ip($ips, $clientip)) {
				$allowed[] = $appinfo;
			}
		}
		//print_r($allowed);
		if(empty($allowed)) { //所有应用都不允许这个用户IP登录
			$viewData['msg'] = array('flag' => false ,'msg' => '没有被允许的应用');	
			return parent::view('applist', $viewData);	
		}
		/*$data = array();
		foreach($allowed as $app) {
			$data[] = array('id' => $app['id'], 'app_name' => $app['app_name']);
		}*/
		//close_db($conn); 	
		$viewData['data'] = $allowed;	
		return parent::view('applist', $viewData);	
 	}
 	
   /**
	 * 得到用户合法的IP集合
	 * @param $uid 用户ID
	 * @param $appid APP ID
	 * @return 用户在要个应用下合法的IP地址
	 */
	private function _get_user_legal_ips($conn, $uid, $appid) {
		//用户自身IP绑定
		$user = get_one_user($conn, $uid);
		$userIP = empty($user['ip_band']) || empty($user['is_band']) ? array('[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}') : explode("\n", $user['ip_band']);
		
		//应用自身IP绑定
		$app = get_one_app($conn, $appid) ;
		$appIP = empty($app['is_band']) || empty($app['ip_band']) ? array('[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}') : explode("\n", $app['ip_band']) ;
		
		//应用绑定用户IP
		$appuser = get_appuserip($conn, $appid, $uid) ;
		$appUserIP = empty($appuser['is_band']) || empty($appuser['ip_band']) ? array('[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}') : explode("\n", $appuser['ip_band']);
		
		
		//print_r($appUserIP);
		$ip = array('user' => $userIP, 'app' => $appIP, 'appuserip' => $appUserIP);
	    //print_r($ip);		
		return $ip;
	}
	
    /**
	 * 验证IP合法性，用户自身，应用， 应用绑定用户IP都允许才行
	 * @param $ips array('user' => , 'app' => , 'appuserip' => )
	 * @param $ip 当前用户IP
	 * @return 返回结果
	 */
	private function _check_ip($ips = array() , $ip) {
		//print_r($ips['user']);
		return check_ip_arr($ips['user'], $ip) && check_ip_arr($ips['app'], $ip) && check_ip_arr($ips['appuserip'], $ip);
	}
 }
 
?>