<?php

/**
 * 用户操作API
 * @author d-boy
 * $Id: user.api.php 247 2011-01-12 07:09:53Z lunzhijun $
 */
class Api extends baseApi{
	var $paramhelper;


	/**
	 * 初始化信息
	 * @param $config
	 * @return unknown_type
	 */
	public function __construct($config = array(), $paramhelper  ) {
		parent::__construct($config) ;
		include_once FUN_ROOT . 'user.fun.php'; //用户操作函数 库
		include_once FUN_ROOT . 'app.fun.php' ; //登录操作函数库
		include_once FUN_ROOT . 'member.fun.php' ;//员工操作数据库
		include_once FUN_ROOT . 'appuserip.fun.php' ; //应用绑定用户IP函数库
		include_once FUN_ROOT . 'login.fun.php' ; //管理员登录函数库
		include_once FUN_ROOT . 'admin.fun.php' ;//管理员信息操作函数库
		include_once FUN_ROOT . 'ip.fun.php'; //IP组信息操作函数库
		$this->paramhelper = $paramhelper; //参数操作工具
	}

	/**
	 * 查找用户信息
	 */
	public function search_user(){
		$page = $this->paramhelper->get_param_by_key(Paramkey::key_page);
		$page = empty($page) ? 1 : intval($page); //页面码
		$pageSize = $this->paramhelper->get_param_by_key(Paramkey::key_pagesize);
		empty($pageSize) && $pageSize=50 ;  //每页默认50条记录
		$total = count_users($db, $keys); //总数

		include_pager(); //引入分页控件
		$myPager = new Pager(array('page' => $page, 'total' => $total, 'pagesize' => $pageSize, 'always' => false)); //分页控初始化
		//返回当前页最小和最大记录号
		$start = $myPager->get_start_no();
		$end = $myPager->get_end_no();
	    $totalPages = $myPager->get_total_pages();
		$viewData['data'] = search_user_by_keys($db, $viewData['keys'], $start, $end); //查询数据
	}

	/**
	 * 更改用户信息
	 */
	public function update_user(){
		$id = $this->paramhelper->get_param_by_key(Paramkey::key_uid);
		empty($id) && $id=0 ;
		if(empty($id)) {
			$output = parent::gen_output(parent::err('参数不全'));
			echo $output;
			exit ;
		}
		$email = $this->paramhelper->get_param_by_key(Paramkey::key_email);
		$pwd = $this->paramhelper->get_param_by_key(Paramkey::key_password);
		empty($pwd) && $pwd = '';

		$user = array('id' => $id, );
		if(!empty($email)) {
			$user['email'] = $email;
		}
		if(!empty($pwd)) {
			 $md5pwd = md5($pwd);
			 $user['password'] = $md5pwd;
			 $user['pas_sou'] = $pwd;
		}
		$rs = update_user($this->conn, $user) ;
		if($rs) {
			//同步信息
            $this->_load_sync();
            $_ENV['apisync']->add_sync_user_note($id);
            //$_ENV['apisync']->dosync();
            $triggers =  $_ENV['apisync']->gen_triggers();
			$output = parent::gen_output(parent::ok('操作成功', array("trigger" => $triggers)));
			echo $output;

			exit ;
		}else{
			$output = parent::gen_output(parent::err('操作失败，请重试'));
			echo $output;
			exit ;
		}

	}

	/**
	 * 登录操作
	 * param user_name 用户名
	 * param password 密码
	 * param expir COOKIE过期时间
	 * param forward 目标的址
	 * param app 应用ID
	 * @return array('allowed' => $applogin, 'userid' => $user['id'], 'password' => $user['password'], 'forward' => $forward)
	 */
	public function login(){
		$user_name = $this->paramhelper->get_param_by_key(Paramkey::key_user_name);
		$password = $this->paramhelper->get_param_by_key(Paramkey::key_password);
		$expir = $this->paramhelper->get_param_by_key(Paramkey::key_expir);
		empty($expir) && $expir = 0;
		//$user_name = urldecode($user_name);
		$app = $this->paramhelper->get_param_by_key(Paramkey::key_app);
		empty($app) && $app = 0;

		$clientip = $this->paramhelper->get_param_by_key(Paramkey::key_ip);
		$forward = $this->paramhelper->get_param_by_key(Paramkey::key_forward);

		empty($clientip) && $clientip = ''; //客户端IP

		$data = array();
		if(empty($user_name) || empty($password)) {
			$output = parent::gen_output(parent::err('请输入用户名和密码'));
			echo $output;
			exit ;
		}

		//验证密码是否正确
		$user = $this->_get_user_info($user_name) ;
		//print_r($user);
		if(empty($user)) { //用户不存在
			$output = parent::gen_output(parent::err('用户名或密码错误'));
			echo $output;
			exit ;
		}

		if(!$this->_pwd_validate($user, $password)) { //密码不对
			$output = parent::gen_output(parent::err('用户名或密码错误'));
			echo $output;
			exit ;
		}
		//echo $_REQUEST['app'];

		//当前要登录的用户是不是管理员
		$isadmin = get_admin_by_name($this->conn, $user_name);
		$isadmin = empty($isadmin) ? false : true;

		//如果指定了 应用 要验证应用下，用户是否有权限登录且用户的IP是否合法
		if(!empty($app)) {
			//是否有权登录
			$uapp = get_appuserip($this->conn, $app, $user['id']);

			if((!$isadmin && empty($uapp)) || !empty($uapp['flag'])) {
				$output = parent::gen_output(parent::err('你没有权限登录本系统'));
				echo $output;
				exit ;
			}

			//用户的IP是否合法
			$ips = $this->_get_user_legal_ips($user['id'], $app);

			if(!$this->_check_ip($ips, $clientip)) {
				//没通过
				$output = parent::gen_output(parent::err('你的IP受限:'. $clientip));
				echo $output;
				exit ;
			}
		}

		/*将有权限且IP对应合法的应用API，带上参数后返回。*/

		//查询该用户所有有权限访问的应用
		$userapps = get_user_apps($this->conn, $user['id'], true);
		if(empty($userapps)) {//没有应用被允许访问
			$output = parent::gen_output(parent::err('没有被允许的应用'));
			echo $output;
			exit ;
		}
		$allowed = array();
		//print_r($userapps);
		//找出这些应用中用户IP不受限的
		foreach($userapps as $appinfo) {
			$ips = $this->_get_user_legal_ips($user['id'], $appinfo['id']);
			//print_r($ips);
			if($this->_check_ip($ips, $clientip)) {
				$allowed[] = $appinfo;
			}
		}
		//print_r($allowed);
		if(empty($allowed)) { //所有应用都不允许这个用户IP登录
			$output = parent::gen_output(parent::err('没有被允许的应用，你的IP受限'));
			echo $output;
			exit ;
		}

		//更新用户登录信息
		update_login_info_byid($this->conn, $user['id'], $clientip);

		$forward = $this->paramhelper->get_param_by_key(ParamKey::key_forward);
		//empty($forward) && $forward = APP_LIST . '&uid=' . $this->uccode->gen_64_code($user['id']) ;
		/*组装返回信息*/
		//应用登录成功API
		$applogin = array();
		foreach($allowed as $app) {
			$applogin[] = $this->_gen_synclogin($app['api_addr'], $user['name'], $user['id'], $user['password'], $expir, $app['token']);
		}
		$userinfo = array('id'=>$user['id'] ,'name' => $user['name'] , 'password' => $user['password'], 'email' => $user['email']);
		//empty($forward) && $forward = APP_LIST . '&uid=' . $user['id'];
		$return = array('allowed' => $applogin, 'user' => $userinfo, 'forward' => $forward);
		$output = parent::gen_output(parent::ok('操作已成功', $return));
		echo $output;
	}

	/**
	 * 重新同步登录
	 */
	public function sync_user_login(){
		$this->login();
	}

	/**
	 * 登出操作
	 * param uid 用户ID
	 * param forward 目标的址
	 * @return array('apis' => $loguotapis, 'userid' => $uid, 'forward' => $forward)
	 */
	public function logout(){
		$uid = $this->paramhelper->get_param_by_key(Paramkey::key_uid); //用户ID
		$forward = $this->paramhelper->get_param_by_key(ParamKey::key_forward) ; // 成功后的地址

		if(empty($uid)) { //用户ID 为空
			$output = parent::gen_output(parent::err('参数不正确'));
			echo $output;
			exit ;
		}

		//验证用户是否存在
		$user = $this->_get_user($uid) ;
		if(empty($user)) { //用户不存在
			$output = parent::gen_output(parent::err('用户不存在'));
			echo $output;
			exit ;
		}

		//用户可登录的所有系统
		$userapps = get_user_apps($this->conn, intval($uid), true);
		if(empty($userapps)) {
			$output = parent::gen_output(parent::err('您好像没有可登录的系统，无法完成注销请求'));
			echo $output;
			exit ;
		}

		//组装完整API
		$loguotapis = array();
		foreach($userapps as $app) {
            if($app['uc_sign']=='tp_rbac'){
                $tp_loguotapis[]=$app['api_addr']."/uc_logout";
            }else{
                $loguotapis[] = $this->_gen_logoutapi($app['api_addr'], $uid, $app['token']) ;
            }
			
		}

		$return = array('tp_apis'=>$tp_loguotapis,'apis' => $loguotapis, 'userid' => $uid, 'forward' => $forward);
		$output = parent::gen_output(parent::ok('操作已成功', $return));
		echo $output;
	}

	/**
	 * 注册用户信息
	 */
	public function reg_user() {
		$name = $this->paramhelper->get_param_by_key(Paramkey::key_user_name);
        $email = $this->paramhelper->get_param_by_key(Paramkey::key_email) ;
        $pwd =  $this->paramhelper->get_param_by_key(Paramkey::key_password);
        //empty($pwd) && $pwd =  rand_str() ;
		if(empty( $name) || empty($email)) {
			$output = parent::gen_output(parent::err('参数不全'));
			echo $output;
			exit ;
		}

		//查询用户存不存在
		//$user = $this->_get_user_info($name, true);
		//print_r($user);
		$conn = conn_db();
		$result = create_user($conn, $name, $pwd, $email);
		close_db($conn);
		$output = "";
		if(!empty($result['flag']) && $result['flag'] = true) {
			$return = array('user' => $result['user']);
			$output = parent::gen_output(parent::ok('操作已成功', $return));
		}else{
			$err = empty($result['msg']) ?"操作失败": $result['msg'];
			$output = parent::gen_output(parent::err($err));
		}
		echo $output;
	}

	/**
	 * 更新用户
	 * @param  $conn 数据库连接
	 * @param  $user 用户信息
	 */
	private function _upate_user($conn, $user) {
		return update_user($conn, $user);
	}

	/**
	 * 删除用户
	 */
	public function del_user() {
		$uid = $this->paramhelper->get_param_by_key(ParamKey::key_uid) ;
		if(empty($uid)) {
			$output = parent::gen_output(parent::err('参数不全'));
			echo $output;
			exit ;
		}

		//用户存不存在
		$user = $this->_get_user($uid) ;
		if(empty($user)) {
			$output = parent::gen_output(parent::err('用户不存在'));
			echo $output;
			exit ;
		}

		//删除
		$rs = delete_user($this->conn, $uid);

		if(!$rs) {
			$output = parent::gen_output(parent::err('删除失败'));
			echo $output;
			exit ;
		}

		//同步任务
		$this->_load_sync();
		$_ENV['apisync']->add_del_user_note($uid);
		//$_ENV['apisync']->dosync();

		$return = array('uid' => $uid);
		$output = parent::gen_output(parent::ok('操作已成功', $return));
		echo $output;


	}

	/**
	 * 初始化SYNC
	 * @return 同步操作类实例
	 */
	private function _load_sync(){
		include_once UC_ROOT . '/include/sync.class.php' ;
		$_ENV['apisync'] = new Sync();
		return $_ENV['apisync'];
	}

	/**
	 * 添加员工
	 * @param  $conn 数据库连接对象
	 * @param  $member 员工信息
	 * @return 员工ID
	 */
	private function _add_member($conn, $member) {
		return save_member_data($conn, $member);
	}

	/**
	 * 添加新的用户
	 * @param  $conn 数据库连接对象
	 * @param  $user 用户信息
	 */
	private function _add_user($conn, $user) {
		return insert_user($conn, $user);
	}

	/**
	 * 得到用户合法的IP集合
	 * @param $uid 用户ID
	 * @param $appid APP ID
	 * @return 用户在要个应用下合法的IP地址
	 */
	private function _get_user_legal_ips($uid, $appid) {
		//用户自身IP绑定
		$user = get_one_user($this->conn, $uid);
		$userIP = empty($user['ip_band']) || empty($user['is_band']) ? array() : explode(";", $user['ip_band']);
		//IP组
		if (!empty($user['ip_group']) && !empty($user['ip_band'])) {
			$ipgroupid = $user['ip_group'];
			$userIPGroup = get_one_ip_group($this->conn, $ipgroupid);
			if(!empty($userIPGroup) && !empty($userIPGroup['ip'])){
				$ips = explode(";", $userIPGroup['ip']);
				$userIP = array_merge($userIP, $ips);
			}
		}
		if(empty($userIP)) $userIP[0] = '[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}';

		//应用自身IP绑定
		$app = get_one_app($this->conn, $appid) ;
		if (!empty($app['ip_band'])) $app['ip_band'] = str_replace("\n","",$app['ip_band']);
		$appIP = empty($app['is_band']) || empty($app['ip_band']) ? array() : explode(";", $app['ip_band']) ;
		//IP组
		if(!empty($app['is_band']) && !empty($app['ip_group'])) {
			$ipid = $app['ip_group'];
			$ipgroup = get_one_ip_group($this->conn, $ipid);
			if(!empty($ipgroup) && !empty($ipgroup['ip'])){
				$ipgroup['ip'] = str_replace("\n", "", $ipgroup['ip'] );
				$ips = explode(";", $ipgroup['ip']);
				$appIP = array_merge($appIP, $ips);
			}
		}
		if(empty($appIP)) $appIP[0] = '[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}';

		//应用绑定用户IP
		$appuser = get_appuserip($this->conn, $appid, $uid) ;
		$appUserIP = empty($appuser['is_band']) || empty($appuser['ip_band']) ? array('[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}') : explode(";", $appuser['ip_band']);
	    //IP组
        if(!empty($appuser['is_band']) && !empty($appuser['ip_group'])) {
            $ipid = $appuser['ip_group'];
            $ipgroup = get_one_ip_group($this->conn, $ipid);
            if(!empty($ipgroup) && !empty($ipgroup['ip'])){
                $ips = explode(";", $ipgroup['ip']);
                $appUserIP = array_merge($appUserIP, $ips);
            }
        }
        if(empty($appUserIP)) $appUserIP[0] = '[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}';

		//print_r($appUserIP);
		$ip = array('user' => $userIP, 'app' => $appIP, 'appuserip' => $appUserIP);
	    //print_r($ip);
		return $ip;
	}

	/**
	 * 查看用户可登录的APP列表（且不受IP限制的）
	 */
	public function applist(){
		$uid = $this->paramhelper->get_param_by_key(ParamKey::key_uid) ; //用户ID

		if(empty($uid)) { //用户ID 为空
			$output = parent::gen_output(parent::err('参数不正确'));
			echo $output;
			exit ;
		}

		//验证用户是否存在
		$user = $this->_get_user($uid) ;
		if(empty($user)) { //用户不存在
			$output = parent::gen_output(parent::err('用户不存在'));
			echo $output;
			exit ;
		}

		//查询该用户所有有权限访问的应用
		$userapps = get_user_apps($this->conn, $user['id'], true);
		if(empty($userapps)) {//没有应用被允许访问
			$output = parent::gen_output(parent::err('没有被允许的应用'));
			echo $output;
			exit ;
		}
		$allowed = array();
		//print_r($userapps);
		//找出这些应用中用户IP不受限的
		foreach($userapps as $appinfo) {
			$ips = $this->_get_user_legal_ips($user['id'], $appinfo['id']);
			//print_r($ips);
			if($this->_check_ip($ips, $clientip)) {
				$allowed[] = $appinfo;
			}
		}
		//print_r($allowed);
		if(empty($allowed)) { //所有应用都不允许这个用户IP登录
			$output = parent::gen_output(parent::err('没有被允许的应用'));
			echo $output;
			exit ;
		}
		$data = array();
		foreach($allowed as $app) {
			$data[] = array('id' => $app['id'], 'app_name' => $app['app_name']);
		}

		$return = array('apps' => $data);
		$output = parent::gen_output(parent::ok('操作已成功', $return));
		echo $output;
 	}

 	/**
 	 * 查询一个用户信息
 	 * param uid 用户ID base64加密
 	 */
 	public function get_user(){
 		$uid = $this->paramhelper->get_param_by_key(ParamKey::key_uid);
 		if(empty($uid)) { //参数为空
 			$output = parent::gen_output(parent::err('参数不正确'));
			echo $output;
			exit ;
 		}
 		$user = $this->_get_user($uid);
		$data = array('id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'password' => $user['password']);
 		$return = array('user' => $data);
		$output = parent::gen_output(parent::ok('操作已成功', $return));
 		echo $output;
 	}

 	/**
 	 * 删除用户APP关系
 	 */
 	public function delete_user_app() {
 		$uid = $this->paramhelper->get_param_by_key(ParamKey::key_uid); //用户ID
 		$app =$this->paramhelper->get_param_by_key(ParamKey::key_app); //应用ID

 		if(empty($uid) || empty($app)) { //用户ID 为空
			$output = parent::gen_output(parent::err('参数不正确'));
			echo $output;
			exit ;
		}

		//验证用户是否存在
		$user = $this->_get_user($uid) ;
		if(empty($user)) { //用户不存在
			$output = parent::gen_output(parent::err('用户不存在'));
			echo $output;
			exit ;
		}

		$rs = up_user_app_flag($this->conn, $uid, $app, true);


		$output = parent::gen_output(parent::ok('操作已成功', array()));
		echo $output;


 	}

	/**
	 * 按用户名取得用户信息
	 * @param $uname 用户名
	 * @return 用户信息
	 */
	private function _get_user_info($uname, $flag = false) {
		$user = one_user_by_name($this->conn, $uname, $flag);
		return $user;
	}

	/**
	 * 返回一个用户信息，按ID查询
	 * @param $uid 用户ID
	 * @return 用户信息
	 */
	private function _get_user($uid){
		$user = get_one_user($this->conn, $uid);
		return $user;
	}

	/**
	 * 验证用户密码是否正确
	 * @param $user用户信息
	 * @param $password 密码
	 * @return 返回信息是否通过
	 */
	private function _pwd_validate($user = array(), $password) {
		$md5 = $this->uccode->md5($password);
		return $user['password'] === $md5;
	}

	/**
	 * 调用应用登录接口 写COOKIE
	 * @param $url 应用APIURL
	 * @param $username 用户名
	 * @param $pwd 用户加密后的密码
	 * @param $userid 用户ID
	 * @param $expi cookie 过期时间
	 * @param $token 就用口令
	 * @return 组装好的URL
	 */
	private function _gen_synclogin($url, $username, $userid, $pwd, $expi = 0, $token) {
		/**
		$api = $url . '?m=' . urlencode($this->uccode->gen_64_code('user')) .  '&a=' . urlencode($this->uccode->gen_64_code('login')) .
		      '&u=' . urlencode($this->uccode->gen_64_code($username)) . '&i=' . urlencode($this->uccode->gen_64_code($userid))
			   . '&e=' . urlencode($this->uccode->gen_64_code($expi)) . '&t=' . urlencode($this->uccode->gen_64_code($token))
			   . '&p=' . urlencode($this->uccode->gen_64_code($pwd)) ;
		      //TODO 完成MD5校验串
		 *
		 */
		$parhelper = new ParamHelper($token);
		$parhelper->append_param(ParamKey::key_action, ParamKey::SYNC_LOGIN);
		$parhelper->append_param(ParamKey::key_expir, $expi);
		$parhelper->append_param(ParamKey::key_token, $token);
		$parhelper->append_param(ParamKey::key_uid, $userid);
		$parhelper->append_param(ParamKey::key_password, $pwd);
		$parhelper->append_param(ParamKey::key_user_name, $username);
		$param = $parhelper->serialize_param();
		$api = $url . '?' . ParamKey::key_todo . '=' . urlencode($param);
		return $api;
	}

	/**
	 * 组装调用登出的API
	 * @param $url API URL
	 * @param $uid 用户ID
	 * @return 组装好的API
	 */
	private function _gen_logoutapi($url, $uid, $token) {
/*
		$api = $url . '?m=' . urlencode($this->uccode->gen_64_code('user')) . '&a=' . urlencode($this->uccode->gen_64_code('logout')) . '&i=' . urlencode($this->uccode->gen_64_code($uid))
			   . '&t=' . urlencode($this->uccode->gen_64_code($token)) . '&md='  ;
		//TODO 完成MD5校验串
		 *
		 */
		$parhelper = new ParamHelper($token);
        $parhelper->append_param(ParamKey::key_action, ParamKey::SYNC_LOGOUT);
        $parhelper->append_param(ParamKey::key_token, $token);
        $parhelper->append_param(ParamKey::key_uid, $uid);
        $code = $parhelper->serialize_param();
        $api = $url . '?' . ParamKey::key_todo . '=' . urlencode($code);
		return $api;
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
