<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 应用和组关系管理操作函数库
 * @author d-boy
 * $Id: groupapp.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/

	if(!defined('APP_TABLE')) define('APP_TABLE',table_name(UCENTER_DBNAME, 'application',  UCENTER_TABLE_PRE)); //应用数据表名
	if(!defined('GROUP_TABLE')) define('GROUP_TABLE',table_name(UCENTER_DBNAME,'base_group',UCENTER_TABLE_PRE)); //用户组数据表名	
	if(!defined('GROUPAPP_TABLE')) define('GROUPAPP_TABLE',table_name(UCENTER_DBNAME,'group_app',UCENTER_TABLE_PRE)); //用户组数据表名	


	/**
	 * 查询可以访问某应用的所有用户组
	 * @param $conn 数据库连接
	 * @param $appid 应用ID
	 * @return 可以访问某应用的所有用户组
	 */
	function get_app_group($conn, $appid) {
		$id = intval($appid);
		$sql = "select g.* from " . APP_TABLE . " app left join " . GROUPAPP_TABLE . " gap on app.id = gap.app_id left join "
			 .  GROUP_TABLE . " g on gap.group_id = g.id where app.id = $id" ;
		$data = select($conn, $sql);
		$result = array();
		foreach($data as $item) {
			$result[$item['id']] = $item;
		}	 
		
		return $result;
	}
	
	/**
	 * 查询一个用户组可访问的应用
	 * @param $conn 数据库连接
	 * @param $groupid 用户组ID
	 * @return 一个组可访问的应用信息
	 */
	function get_group_app($conn, $groupid) {
		$id = intval($groupid);
		$sql = "select app.* from " . APP_TABLE . " app inner join " . GROUPAPP_TABLE . " gap on app.id = gap.app_id inner join "
			 .  GROUP_TABLE . " g on gap.group_id = g.id where g.id = $id" ;
		$data = select($conn, $sql);
		$result = array();
		foreach($data as $item) {
			$result[$item['id']] = $item;
		}	 
		
		return $result;
	}
	
	/**
	 * 删除一个用户组下所有可访问的应用的关系
	 * @param $conn 数据库连接
	 * @param $group 组ID
	 * @return 是否成功
	 */
	function delete_group_app($conn, $group) {
		$group = intval($group);
		$where = " group_id = $group";
		$rs = delete_data($conn, GROUPAPP_TABLE, $where);
		
		return $rs;
	}
	
	/**
	 * 删除一个应用下的用户组关系
	 * @param $conn 数据库连接
	 * @param $appid 应用ID
	 * @return 是否成功
	 */
	function delete_app_group($conn, $appid) {
		$appid = intval($appid);
		$where = " app_id = $appid";
		return delete_data($conn, GROUPAPP_TABLE, $where);
	}
	
	/**
	 * 更新一个应用与用户组的关系
	 * @param $conn 数据连接
	 * @param $appID 应用ID
	 * @param $groupIDs 用户组
	 * @return 操作结果
	 */
	function update_app_group($conn, $appID, $groupIDs) {
		//print_r($groupIDs);
		if(empty($appID) || !is_array($groupIDs)) return false;

		//先清掉原有的关系
		delete_app_group($conn, $appID);
		//建立新关系
		$ok = 0;
		
		foreach($groupIDs as $group) {
			$ok += insert_groupapp($conn, $group, $appID);
		}
		
		return $ok;
	}
	
	
	/**
	 * 更新用户组可访问的应用
	 * @param $conn 数据库连接
	 * @param $groupID 用户组ID
	 * @param $appIDs 应用ID数组
	 * @return 操作结果
	 */
	function update_group_app($conn, $groupID, $appIDs) {
		if(empty($appIDs) ||  !is_array($appIDs)) return false;
		//先清掉原有的关系
	
		delete_group_app($conn, $groupID);
		//print_r($appIDs);
		//建立新关系
		$ok = 0;
		
		foreach($appIDs as $app) {
			$ok += insert_groupapp($conn, $groupID, $app);
		}
		
		return $ok;
	}
	
	/**
	 * 插入新的用户组和应用关系
	 * @param $conn 数据连接
	 * @param $groupid 用户组ID
	 * @param $appid 就用ID
	 */
	function insert_groupapp($conn, $groupid, $appid) {
		$info = array('group_id' => intval($groupid), 'app_id' => intval($appid));
		
		insert_data($conn, GROUPAPP_TABLE, $info);
		return 1;
	}
	
?>