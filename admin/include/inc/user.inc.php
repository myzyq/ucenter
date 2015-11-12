<?php
defined('U_CENTER') or exit('Access Denied');

/**
 * 用户信息操作控制器
 * @author d-boy
 * $Id: user.inc.php 248 2011-01-12 07:50:43Z lunzhijun $
 */
class Inc extends BaseInc {

	function __construct() {
		parent::__construct();
		include_once FUN_ROOT . 'user.fun.php';
		include_once FUN_ROOT . 'usergroup.fun.php';
		include_once FUN_ROOT . 'group.fun.php';
		include_once FUN_ROOT . 'ip.fun.php';
	}

	/**
	 * 首页
	 * @see include/inc/BaseInc#_index()
	 */
	function _index(){
		$viewData = user_index_data();
		parent::view('user_index', $viewData);
	}

	/**
	 * 保存用户信息
	 */
	function _save() {
		$info = $_POST['info']; //添加的用户信息
		$msg = $this->check_data($info);
		if(empty($msg)) {
			$conn = $_ENV['db'] ;
			$rs = create_user($conn, $info['name'], $info['password'], $info['email'], $info['group_id']);
			$msg = 'msg[flag]=' . $rs['flag'] . '&msg[msg]=' . urlencode($rs['msg']);
			//close_db($db);
		}else {
			$msg = 'msg[flag]=0&msg[msg]=' . urlencode($msg);
		}
		header("location:enter.php?m=user&a=index&$msg");
	}

	/**
	 * 更改用户信息
	 */
	function _edit(){
		$id = $_REQUEST['id'];
		$viewData = array();
		$conn = $_ENV['db'];
		$viewData['data'] = get_one_user($conn,$id);

		if(empty($viewData['data'])) {
			$viewData['msg'] = array('flag' => FALSE, 'msg' => '没要找到用户信息，可能这个用户已经被删除');
		}else {
			$this->get_userful_groups($viewData ,$conn, $id) ; //用户和用户组关系
			$this->get_useful_apps($viewData, $conn, $id);//用户与APP关联信息
			$viewData['ipgroup'] = all_ipgroup($conn);
		}
		//close_db($conn);
		parent::view('user_edit', $viewData);
	}

	/**
	 * 创建用户
	 */
	function _add(){
		$viewData = array();
		$conn = $_ENV["db"];
		$id = 0;
		$this->get_userful_groups($viewData ,$conn) ; //用户和用户组关系
		$this->get_useful_apps($viewData, $conn);//用户与APP关联信息
		$viewData['ipgroup'] = all_ipgroup($conn);
		parent::view('user_add', $viewData);

	}

	/**
	 * 保存新用户，保存用户与应用关系，推送用户
	 */
	function _save_user(){
		if(empty($_POST['info'])) {
			parent::message("数据不完整", "您提交的数据不完整，请重新操作");
			return;
		}

		$info =  $_POST['info'] ;
		$msg = $this->check_data($info);
		if(!empty($msg)){
			parent::message("", $msg);
			return;
		}

		$groups = array();

		if(isset($info['group'])) {
			$groups = $info['group'];
			unset($info['group']);
		}
		$apps = array();
		if(isset($info['apps'])) {
			$apps = $info['apps'];
			unset($info['apps']);
		}

		$conn = $_ENV['db'];
		$password = empty($info['password']) ? '' : $info['password'];
		$rs = create_user($conn, $info['name'], $password, $info['email'], $info["group_id"]);
		if(empty($rs) || $rs['flag'] == false || empty($rs['user'])){
			parent::message("操作失败","用户注册失败" . (empty($rs) ? "" : $rs["msg"]));
			return;
		}

		$id = $rs['user']['id'];
		//添加用户与应用的关联
		up_user_app_flag($conn,$id, 0);
		update_user_app_relations($conn, $id, $apps); //更新应用与用户间的关系

		//by d-boy 国付提出注册用户时不同步数据
		//生成同步触发器
		//$_ENV['sync']->add_sync_user_note($id, $apps);
		//$tiggers = $_ENV['sync']->gen_triggers_img(); 

		//parent::message('', $rs['msg'] . $tiggers, 'enter.php?m=user&a=index', 10000);
		parent::message('', $rs['msg'], 'enter.php?m=user&a=index', 10000);
	}

	/**
	 * 更改一个用户的密码
	 */
	function _edit_pass(){
		$id = $_REQUEST['id'];
		$viewData = array();
		$conn = $_ENV['db'];
		$viewData['data'] = get_one_user($conn,$id);
		if(empty($viewData['data'])) {
			$viewData['msg'] = array('flag' => FALSE, 'msg' => '没要找到用户信息，可能这个用户已经被删除');
		}else{
			$viewData['msg'] = empty($_REQUEST['msg']) ? '' : $_REQUEST['msg'];
		}

		parent::view('pwd_edit', $viewData);
	}

	/**
	 * 返回用户与组的关联信息
	 * @param $conn 数据库连接
	 * @return 向$viewData中加入group 和usergroup
	 */
	function get_userful_groups(&$viewData, $conn) {
		$viewData['groups'] = search_group($conn, array(), 0, 0);
		//$viewData['usergroup'] = get_user_group($conn, $id);
	}

	/**
	 * 查询用户与APPS的关联
	 * @param $conn 数据连接对象
	 * @param $id 用户ID
	 * @return 将apps 和 myapps加入$viewData中
	 */
	function get_useful_apps(&$viewData, $conn, $id = 0){
		$viewData['apps'] = search_apps($conn, array(), 0, 0 ); //所有的APP
		if(!empty($id)) {
			$uapps = get_user_apps($conn, $id, true); //所有可访问APP
			$myapps = array();
			if(!empty($uapps)) {
				foreach($uapps as $a) {
					$myapps[$a['id']] = $a;
				}
			}
			$viewData['myapps'] = $myapps;
		}
	}

	/**
	 * 更新用户信息
	 */
	function _update() {


		$info = empty($_POST['info']) ? array() : $_POST['info'] ;

		//$viewData = array();
		$id = $info['id'];
		$groups = array();

		if(isset($info['group'])) {
			$groups = $info['group'];
			unset($info['group']);
		}
		$apps = array();
		if(isset($info['apps'])) {
			$apps = $info['apps'];
			unset($info['apps']);
		}
		$conn = $_ENV['db'];
		$result = update_user($conn, $info) ;
		$message = "";
		if(empty($result)) {
			//$viewData['msg'] = array('flag' => FALSE, 'msg' => '更新失败，请重试');
			$message = "更新失败，请重试";
		}else{

			//update_user_group($conn, $id, $groups); //更新用户与组的关系
			up_user_app_flag($conn,$id,0);
			update_user_app_relations($conn, $id, $apps); //更新应用与用户间的关系

			$this->get_userful_groups($viewData ,$conn, $id) ; //用户和用户组关系
			$this->get_useful_apps($viewData, $conn, $id);//用户与APP关联信息
			$_ENV['sync']->add_sync_user_note($id);
			$tiggers = $_ENV['sync']->gen_triggers_img();

			//$viewData['msg'] = array('flag' => TRUE , 'msg' => '更新成功！'. $tiggers)
			$message = '更新成功！'. $tiggers;
		}
		//$viewData['data'] = get_one_user($conn, $id);
		//close_db($conn);
		$forward = "enter.php?m=user&a=index";
		parent::message('',$message, $forward, 10000);
		//print_r($viewData);
		//parent::view('user_edit', $viewData);
	}

	/**
	 * 删除用户
	 */
	function _bath_delete(){
		$msg = '';
		if(empty($_REQUEST['ids'])){
			$msg = '请选择要删除的用户' ;
		}else{
			$ids = $_REQUEST['ids'];

			$count = count($ids);
			$conn = $_ENV['db'];
			$rs = batch_delete_user($conn, $ids);
			$tiggers = "";
			if($count>0) {
				foreach($ids as $id) {
					$_ENV['sync']->add_del_user_note($id);
				}
				$tiggers = $_ENV['sync']->gen_triggers_img();
			}
		}
		$forward = "enter.php?m=user&a=index";
		$msg = "成功{$rs}个,共{$count}个 {$tiggers}";
		//echo $msg;
		parent::message('', $msg, $forward,10000);
		//header("location:enter.php?m=user&a=index&$msg");
	}

	/**
	 * 移动用户到某一组下
	 */
	function _batch_mv(){
		if(empty($_REQUEST['ids'])){
			parent::message('参数错误', '请选技要移动的用户');
			return ;
		}
		$ids = $_REQUEST['ids'];
		$conn = $_ENV['db'];
		foreach($ids as $id) {
			$group = empty($_REQUEST['group']) ? 0 : intval($_REQUEST['group']);
			move_user($conn, $id, $group);
		}
		parent::message('操作成功', '选定的用户已移动到指定组下', 'enter.php?m=user&a=index');

	}

	/**
	 * 系统重新生成密码
	 */
	function _reset_pwd() {
		$id = empty($_REQUEST['id'])? '' : $_REQUEST['id'];
		$pwd = empty($_REQUEST['pwd']) ? '' : $_REQUEST['pwd'] ;
		$rpwd = empty($_REQUEST['rpwd']) ? '' : $_REQUEST['rpwd'];
		if (!empty($pwd) && $pwd != $rpwd) {
			$msgstr = "两次密码不一样，请重新设置";
			$msg = 'msg[flag]=false&msg[msg]=' . urlencode($msgstr) ;
			header("location:enter.php?m=user&a=edit_pass&id={$id}&msg={$msg}");
			return;
		}elseif(!empty($pwd) && (strlen($pwd)  > 15 || strlen($pwd) < 6)) {
			$msgstr = "密码长度在6到15位之间";
			$msg = 'msg[flag]=false&msg[msg]=' . urlencode($msgstr) ;
			header("location:enter.php?m=user&a=edit_pass&id={$id}&msg={$msg}");
			return;
		}
		//$tmpl = "msg[flag]=%s&msg[msg]=%s";
		$msg = '';
		if(empty($id)) {
			$msg =  '参数有误，请重试';
		}else{
			$conn = $_ENV['db'];
			$rs = reset_user_pwd($conn, $id, $pwd);
			$flag = $rs['flag'] ? '1' : '0';

			//close_db($conn);

			$_ENV['sync']->add_sync_user_note($id);
			//$_ENV['sync']->dosync();
			$tiggers = $_ENV['sync']->gen_triggers_img();
			$msg = $rs['msg'] . $tiggers;
		}
		$forward = "enter.php?m=user&a=index";
		parent::message('', $msg, $forward, 10000);
		//header("location:enter.php?m=user&a=index&$msg");
	}



	/**
	 * 验证数据是否正确
	 * @param $db 数据库连接对象
	 * @param $data 数据
	 * @return 是否通过验证
	 */
	function check_data($data) {
		if(empty($data['name'])){
			return '用户名不能为空';
		}
		return '';
	}

	/**
	 * 用户绑定应用用管理
	 */
	function _apps(){
		$viewData = array();
		$tmpl = "msg[flag]=%s&msg[msg]=%s";
		if(empty($_REQUEST['uid'])){
			$msg = sprintf($tmpl, '0', urlencode('参数有误，请重试'));
			echo "<script type='text/javascript'>location.href='enter.php?m=user&a=index&$msg';</script>";
			exit;
		}
		$uid = $_REQUEST['uid'];

		$viewData = $this->get_user_appdata($uid ,$viewData);
		return parent::view('user_apps', $viewData);
	}

	/**
	 * 查询应用用户列表操作数据
	 * @param $conn 数据库连接对象
	 * @param $uid 用户ID
	 */
	function get_user_appdata($uid, $viewData) {
		$viewData['apps'] = search_apps($this->conn, array(), 0, 0 );
		$viewData['myapps'] = search_appuserip($this->conn, array('user_id' => $uid), 0 ,0, ' aui.flag asc, aui.app_id desc');
		$viewData['uid'] = intval($uid);
		return $viewData;
	}

	/**
	 * 禁止用户访问某应用
	 */
	function _disable_uapp(){
		$viewData = array();
		if(empty($_REQUEST['uid']) || empty($_REQUEST['appid'])) {
			$viewData['msg'] = array('flag' => FALSE, 'msg' => '操作失败，请重试');
			return parent::view('user_apps', $viewData);
			exit;
		}

		$uid = $_REQUEST['uid'];
		$appid = $_REQUEST['appid'] ;
		up_user_app_flag($this->conn, $uid, $appid, true);
		$viewData['msg'] = array('flag' => TRUE, 'msg' => '操作成功');
		$viewData = $this->get_user_appdata($uid ,$viewData);
		return parent::view('user_apps', $viewData);
	}

	/**
	 * 允许用户访问某应用
	 */
	function _enable_uapp(){
		$viewData = array();
		if(empty($_REQUEST['uid']) || empty($_REQUEST['appid'])) {
			$viewData['msg'] = array('flag' => FALSE, 'msg' => '操作失败，请重试');
			return parent::view('user_apps', $viewData);
			exit;
		}

		$uid = $_REQUEST['uid'];
		$appid = $_REQUEST['appid'] ;
		up_user_app_flag($this->conn, $uid, $appid, false);
		$viewData['msg'] = array('flag' => TRUE, 'msg' => '操作成功');
		$viewData = $this->get_user_appdata($uid ,$viewData);
		return parent::view('user_apps', $viewData);
	}


	/**
	 * 启用用户访问某应用的IP绑定
	 */
	function _enable_ipband(){
		$viewData = array();
		if(empty($_REQUEST['uid']) || empty($_REQUEST['appid'])) {
			$viewData['msg'] = array('flag' => FALSE, 'msg' => '操作失败，请重试');
			return parent::view('user_apps', $viewData);
			exit;
		}

		$uid = $_REQUEST['uid'];
		$appid = $_REQUEST['appid'] ;
		up_user_app_is_band($this->conn, $uid, $appid, false);
		$viewData['msg'] = array('flag' => TRUE, 'msg' => '操作成功');
		$viewData = $this->get_user_appdata($uid ,$viewData);
		return parent::view('user_apps', $viewData);
	}

	/**
	 * 禁用用用户访问某应用的IP绑定
	 */
	function _disable_ipband(){
		$viewData = array();
		if(empty($_REQUEST['uid']) || empty($_REQUEST['appid'])) {
			$viewData['msg'] = array('flag' => FALSE, 'msg' => '操作失败，请重试');
			return parent::view('user_apps', $viewData);
			exit;
		}

		$uid = $_REQUEST['uid'];
		$appid = $_REQUEST['appid'] ;
		up_user_app_is_band($this->conn, $uid, $appid, true);
		$viewData['msg'] = array('flag' => TRUE, 'msg' => '操作成功');
		$viewData = $this->get_user_appdata($uid ,$viewData);
		return parent::view('user_apps', $viewData);
	}

	/**
	 * 编辑应用对用户关联信息
	 */
	function _edit_userapp(){
		$viewData = array();

		$viewData['app_id'] = $appid = empty($_REQUEST['appid']) ? 0 : $_REQUEST['appid'];
		$viewData['user_id'] = $userid = empty($_REQUEST['uid']) ? 0 : $_REQUEST['uid'] ;

		if(empty($appid)) {
			$err = $this->appuserip_err('没有指定应用，请重新操作') ;
			$viewData = array_merge($viewData, $err);
		}else{
			$this->include_appuserid_fun();
			$data = get_appuserip($this->conn, $appid, $userid);
			if(empty($data)) {
				$err = $this->get_app_and_user($this->conn, $userid, $appid);
				//	print_r($err);
				$viewData = array_merge($viewData , $err);
			}else{
				$viewData['data'] = $data;
			}

			//$viewData['users'] = get_app_user($this->conn, $appid);
		}
		$viewData['ipgroup'] = all_ipgroup($this->conn);
		//$viewData['msg'] = array('flag' => true, 'msg' => '操作已成功');
		//close_db($conn);
		parent::view('user_appipedit', $viewData);
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
			$viewData['msg'] = array('flag' => true, 'msg' => '操作已成功！');
		}
		$viewData['user_id'] = $info['user_id'];
		$viewData['app_id'] = $info['app_id'];
		$viewData['data'] = get_appuserip($conn, $info['app_id'], $info['user_id']);
		//$viewData['users'] = get_app_user($conn, $info['app_id']);

		//close_db($conn);
        $viewData['ipgroup'] = all_ipgroup($this->conn);
		parent::view('user_appipedit', $viewData);
	}

	/**
	 * 批量修改用户APP绑定
	 */
	function _batch_app(){
		$viewData = array();
		if(empty($_REQUEST['ids'])) {
			$viewData['msg'] = array('flag' => FALSE, 'msg' => '没有指定用户，请重试');
			return parent::view('user_batchappipedit', $viewData);
		}
		$ids = $_REQUEST['ids'];
		$users = array();
		$conn = $_ENV['db'];
		$viewData['group'] = search_group($conn, array(), 0, 0); //所有组
		$viewData['apps'] = search_apps($conn, array(), 0, 0 ); //所有的APP

		foreach($ids as $id) {
			$users[] = get_one_user($conn,$id);
		}
		$viewData['users'] = $users;
		return parent::view('user_batchappipedit', $viewData);
	}

	/**
	 * 保存批量绑定应用
	 */
	function _batch_saveapp(){
		if(empty($_REQUEST['uid'])) {
			$viewData['msg'] = array('flag' => FALSE, 'msg' => '没有指定用户，请重试');
			return parent::view('user_batchappipedit', $viewData);
		}

		$conn = $_ENV['db'];
		$uids = $_REQUEST['uid'];
		$info = $_REQUEST['info'];
		$apps = array();
		if(isset($info['apps'])) {
			$apps = $info['apps'];
			unset($info['apps']);
		}

		$groups = array();

		if(isset($info['group'])) {
			$groups = $info['group'];
			unset($info['group']);
		}

		foreach($uids as $uid) {
			up_user_app_flag($conn, $uid, 0);
			update_user_group($conn, $uid, $groups); //更新用户与组的关系
			update_user_app_relations($conn, $uid, $apps); //更新应用与用户间的关系
		}

		echo "<script type='text/javascript'>alert('操作已成功！');location.href='enter.php?m=user&a=index';</script>";

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

}

?>