<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 应用管理操作函数库
 * @author d-boy
 * $Id: app.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/

	if(!defined('APP_TABLE')) define('APP_TABLE',table_name(UCENTER_DBNAME, 'application',  UCENTER_TABLE_PRE)); //应用数据表名
	if(!defined('APPUSERIP_TABLE')) define('APPUSERIP_TABLE',table_name(UCENTER_DBNAME, 'app_user_ip',  UCENTER_TABLE_PRE)); //应用数据表名
	if(!defined('USER_TABLE')) define('USER_TABLE',table_name(UCENTER_DBNAME, 'user_base',  UCENTER_TABLE_PRE)); //应用数据表名
	if(!defined('USERGROUP_TABLE')) define('USERGROUP_TABLE',table_name(UCENTER_DBNAME, 'user_group', UCENTER_TABLE_PRE )); //用户组关系数据表全名
	if(!defined('GROUP_TABLE')) define('GROUP_TABLE',table_name(UCENTER_DBNAME, 'base_group', UCENTER_TABLE_PRE )); //用户组数据表全名
	if(!defined('GROUPAPP_TABLE')) define('GROUPAPP_TABLE', table_name(UCENTER_DBNAME, 'group_app',  UCENTER_TABLE_PRE)); //应用用户组关系数据表全名
	
	/**
	 * 查询应用数
	 * @param $db 数据库连接对象
	 * @param $keys 条件
	 * @return 符合条件的应用数
	 */
	function count_apps($db, $keys = array()) {
		$table = APP_TABLE;
		$where = get_apps_where($keys);
		$data = exec_count($db, $table, $keys);
		
		return $data;
	}
	
	/**
	 * 查询应用信息
	 * @param $conn 数据库连接
	 * @param $keys 条件
	 * @param $start 开始
	 * @param $end 结束
	 * @param $order 排序条件
	 * @return 符合条件的数据
	 */
	function search_apps($conn, $keys=array(), $start, $end, $order=' id desc') {
		$table = APP_TABLE;
		$where = get_apps_where($keys);
		if(!empty($order)) $where .= ' order by ' . $order;
		if(!empty($end)) $where .= ' limit ' . intval($start) . ',' . intval($end);
		$sql = "select * from $table $where";
        //echo $sql;
		return select($conn, $sql);
	}
	
	/**
	 * 返回WHERE子句
	 * @param $keys 条件
	 * @return WHERE子句
	 */
	function get_apps_where($keys) {
		
		$where = ' where flag = 0 ' ;
		if(!empty($keys)){
			if(!empty($keys['id'])) $where .= ' and id=' . intval($keys['id']);
			if(!empty($keys['app_name'])) $where .= ' and app_name like ' . quote_smart('%'.$keys['app_name'].'%');
			if(!empty($keys['create_date'])) $where .= ' and create_date = ' . quote_smart($keys['create_date']);
			if(isset($keys['flag'])) $where .= ' and flag = ' . intval($keys['flag']);
			if(!empty($keys['uc_sign'])) $where .= ' and uc_sign=' . quote_smart("{$keys['uc_sign']}");
		}
		return $where;
	}
	
	/**
	 * 查询一个应用信息
	 * @param $conn 数据连接
	 * @param $id ID
	 * @return 一个应用信息
	 */
	function get_one_app($conn, $id) {
		$data = search_apps($conn, array('id'=> intval($id)), 0, 0);
		return empty($data) ? array() : $data[0];
	}
	
	/**
	 * 插入新的应用数据
	 * @param $conn 数据库连接
	 * @param $info 应用信息
	 * @return 成功返回ID
	 */
	function insert_app($conn, $info = array()) {
		$table = APP_TABLE;
		//生成随机密码
		$pwd = rand_str(16);
		$md5pwd = md5($pwd);
		$info['token'] = $md5pwd;
		$info['token_content'] = $pwd;
		return insert_data($conn, $table, $info);
	}
	
	/**
	 * 更新应用信息
	 * @param $conn 数据库连接
	 * @param $info 信息
	 * @return 成功返回大于0的数
	 */
	function update_app($conn, $info) {		
		$id = intval($info['id']);
		unset($info['id']);
		$table = APP_TABLE;
		$where = ' id=' . $id;
		return update_data($conn, $table, $info, $where);
	}
	
	/**
	 * 保存修改的应用信
	 * @param $conn 数据连接
	 * @param $info 应用信息
	 * @return array(flag => 成功？, msg=>信息)
	 */
	function do_update_app($conn, $info){
		if(empty($info['id'])) return array('flag' => false, 'msg' => '信息不正确，请重试');
		$groups = array();
		if(isset($info['groups'])) {
			$groups = $info['groups'];
			unset($info['groups']);
		}
		$id = $info['id'];
		//print_r($groups);
		$rs = update_app($conn, $info); //更新数据
		/*if(!empty($rs)) {			
			//更新关系
 			update_app_group($conn, $id, $groups);
		}*/
		return empty($rs) ? array('flag'=>false, 'msg'=>'更新失败') : array('flag'=>true, 'msg'=>'更新成功');
	}
	
	/**
	 * 保存新增的应用
	 * @param $conn 数据连接
	 * @param $info 应用信息
	 * @return array(flag => 成功？, msg=>信息)
	 */
	function save_app($conn, $info){
		
		if(empty($info) ) {
			return array('flag' => false, 'msg' => '信息不正确，请重试');
		}
		$groups = array();
		if(isset($info['groups'])) {
			$groups = $info['groups'];
			unset($info['groups']);
		}
		
		if(empty($info['create_date'])) $info['create_date'] = Date('Y-m-d');
		$id = insert_app($conn, $info);
		//$info['id'] = $id;
		if(empty($id)) {
			return array('flag' => false, 'msg' => '保存失败');
		}else{
			//更新关系
 			//update_app_group($conn, $id, $groups);
 			//$group = get_app_group($conn, $id);
			return array('flag' => true, 'msg' => '保存成功，新应用ID为' . $id);
		}
	}
	
	/**
	 * 检查应用信息是否合法
	 * @param $info
	 * @return 操作结果
	 */
	function check_app($info) {
		if(empty($info)) {
			return '没有任何信息可保存';
		}
		if(empty($info['app_name'])) {
			return '应用名不能为空' ;
		}
	
		if(empty($info['api_addr']) || !preg_match("/^(http:\/\/).*/", $info['api_addr'])) {
			return 'API URL 格式不正确';
		}
		return '';
	}
	
	/**
	 * 删除一个应用信息
	 * @param $conn 数据连接
	 * @param $id ID
	 * @return 成功返回大于0的数
	 */
	function delete_app($conn, $id) {
		$table = APP_TABLE;
		$where = ' id=' . intval($id);
		$rs = update_data($conn, $table,array('flag'=>1), $where);
		return $rs;
	}
	
	/**
	 * 批量删除就用信息
	 * @param $conn 数据库连接
	 * @param $ids ID数组
	 * @return array() 操作结果
	 */
	function batch_delete_app($conn, $ids) {
		$result = array();
		if(empty($ids) || !is_array($ids)) {
			$result['falg'] = '0';
			$result['msg'] = '删除失败,参数不正确';
		}else {
			$ok = 0;
			$count = count($ids);
			foreach($ids as $id) {
				if(delete_app($conn, $id) > 0) $ok++;
			}
			$result['flag'] = '1';
			$result['msg'] = '成功删除' . $ok . '个，共' . $count . '个';
		}
		
		return $result;
	}
	
	/**
	 * 查询一个用户可访问的APP
	 * @param $conn 数据库连接
	 * @param $uid 用户ID
	 * @param $enabledFlag 是否查询可用的
	 * @return 用户可访问的APP
	 */
	function get_user_apps($conn, $uid , $enabledFlag = false) {
		$uid = intval($uid);
		$en = $enabledFlag ? ' and flag = 0 ' : ''; 
		
		$sql = "select * from " . APP_TABLE ." where flag=0 and id in (
				select app_id from  " . APPUSERIP_TABLE . " where user_id = $uid $en
				)";
		
		$data = select($conn, $sql) ;
		return $data;
	}
	
	/**
	 * 向一个应用同步所有用户
	 * @param $app 应用信息
	 * @param $users 用户信息
	 */
	function sync_all_users($app = array(), $users = array()){
		if(empty($app) || empty($users)) return ;
		include_once UC_ROOT . '/common/param_key.class.php';
		include_once UC_ROOT . '/common/param_helper.class.php';
		require_once UC_ROOT . '/common/mysql.class.php';
		include_once UC_ROOT . '/common/uccode.class.php';
		include_once UC_ROOT . '/common/ucsocket.class.php';
		include_once UC_ROOT . '/common/customdes.class.php';
		require_once FUN_ROOT . 'common.fun.php';
		require_once FUN_ROOT . 'conn.fun.php' ;
		ignore_user_abort(true);
		$helper = new ParamHelper($app['token']);
		//$helper->set_key($app['token']);
		$socket = new  UCSocket();
		$f = fopen('synclog.txt', 'a+');
		foreach($users as $user){
			$helper->set_param(array());
			$helper->append_param(ParamKey::key_token, $app['token']);
			$helper->append_param(ParamKey::key_action, ParamKey::SYNC_USER);
			$helper->append_param(ParamKey::key_uid, $user['id']);
			$helper->append_param(ParamKey::key_user_name, base64_encode($user['name']));
			$helper->append_param(ParamKey::key_password, $user['password']);
			$helper->append_param(ParamKey::key_email, $user['email']);
			$code = $helper->serialize_param();
			$url = $app['api_addr'] . '?' . ParamKey::key_todo . '=' . urlencode($code);
			fwrite($f, $url . "\n");
			$socket->fopen2($url);
		}
		 
		fclose($f);
	}
	
?>