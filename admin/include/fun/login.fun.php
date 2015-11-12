<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 登录操作函数库
 * @author d-boy
 * $Id: login.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/

include_once FUN_ROOT . 'admin.fun.php' ;
include_once FUN_ROOT . 'settings.fun.php';


	/**
	 * 用户中心管理员登录
	 * @param $conn 数据库连接
	 * @param $username 用户名
	 * @param $password 密码
	 * @param $ip 用户登录IP
	 * @return 是否成功
	 */
	function uc_admin_login($conn, $username, $password, $ip) {
		//查询用户信息
		$msg = '未知错误，验证失败' ;
		
		$admin = uc_login_user_info($conn, $username); //查询用户信息
		//print_r( $admin);
		if(empty($admin)) { //用户不存在
			$msg = '用户名或密码错误';
		}else if(!uc_check_pwd($password, $admin['password'])){	//验证密码失败		
			$msg = '用户名或密码错误';
		}else if(!empty($admin['is_band']) && !empty($admin['ip_band']) && !check_ip($admin['ip_band'], $ip)){ //验证客户端IP失败
			$msg = '您的IP不被允许登录';
		}else if(!check_uc_ip($conn, $ip)) { //验证UCENTER系统IP绑定失败
			$msg = '系统不允许您的IP登录';
		}else {
			//验证通过，写COOKIE
			set_cookie(COOKIE_NAME, $admin['id']);
			$msg = '';
		}
		
		return $msg;
	}
	
	/**
	 * 验证用户中心是否允许某IP登录
	 * @param $conn 数据库连接
	 * @param $ip IP
	 * @return 用户中心是否允许该IP登录
	 */
	function check_uc_ip($conn, $ip) {
		$result = false;
		
		//查询配置信息
		$settings = get_settings($conn);
		
		if(empty($settings['is_band']) || empty($settings['is_band']['v']) ||  empty($settings['ip_band']) || empty($settings['ip_band']['v'])) {
			$result = true;
		} else {
			
			$result = check_ip($settings['ip_band']['v'], $ip);
		}
		
		return $result;
	}
	
	/**
	 * 验证IP是否在绑定的IP里员
	 * @param $ipband 绑定IP信息
	 * @param $ip 客户端IP
	 * @return 是否正确
	 */
	function check_ip($ipband, $ip){
		//echo $ipband;
		$arr = explode("\n" , $ipband);
		
		//print_r($arr);
		return check_ip_arr($arr, $ip);
	}
	
	/**
	 * 验证IP是否在集合中
	 * @param $arr_ip IP集合
	 * @param $ip IP
	 * @return IP是否在合法集合中
	 */
	function check_ip_arr($arr_ip = array() , $ip) {
		$result = false;
		foreach($arr_ip as $item) {
			$item = str_replace(array('.','*'), array('\.','[0-9]{1,3}') , $item);
			//echo $item;
			if(preg_match('/^' . $item . '$/i', $ip)) {
				$result = true;
				break;
			}	
		}
		return $result;
	}
	
	/**
	 * 返回管理员用户信息
	 * @param $conn 数据库连接
	 * @param $name 用户名
	 * @return 管理员用户信息
	 */
	function uc_login_user_info($conn, $name) {
		return get_admin_by_name($conn, $name);
	}
	
	/**
	 * 验证密码
	 * @param $pwd 用户输入的密码
	 * @param $cpwd 用户正确的密码
	 * @return 密码是否正确
	 */
	function uc_check_pwd($pwd, $cpwd ) {
		$pwd = md5($pwd);
		return $pwd === $cpwd;
	}	
	
	/**
	 * 登出操作
	 */
	function uc_logout() {
		clear_cookie(COOKIE_NAME);
	}
	

?>