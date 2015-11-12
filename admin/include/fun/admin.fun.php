<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 管理员管理操作函数库
 * @author d-boy
 * $Id: admin.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/
	
	if(!defined('ADMIN_TABLE')) define('ADMIN_TABLE',table_name(UCENTER_DBNAME, 'admin_info', UCENTER_TABLE_PRE )); //用户数据表名
	if(!defined('USER_TABLE')) define('USER_TABLE',table_name(UCENTER_DBNAME, 'user_base', UCENTER_TABLE_PRE )); //用户数据表全名

	/**
	 * 查询管理员数
	 * @param $conn 数据库连接对象
	 * @param $where 条件数组
	 * @return 符合条件的用户数
	 */
	function count_admins($conn, $where = array()) {
		$w = get_admin_where($where);
		$table = get_admin_join();
		return exec_count($conn, $table, $w);
	}
	
	/**
	 * 返回数据表连接串
	 * @return 表连接串
	 */
	function get_admin_join() {
		return ADMIN_TABLE . ' a inner join ' . USER_TABLE . ' u on a.id = u.id ';
	}
	
	/**
	 * 组装WHERE 子句
	 * @param $where 条件
	 * @return WHERE子句
	 */
	function get_admin_where($where = array()) {
		$w = '';
		if(!empty($where)) {
			$w .= empty($where['admin_name']) ? '' : ' and u.name like ' . quote_smart("%{$where['admin_name']}%") ;
			$w .= empty($where['id']) ? '' : ' and a.id = ' . intval($where['id']);
			$w .= empty($where['email']) ? '' : ' and u.email like ' . quote_smart("%{$where['email']}%");
		}
		return empty($w) ? $w : ' where 1=1 ' . $w;
	}
	
	/**
	 * 查询管理员信息
	 * @param $conn 数据连接
	 * @param $where 条件
	 * @param $start 开始记录
	 * @param $end 结束记录
	 * @param $order 排序条件
	 * @return 符合条件的数据
	 */
	function search_admins($conn, $where = array(), $start = 0, $end = 0, $order = ' id desc') {
		$w = get_admin_where($where) ;
		$t = get_admin_join();
		if(!empty($order)) $w .= ' order by ' . $order;
		if(!empty($end)) $w .= ' limit ' . intval($start) . ' , ' . intval($end);
		$sql = " select u.* from $t $w";
		$data = select($conn, $sql);
		return $data;
	} 
	
	/**
	 * 返回一个管理员信息
	 * @param $conn 数据库连接对像
	 * @param $adminid 管理员ID
	 * @return 管理员信息
	 */
	function get_admin_user($conn, $adminid ) {
		$where = array('id' => $adminid);
		$data = search_admins($conn, $where, 0, 0);
		return empty($data) ? $data : $data[0];
	}
	
	/**
	 * 按用户名查询管理员用户信息
	 * @param $conn 数据库连接
	 * @param $name 用户名
	 * @return 管理员用户信息
	 */
	function get_admin_by_name($conn, $name) {
		$t = get_admin_join();
		$sql = " select u.* from $t where u.name =" . quote_smart($name);
		$data = select($conn, $sql);
		return empty($data) ? $data : $data[0];
	}
	
	
	/**
	 * 保存管理员信息
	 * @param $username 用户名
	 * @param $info 信息
	 * @return 是否成功
	 */
	function save_admin($conn, $username) {
		//查询用户存不存在
		include_once FUN_ROOT . 'user.fun.php';
		$user = one_user_by_name($conn, $username);
		if(empty($user)) {
			return array('flag' => false, 'msg' => '用户' . $username . '不存在');
		}
		
		$admin = get_admin_user($conn, $user['id']);
		
		if(!empty($admin)) {
			return array('flag' => false, 'msg' => '用户' . $username . '已经是管理员了');
		}
		
		//将用户设为管理员
		$info = array('id' => $user['id'] , 'admin_name' => $user['name']) ;
		if(insert_admin($conn, $info) > 0 )
			return array('flag'=> true, 'msg' => '添加成功');
		else 
			return array('flag' => false, 'msg' => '添加失败，请重试');	
	}
	
	/**
	 * 插入新的管理员信息
	 * @param $conn 数据库连接
	 * @param $info 信息
	 */
	function insert_admin($conn, $info = array()) {
		return insert_data_noiden($conn, ADMIN_TABLE, $info) ;
	}
	
	/**
	 * 删除管理员信息
	 * @param $conn 数据库连接
	 * @param $id 管理员ID
	 * @return 是否成功
	 */
	function delete_admin($conn, $id) {
		$w = ' id=' . intval($id);
		return delete_data($conn, ADMIN_TABLE, $w);
	}
	
	/**
	 * 批量删除信息
	 * @param $conn 数据库连接
	 * @param $ids ID数组
	 * @return 成功的数量
	 */
	function batch_delete_admin($conn, $ids = array()) {
		$acc = array();
		foreach($ids as $id) {
			if(delete_admin($conn, $id) > 0) {
				$acc[] = $id;
			}
		}
		return $acc;
	}
	
	
?>