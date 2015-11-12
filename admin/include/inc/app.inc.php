<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 用户信息操作控制器
  * @author d-boy
  * $Id: app.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	
 	function __construct() {
 		parent::__construct();
 		include_once FUN_ROOT . 'app.fun.php';
 		include_once FUN_ROOT . 'appuserip.fun.php';
 		include_once FUN_ROOT . 'group.fun.php';
 		include_once FUN_ROOT . 'user.fun.php';
 		include_once FUN_ROOT . 'ip.fun.php';
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){ 	 
 	 	$viewData = array();
 	 	$conn = $_ENV['db'];
 	 	$keys = $viewData['keys'] = empty($_REQUEST['keys']) ? array() : $_REQUEST['keys']; //查询条件变量		
		$viewData['msg'] = empty($_REQUEST['msg']) ? array() : $_REQUEST['msg'] ; //提示信息
		$viewData['keys'] = $keys;		
		$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']); //页面码
		$pageSize = 50; //每页50条记录
		$total = count_apps($conn, $keys); //总数
		
		include_pager(); //引入分页控件
		$myPager = new Pager(array('page' => $page, 'total' => $total, 'pagesize' => $pageSize, 'always' => false)); //分页控初始化
		//返回当前页最小和最大记录号
		$start = $myPager->get_start_no();
		//$end = $myPager->get_end_no();
	
		$viewData['data'] = search_apps($conn, $viewData['keys'], $start, $pageSize); //查询数据
		$viewData['pager'] = $myPager->exec_pager(); //分页条代码
 	 	//close_db($conn);
 		parent::view('app_index', $viewData);
 	}
 	
 	/**
 	 * 编辑或添加应用
 	 */
 	function _edit() {
 		$viewData = array();
 		$id = empty($_REQUEST['id']) ? 0 : $_REQUEST['id'] ;
 		$conn = $_ENV['db'];
 		//$viewData['groups'] = search_group($conn, array(), 0, 0);
 		$viewData['appgroup'] = array();	
 		$viewData['data'] = array();
 		
 		//UCSIGN 
 		$viewData['ucsign'] = $this->init_ucsign($conn);
 		if(empty($id)) {
 			//添加应用
 			$viewData['msg'] = array('flag' => true, 'msg' => '添加新应用') ; 					
 		}else { 			
 			$data = get_one_app($conn, $id);
 			
 			if(empty($data)) {
 				$viewData['msg'] = array('flag' => false, 'msg' => '没有找到id=' . $id . '的应用，想添加新应用请添加下面的表单后提交' ) ;
 				
 			}else {
 				$viewData['data'] = $data;
 				//$viewData['appgroup'] = get_app_group($conn, $id);
 			} 			
 		}
 		$viewData['ipgroup'] = all_ipgroup($conn);
 		//close_db($conn);
		//print_r($viewData);die;
 		parent::view('app_edit', $viewData);
 	}
 	
 	/**
 	 * 保存或更新应用信息
 	 */
 	function _save() {
 		$info = $_REQUEST['info'] ;
 		
 		$viewData = array();
 		
 		if(empty($info)) {
 			$viewData['msg'] = array('flag' => false, 'msg' => '数据有误，请重试');
 		}else{
 			$chk = check_app($info);
 			if(!empty($chk)) {
 				$viewData['msg'] = array('flag' => false, 'msg' => $chk);
 			}
 			
 			//print_r($info['groups']);
 			
 			//验证通过
 			$conn = $_ENV['db'];
 			if(empty($info['id'])) {
 				//添加信息 				
 				$viewData['msg'] = save_app($conn, $info); 
 				//$viewData['appgroup'] = $viewData['msg']['group'];				
 			}else {
 				//更新
 				$id = $info['id'];
 				$viewData['msg'] = do_update_app($conn, $info);
 				//$viewData['appgroup'] = get_app_group($conn, $info['id']);
 			}
 			
 			$viewData['data'] = get_one_app($conn, $info['id']);
 			$viewData['groups'] = search_group($conn, array(), 0, 0);
 			
 			
 			//close_db($conn);
 		}
        
 		//UCSIGN 
        $viewData['ucsign'] = $this->init_ucsign($conn);
        $viewData['ipgroup'] = all_ipgroup($conn);
 		parent::view('app_edit', $viewData);
 	}
 	
 	/**
 	 * 批量删除应用
 	 */
 	function _batch_delete() {
 		$ids = $_REQUEST['ids'];
 		$conn = $_ENV['db'];
 		$result = batch_delete_app($conn, $ids);
 		//close_db($conn);
 		$msg = "msg[flag]={$result['flag']}&msg[msg]=" . urlencode($result['msg']);
 		header("location:enter.php?m=app&a=index&$msg");
 	}

 	/**
 	 * 用户IP绑定
 	 */
 	function _appuser() {
 		$viewData = array();
 		$this->include_appuserid_fun();
 	 	$conn = $_ENV['db'];
 	 	$keys = $viewData['keys'] = empty($_REQUEST['keys']) ? array() : $_REQUEST['keys']; //查询条件变量		
		$viewData['msg'] = empty($_REQUEST['msg']) ? array() : $_REQUEST['msg'] ; //提示信息
		$viewData['keys'] = $keys;		
		$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']); //页面码
		$pageSize = 50; //每页50条记录
		$total = count_appuserip($conn, $keys); //总数
		
		include_pager(); //引入分页控件
		$myPager = new Pager(array('page' => $page, 'total' => $total, 'pagesize' => $pageSize, 'always' => false)); //分页控初始化
		//返回当前页最小和最大记录号
		$start = $myPager->get_start_no();
		//$end = $myPager->get_end_no();
	
		$viewData['data'] = search_appuserip($conn, $viewData['keys'], $start, $pageSize); //查询数据
		$viewData['pager'] = $myPager->exec_pager(); //分页条代码
 	 	//close_db($conn);
 		
 		parent::view('appuserip_index', $viewData);
 	}
 	
 	/**
 	 * 添加或修改用户IP绑定
 	 */
 	function _edit_appuserip(){
 		$viewData = array();
 		$conn = $_ENV['db'];
 		
 		$viewData['app_id'] = $appid = empty($_REQUEST['app_id']) ? 0 : $_REQUEST['app_id'];
 		$viewData['user_id'] = $userid = empty($_REQUEST['user_id']) ? 0 : $_REQUEST['user_id'] ;
 		
 		if(empty($appid)) {
 			 $err = $this->appuserip_err('没有指定应用，请重新操作') ;
 			 $viewData = array_merge($viewData, $err); 			
 		}else{
 			$this->include_appuserid_fun();
 			$data = get_appuserip($conn, $appid, $userid);
 			if(empty($data)) {
 				$err = $this->get_app_and_user($conn, $userid, $appid); 		
 			//	print_r($err);		
 				$viewData = array_merge($viewData , $err);
 			}else{
 				$viewData['data'] = $data;
 			} 
 			$viewData['ipgroup'] = all_ipgroup($conn);
 			$viewData['users'] = search_user_by_keys($conn, array(), 0, 0);
 		}
 		
 		//close_db($conn);
 		parent::view('appuserip_edit', $viewData);
 	}
 	
 	/**
 	 * 绑定用户
 	 */
 	function _band_users(){
 		$viewData = array();
 		$id = empty($_REQUEST['id']) ? 0 : $_REQUEST['id'] ;
        $conn = $_ENV['db'];
        if(empty($id)) {        	
        	parent::message("参数错误","参数错误，请重新操作", "", 5000);
        	return;
        }
        $data = get_one_app($conn, $id);
        if(empty($data)) {
        	parent::message("","应用不存在，可能已被删除", "", 5000);
            return;
        }
        //$viewData['groups'] = search_group($conn, array(), 0, 0);         
        $viewData['data'] = $data;
        $viewData['users'] = all_user_group_by_group($conn);
       
        //print_r($viewData['users']);
        $appuserobjs = get_app_user($conn, $id);
      
        $appusers = array();
        foreach($appuserobjs as $u){
        	$appusers[] = $u['id'];
        }
   
        $viewData['appusers'] = $appusers; //可登录APP的用户
        //print_r($viewData['appusers']);
        parent::view("app_map_user", $viewData);
 	}
 	
 	/**
 	 * 保存存绑定的信息
 	 */
 	function _save_band(){
 		$appid = empty($_REQUEST["appid"]) ? 0 : intval($_REQUEST["appid"]);
 		if(empty($appid)) {
 			parent::message("","参数有误，请重新操作");
 			return;
 		}
 		
 		$conn = $_ENV['db'];
 		//更新用户与应用之间的关系
 		$ids = empty($_REQUEST['ids']) ? array() : $_REQUEST['ids'];
 		//print_r($users);
 		$users = array();
 		foreach($ids as $id) {
 			$users[] = array('id' => $id);
 		}
 		update_app_user_relations($conn, $appid, $users);
 		parent::message("", "操作已成功", "enter.php?m=app&a=index");
 	}
 	
 	/**
 	 * 保存或更新应用绑定用户IP
 	 */
 	function _save_appuserip() {
 		$viewData = array();
 		$info = empty($_REQUEST['info']) ? array() : $_REQUEST['info'] ;
 		$conn = $_ENV['db']; 	
 	    $this->include_appuserid_fun();
 		//print_r($info);	
 	    if(empty($info) || empty($info['user_id']) || empty($info['app_id'])) {
 			$viewData['msg'] = array('flag' => false , 'msg' => '数据不完整，请重新操作');
 		}
	 	else{	
	 		add_or_update_appuserip($conn, $info) ;
	 	}	 		
	 	$viewData['user_id'] = $info['user_id'];
	 	$viewData['app_id'] = $info['app_id'];
 		$viewData['data'] = get_appuserip($conn, $info['app_id'], $info['user_id']);
 		$viewData['users'] = search_user_by_keys($conn, array(), 0, 0);
 		//close_db($conn);
 	    $viewData['ipgroup'] = all_ipgroup($conn);   
 		parent::view('appuserip_edit', $viewData);
 	}
 	
 	/**
 	 * 向VIEWDATA里加入APP信息和USER信息 （在用户绑定不存在的情况下）
 	 * @param $conn 数据连接
 	 * @param $uid 用户ID
 	 * @param $appid APPID
 	 */
 	function get_app_and_user($conn, $uid, $appid) {
 		$viewData = array();
 		$data = get_appuserip($conn, $appid, $uid);
 		if(empty($data)) {
	 		$app = get_one_app($conn, $appid) ;
	 		if(empty($app)) {
	 			return $this->appuserip_err('没有找到应用信息，请重新操作'); 			
	 		}
	 		
	 		$user = $this->read_user_info($conn, $uid);
	 		if(empty($user)) { 			
	 			$viewData = $this->appuserip_err('没有找到用户，请重新操作'); 
	 			return $viewData; 			
	 		}		
	 		
	 		
	 		$viewData['data'] = array('app_id' => $app['id'], 'app_name' => $app['app_name'], 'user_id' => $user['id'], 'user_name' => $user['name']);
 		}else {
 			$viewData['data'] = $data;
 		}
 		return $viewData;
 	}
 	
 	
 	/**
 	 * 应用用户IP绑定数据异常
 	 * @param $msg 信息
 	 * @return 重新初始化的数据
 	 */
 	function appuserip_err($msg){
 		$viewData = array();
 		$viewData['msg'] = array('flag' => false, 'msg' => $msg);
 		$viewData['users'] = array();
 		$viewData['data'] = array();
 		return $viewData;
 	}
 	
 	function read_user_info($conn, $uid) {
 		$this->include_user_fun();
 		$user = get_one_user($conn, $uid) ;
 		return $user;
 	}
 	
 	function include_user_fun() {
 		include_once FUN_ROOT . 'user.fun.php';	
 	}
 	
 	function include_appuserid_fun() {
 		include_once FUN_ROOT . 'appuserip.fun.php';
 	}
 	
 	/**
 	 * 初始化ucsign
 	 */
 	function init_ucsign($conn){
 		/*include_once FUN_ROOT . 'settings.fun.php';
 		$sign = get_one_setting($conn, 'ucsign');
 		if(empty($sign)){
 			return array();
 		}
 		$signs = explode(';', $sign['v']);*/
 		global $UCENTERS;
 		$signs = array_keys($UCENTERS);
 		return $signs;
 	}
 	
 	/**
 	 * 向应用推送用户
 	 */
 	function _sync_users_view(){
 	    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
        if(empty($id)){
            parent::message("","参数错误，请重试");
            return;
        }
        $conn = $_ENV["db"];
        
 	    $data = get_one_app($conn, $id);
        if(empty($data)) {
            parent::message("","应用不存在，可能已被删除", "", 5000);
            return;
        }
        
 		//返回所有用户
 		$users = all_user_group_by_group($conn);
 		
 		$viewData = array();
 		$viewData["users"] = $users;
 		$viewData["app"] = $data;
 		parent::view("push_user", $viewData);
 		
 	}
 	
 	/**
 	 * 向应用推送用户
 	 */
 	function _sync_users(){
 		$id = empty($_REQUEST['app']) ? 0 : intval($_REQUEST['app']);
 		if(empty($id)){
 			parent::message("","参数错误，请重试");
 			return;
 		}
 		$users = empty($_REQUEST['ids']) ? array() : $_REQUEST['ids'];
 		if(empty($users)){
 		    parent::message("","请选择要推送的用户");
            return;
 		}
 		$conn = $_ENV['db'];
 		$app = get_one_app($conn, $id);
 		if(empty($app)) {
 			parent::message("","参数错误，请重试");
            return;
 		}
 		//返回所需用户
 		$userdata = array();
 		foreach($users as $uid){
 			$u = get_one_user($conn, $uid);
 			if(!empty($u)) $userdata[] = $u;
            $email[]=$u['email'];
 		}
        if($app['uc_sign']=='tp_rbac'){
            //特殊处理tp_rbac的框架
            $email_list = implode(",",$email);
            $post_data = array("email" => $email_list);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $app['api_addr'].'/sync_user');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            $output = curl_exec($ch);
            curl_close($ch);
            parent::message("","同步命令已发出，正在向{$app['app_name']}同步用户信息。请稍等....", "", 10000);
        }else{
            sync_all_users($app, $userdata);
            parent::message("","同步命令已发出，正在向{$app['app_name']}同步用户信息。请稍等....", "", 10000);
        }
 	}
 }
 

 
?>