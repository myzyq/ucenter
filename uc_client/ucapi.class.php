<?php

/**
 * UCENTER 接口 操作类
 * @author d-boy
 * $Id: ucapi.class.php 151 2010-05-14 03:13:18Z lunzhijun $
 */
class UCApi{
	const _MODEL_USER = 'user' ; //用户信息API模块名
	const _ACTION_LOGIN = 'login' ; //登录API ACTION名
	const _ACTION_LOGOUT = 'logout' ; //注销API ACTION 名
	const _ACTION_GET_USER = 'get_user' ; //返回一个用户信息的ACTION
	const _ACTION_REG_USER = 'reg_user' ; //向UCENTER注册一个用户
	const _ACTION_DEL_USER = 'del_user' ; //向UCENTER申请删除一个用户
	const _ACTION_UPDATE_USER = 'update_user' ; //更改一个用户的信息
	const _ACTION_DEL_USERAPP_RELATION = 'delete_user_app' ; //删除一个用户和APP的关联
	
	var $socket ; //socket
	var $ucapi ; //UCENTER api URL
	var $uccode; //UCENTER 编码操作类
	var $paramhelper; //参数操作工具类
	var $app ; //APPID
	var $token; //APP口令
	function __construct($config = array()) {
		$this->socket = new UCSocket();
		$this->ucapi = $config['ucapi'];
		$this->app = $config['app'];
		$this->token = $config['token'];
		$this->uccode = new UCCode();
	} 
	
	/**
	 * 将APP ID 加入到参数内
	 */
	private function append_app(){
		$this->paramhelper->append_param(ParamKey::key_app, $this->app);
	}
	
	/**
	 * 初始话请求串
	 */
	private function init_url(){
		return $this->ucapi . '?' . ParamKey::key_app . '=' . $this->app;
	}
	
	/**
	 * 重新初始化PARAM HELPER
	 */
	private function re_init_paramhelper() {
		if(empty($this->paramhelper)) {
			$helpertoken = $this->uccode->md5($this->token);
	        $this->paramhelper = new ParamHelper($helpertoken);
		}
		$this->paramhelper->set_paramstr('');
		$this->paramhelper->set_param(array());
		$this->paramhelper->append_param(ParamKey::key_app, $this->app);
		$this->paramhelper->append_param(ParamKey::key_token, $this->token);
	}
	
	
    /**
     * 登录接口
     * @param $userName 用户名
     * @param $password 密码
     * @param $expri COOKIE过期时间
     * @return 登录操作结果
     */
    public function login($forward = '' ,$userName = '', $password = '', $expri = 0) {
        /* ==========================
         * 登录URL参数
         * user_name 用户名
         * password 密码
         * expir COOKIE过期时间
         * forward 目标的址
         ====================*/     
         //$url = $this->ucapi . '?' . $this->_gen_model_action(self::_MODEL_USER, self::_ACTION_LOGIN) 
         //     . '&' . $this->_gen_app_token($app, $token) 
         //     . '&user_name=' . urlencode($userName) . '&password=' . $password . '&forward=' . urlencode($forward)
         //     . '&expir=' . $expri . '&ip=' . $this->get_client_ip();
         //echo $url;
         $this->re_init_paramhelper();
         $url = $this->init_url();
         $this->paramhelper->append_param(ParamKey::key_user_name, $userName);
         $this->paramhelper->append_param(ParamKey::key_password, $password);
         $this->paramhelper->append_param(ParamKey::key_forward, $forward);
         $this->paramhelper->append_param(ParamKey::key_expir, $expri);
         $this->paramhelper->append_param(ParamKey::key_ip, $this->get_client_ip());
         $this->paramhelper->append_param(ParamKey::key_action, self::_ACTION_LOGIN);
         $this->paramhelper->append_param(ParamKey::key_model, self::_MODEL_USER);

//			var_dump( $this->paramhelper->get_param());
         $code = $this->paramhelper->serialize_param();

//         echo "<p>$code</p>";
//         $this->paramhelper->set_paramstr($code);
//         $this->paramhelper->recover_param();
//          print_r( $this->paramhelper->get_param());
//         echo $this->get_client_ip();


		$code = urlencode($code);

//		echo $code.'<br />';


		$url = $url . '&' . ParamKey::key_todo . '=' . $code;
//		echo $url;
//		die;
		//print_r($this->paramhelper->get_param());
        
         $return = $this->socket->fopen2($url);
         $data = $this->unserialize($return);
         //print_r($data);
         return $data ; 
    }
	
	/**
	 * 更新用户信息
	 * @param $app APP ID
	 * @param $token APP TOKEN
	 * @param $info 用户数据
	 * @return 操作结果
	 */
	public function update_user($info = array()){
		/* ==========================
		 * 更新用户信息URL参数
		 * uid 用户ID
	 	 * password 密码
	 	 * email Email
	 	 * app 应用ID
	 	 * token 应用口令
		 ====================*/		
		
		/* $url = $this->ucapi . '?' . $this->_gen_model_action(self::_MODEL_USER, self::_ACTION_UPDATE_USER) 
		 		. '&' . $this->_gen_app_token($app, $token) 
		 		. '&uid=' . urlencode($this->uccode->gen_64_code($info['id'])) 
		 		. '&password=' .  urlencode($this->uccode->gen_64_code($info['password'])) 
		 		. '&email=' .  urlencode($this->uccode->gen_64_code($info['email'])) ;
		 //echo $url;
		  * 
		  */
		 $this->re_init_paramhelper();
         $url = $this->init_url();
         $this->paramhelper->append_param(ParamKey::key_uid, $info['id']);
         $this->paramhelper->append_param(ParamKey::key_password, $info['password']);
         $this->paramhelper->append_param(ParamKey::key_email, $info['email']);
         $this->paramhelper->append_param(ParamKey::key_action, self::_ACTION_UPDATE_USER);
         $this->paramhelper->append_param(ParamKey::key_model, self::_MODEL_USER);
         $code = $this->paramhelper->serialize_param();
         $code = urlencode($code);
         
         $url = $url . '&' . ParamKey::key_todo . '=' . $code;
         //echo $url;
		 $return = $this->socket->fopen2($url);
		 $data = $this->unserialize($return);
		 //print_r($data);
		 return $data ;  		
	} 

	/**
	 * 删除一个用户和这个应用的关联
	 * @param $app APP ID
	 * @param $token APP TOKEN
	 * @param $uid 用户ID
	 */
	public function delete_user_app_relation($uid) {
		/* ==========================
		 * 删除用户信息URL参数
		 * uid 用户ID
	 	 * password 密码
	 	 * email Email
	 	 * app 应用ID
	 	 * token 应用口令
		 ====================*/		
		/*
		 $url = $this->ucapi . '?' . $this->_gen_model_action(self::_MODEL_USER, self::_ACTION_DEL_USERAPP_RELATION) 
		 		. '&' . $this->_gen_app_token($app, $token) 
		 		. '&uid=' . urlencode($this->uccode->gen_64_code($uid)) ;
		
		 //echo $url;
		  * 
		  */
		 $this->re_init_paramhelper();
         $url = $this->init_url();
         $this->paramhelper->append_param(ParamKey::key_uid, $uid);
         $this->paramhelper->append_param(ParamKey::key_action, self::_ACTION_DEL_USERAPP_RELATION);
         $this->paramhelper->append_param(ParamKey::key_model, self::_MODEL_USER);
         $code = $this->paramhelper->serialize_param();
         $code = urlencode($code);
         //print_r($info);
         $url = $url . '&' . ParamKey::key_todo . '=' . $code;
		 $return = $this->socket->fopen2($url);
		 $data = $this->unserialize($return);
		 return $data ;  		
	}
	

	
	/**
	 * 重新返回同步代码
	 * @param $forward
	 * @param $userName
	 * @param $password
	 * @param $expri
	 * @return 同步数据
	 */
	public function regen_sync_login( $forward = '' ,$userName = '', $password = '', $expri = 0) {
		return $this->login($forward  ,$userName, $password , $expri);
	}
	
	/**
	 * 登出操作
	 * @param $uid 用户ID
	 * @return 注销信息
	 */
	public function logout($uid , $forward = '' ) {
		
		 /*$url = $this->ucapi . '?' .  $this->_gen_model_action(self::_MODEL_USER, self::_ACTION_LOGOUT) 
		 		. '&' . $this->_gen_app_token($app, $token)	. '&uid=' . $uid . '&forward=' . urlencode($forward);
		// echo $url;
		 * 
		 */
		 $this->re_init_paramhelper();
         $url = $this->init_url();
         $this->paramhelper->append_param(ParamKey::key_uid, $uid);
         $this->paramhelper->append_param(ParamKey::key_forward, $forward);
         $this->paramhelper->append_param(ParamKey::key_action, self::_ACTION_LOGOUT);
         $this->paramhelper->append_param(ParamKey::key_model, self::_MODEL_USER);
         $code = $this->paramhelper->serialize_param();
         $code = urlencode($code);
         $url = $url . '&' . ParamKey::key_todo . '=' . $code;
         //echo $url;
		 $return = $this->socket->fopen2($url);
		 $data = $this->unserialize($return);
		 return $data;
	}
	
	/**
	 * 反序列化
	 * @param $str 序列化后的串
	 * @return 对象数组
	 */
	public function unserialize($str) {
		return $this->uccode->unserialize($str);
	}
	
	/**
	 * 返回用户信息
	 * @param $userid 用户ID
	 * @return 用户信息
	 */
	public function get_user_info($userid){
		/*$app_token = $this->_gen_app_token($app, $token) ;
		$action = $this->_gen_model_action(self::_MODEL_USER, self::_ACTION_GET_USER);
		$url = $this->ucapi . '?' . $app_token . '&' . $action . '&uid=' . urlencode($this->uccode->gen_64_code($userid));
		//echo $url;
		 * 
		 */
		$this->re_init_paramhelper();
		$url = $this->init_url();
		$this->paramhelper->append_param(ParamKey::key_uid, $userid);
		$this->paramhelper->append_param(ParamKey::key_action, self::_ACTION_GET_USER);
		$this->paramhelper->append_param(ParamKey::key_model, self::_MODEL_USER);
		$code = $this->paramhelper->serialize_param();
		$code = urlencode($code);
        $url = $url . '&' . ParamKey::key_todo . '=' . $code;
		$data = $this->socket->fopen2($url);
		return $this->unserialize($data);
	}
	
	/**
	 * 向UCENTER注册用户
	 * @param  $name 用户名
	 * @param  $pwd 密码
	 * @param  $email email
	 * @return 用户简单信息
	 */
	public function reg_user($name, $pwd, $email) {
		/*$app_token = $this->_gen_app_token($app, $token) ;
		$action = $this->_gen_model_action(self::_MODEL_USER, self::_ACTION_REG_USER);
		$pwd = empty($pwd) ? '' : $this->uccode->gen_64_code($pwd);
		
		$url = $this->ucapi . '?' . $app_token 
			. '&' . $action 
			. '&name=' . urlencode($this->uccode->gen_64_code($name))
			. '&password=' . urlencode($pwd)
			. '&email=' . urlencode($this->uccode->gen_64_code($email)) ;
			//echo $url;
			 * 
			 */
	    $this->re_init_paramhelper();
        $url = $this->init_url();
        $this->paramhelper->append_param(ParamKey::key_user_name, $name);
        $this->paramhelper->append_param(ParamKey::key_password, $pwd);
        $this->paramhelper->append_param(ParamKey::key_email, $email);
        $this->paramhelper->append_param(ParamKey::key_action, self::_ACTION_REG_USER);
        $this->paramhelper->append_param(ParamKey::key_model, self::_MODEL_USER);
        $code = $this->paramhelper->serialize_param();
        $code = urlencode($code);
        $url = $url . '&' . ParamKey::key_todo . '=' . $code;
        //echo $url;
		$data = $this->socket->fopen2($url);
		return $this->unserialize($data);	   
	}
	
	/**
	 * 删除用户
	 * @param $uid USER ID
	 * @return 操作信息
	 */
	public function del_user($uid) {
		/**$app_token = $this->_gen_app_token($app, $token) ;
		$action = $this->_gen_model_action(self::_MODEL_USER, self::_ACTION_DEL_USER);
		$uid = intval($uid);
		$url = $this->ucapi . '?' . $app_token 
			 . '&' . $action
			 . '&uid=' . urlencode($this->uccode->gen_64_code($uid));
		//echo $url;	 
		 * 
		 */
		 $this->re_init_paramhelper();
        $url = $this->init_url();
        $this->paramhelper->append_param(ParamKey::key_uid, $uid);
        $this->paramhelper->append_param(ParamKey::key_action, self::_ACTION_DEL_USER);
        $this->paramhelper->append_param(ParamKey::key_model, self::_MODEL_USER);
        $code = $this->paramhelper->serialize_param();
        $code = urlencode($code);
        $url = $url . '&' . ParamKey::key_todo . '=' . $code;
		$data = $this->socket->fopen2($url);
		return $this->unserialize($data);	 	 
	}
	
	/**
	 * 得到用户ID 
	 * @param  $data 调用删除接口返回的数据
	 * @return 被删除的用户ID
	 */
	public function gen_user_id($data) {
		return $data[ParamKey::key_uid];
	}

	/**
	 * 生成同步登录状态的方法
	 * @param $data 登录操作返回数据集合
	 * @return 应该输出到页面上的代码
	 */
	public function gen_login_sync($data = array()) {
		$code = '' ;
		foreach($data['allowed'] as $app) {
				$code .= '<img style="display:none;" src="' . $app . '" width=0 height=0 />' . "\n";
		}
        
		return $code;
	}
	
	/**
	 * 返回登录的用户信息
	 * @param $data 登录操作返回信息
	 * @return 登录成功的用户信息
	 */
	public function sync_login_user($data = array()) {
		return empty($data[ParamKey::FLAG_USER] ) ? array() : $data[ParamKey::FLAG_USER] ;
	}

	
	/**
	 * 生成同步登出的代码
	 * @param $data 登出操作返回的数据集合
	 * @return 应该输出到页面上的代码
	 */
    public function gen_logout_sync($data) {
		$code =  '' ;		
		foreach($data['apis'] as $api) {
	 			$code .= '<img style="display:none;" src="' . $api . '" width=0 height=0 />' . "\n";
	 	}
        foreach($data['tp_apis'] as $api) {
	 			$code .= '<img style="display:none;" src="' . $api . '" width=0 height=0 />' . "\n";
	 	}
		return $code;
	}
 
	/**
	 * 操作是否成功
	 * @param $data 远程API返回的数据
	 * @return 成功？ true : false
	 */
	public function is_ok($data = array()) {
		return isset($data[ParamKey::FLAG_FLAG]) && $data[ParamKey::FLAG_FLAG] == ParamKey::FLAG_OK ;
	}

	/**
	 * 操作信息
	 * @param $data 远程API返回的数据
	 * @return 操作信息
	 */
	public function get_msg($data = array()) {
		return isset($data[ParamKey::FLAG_MSG]) ? $data[ParamKey::FLAG_MSG] : '' ;
	}
	
	/**
	 * 返回FORWARD 内容
	 * @param  $data 远程调用返回的结果
	 */
	public function get_forward($data = array()) {
		return empty($data[ParamKey::key_forward]) ? '' :  $data[ParamKey::key_forward];
	}

	/**
	 * 返回同步更新的触发器
	 */
	public function gen_sync_triggers($data = array()){
		$trigger = empty($data[ParamKey::key_trigger]) ? array() : $data[ParamKey::key_trigger];
		$code = "";
		foreach($trigger as $t){
			$code .= '<img style="display:none;" src="' . $t . '" width=0 height=0 />' . "\n";
		}
		return $code;
	}

	/**
	 * 得到客户端IP
	 * @return IP地址
	 */
	public function  get_client_ip(){
	   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	           $ip = getenv("HTTP_CLIENT_IP");
	       else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	           $ip = getenv("HTTP_X_FORWARDED_FOR");
	       else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	           $ip = getenv("REMOTE_ADDR");
	       else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	           $ip = $_SERVER['REMOTE_ADDR'];
	       else
	           $ip = "unknown";
	   return($ip);
	}
}
