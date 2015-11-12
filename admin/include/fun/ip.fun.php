<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 管理员管理操作函数库
 * @author d-boy
 * $Id: ip.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/
	
	if(!defined('IP_TABLE')) define('IP_TABLE',table_name(UCENTER_DBNAME, 'ip_group', UCENTER_TABLE_PRE )); //用户数据表名
	
	/**
	 * 返回所有的IP组
	 * @param $conn 数据库连接
	 */
	function all_ipgroup($conn){
		$sql = "select * from " . IP_TABLE;
		$data = select($conn, $sql);
		return $data;
	}	
	
	/**
	 * 返回一个用户IP
	 * @param $conn 数据库连接
	 * @param $id 用户ID
	 */
	function get_one_ip_group($conn , $id){
		$sql = "select * from " . IP_TABLE . " where id=" . intval($id);
		$data  = get_one($conn, $sql);
		return $data;
	}
	
	/**
	 * 添加新的IP组
	 * @param $conn 数据库连接
	 * @param $info IP组信息
	 */
	function insert_ip_group($conn , $info = array()){
		if(empty($info)) {
			return 0;
		}
		$id = insert_data($conn, IP_TABLE, $info);
		return $id;
	}
	
	/**
	 * 更新IP组
	 * @param $conn 数据库连接
	 * @param $id IP组ID
	 * @param $info 要更新的数据
	 */
	function update_ip_group($conn, $id, $info = array()){
		$rs = update_data($conn , IP_TABLE, $info, "id=" . intval($id));
		return $rs;
	}
	
	/**
	 * 删除IP组
	 * @param $conn 数据库连接
	 * @param $id 组ID
	 */
	function delete_ip($conn, $id){
		$result = delete_data($conn, IP_TABLE, ' id=' . intval($id));
		return $result;
	}
?>