<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 用户和用户组关系管理操作函数库
 * @author d-boy
 * $Id: usergroup.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/

	if(!defined('USER_TABLE')) define('USER_TABLE', table_name(UCENTER_DBNAME,'user_base', UCENTER_TABLE_PRE )); //用户数据表名
	if(!defined('USERGROUP_TABLE')) define('USERGROUP_TABLE',table_name(UCENTER_DBNAME, 'user_group', UCENTER_TABLE_PRE )); //用户组关系数据表全名
	if(!defined('GROUP_TABLE')) define('GROUP_TABLE',table_name(UCENTER_DBNAME, 'base_group', UCENTER_TABLE_PRE )); //用户组数据表全名
	
	include_once FUN_ROOT . 'groupapp.fun.php';
	include_once FUN_ROOT . 'appuserip.fun.php';
		
	/**
	 * 返回一个用户所属的组
	 * @param $conn 数据库连接
	 * @param $uid 用户ID
	 * @return 用户所属的组信息
	 */
	function get_user_group($conn, $uid) {
		$uid = intval($uid);
		$sql = "select g.* from " . USERGROUP_TABLE . " ug inner join " . GROUP_TABLE 
			   . " g on ug.group_id = g.id where ug.user_id=$uid";
		
		$result = array();
		$data = select($conn, $sql) ;
		foreach($data as $item) {
			$result[$item['id']] = $item;
		}
		
		return $result;
	}
	
	/**
	 * 清除一个用户的组的关系
	 * @param $conn 数据库连接
	 * @param $uid 用户ID
	 * @param $gid 组ID
	 * @return 是否成功
	 */
	function delete_user_group($conn, $uid, $gid = 0) {
		$uid = intval($uid);
		if(empty($uid) && empty($gid)) return false;
		$where = '';
		if(!empty($uid))
			$where = ' user_id = ' . $uid;
		if(!empty($gid)) {
			$where += ' and group_id = ' . intval($gid);
		}		
		
		//删除用户与组的关联
		$rs = delete_data($conn, USERGROUP_TABLE, $where);
		
		if(!empty($gid) && empty($uid)) {
			//查询这个组下的所有用户
			//$usergroups = group_users($conn, $gid);
			
			//查询这个组下可访问的应用
			$apps = get_group_app($conn, $gid) ;
			if(!empty($apps)) {
				foreach($apps as $a) {
					//更新用户应用对应		
					up_user_app_flag($conn, 0, $a['id']);
				}
			}
		}else {
			//更新用户应用对应		
			up_user_app_flag($conn, $uid, $gid);
		}
		
		return $rs;
	}
	
	/**
     * 一个组下的所有用户
     * @param $conn 数据库连接对象
     * @param $group 用户组ID
     * @return 用户
     */
    function group_users($conn, $group) {
        if(empty($group)) return array();
        $sql = "select * from " . USERGROUP_TABLE . ' where group_id=' . intval($group);
        return select($conn, $sql);
    }
    
    /**
     * 一个组下的所有用户ID
     * @param $conn 数据库连接对象
     * @param $group 用户组ID
     * @return 用户
     */
    function group_user_ids($conn, $group) {
        if(empty($group)) return array();
        $sql = "select user_id as id from " . USERGROUP_TABLE . ' where group_id=' . intval($group);
        return select($conn, $sql);
    }
	
	/**
	 * 插入新的用户组关系
	 * @param $conn 数据库连接
	 * @param $uid 用户ID
	 * @param $groupid 用户组ID
	 * @return 是否成功
	 */
	function insert_user_group($conn, $uid, $groupid) {
		$uid = intval($uid);
		$groupid = intval($groupid);
		$sql = "insert into " . USERGROUP_TABLE . " (user_id,group_id) values({$uid}, {$groupid});";
		$rs = query($conn, $sql);
		
		//更新用户应用关联
		if($rs){
			//用户组可访问的应用
			update_user_app_relation($conn, $groupid, $uid);
		}
	}
	
	/**
	 * 更新一个用户的组关系
	 * @param $conn 数据库连接
	 * @param $uid 用户ID
	 * @param $groupids 组ID数组
	 * @return 成功数
	 */
	function update_user_group($conn, $uid, $groupids = array()) {		
		//清除原有的关系
		delete_user_group($conn, $uid);
		
		$ok = 0;
		foreach($groupids as $group) {
			$rs = insert_user_group($conn, $uid, $group);
			if(!empty($rs)) $ok ++; 
		}
		
		//更新用户应用关联
		if($ok > 0) {
			//up_user_app_flag($conn, $uid, 0); //禁用这个用户的所有应用
			
			/*
			//重新构建关联
			foreach($groupids as $group) {
				//用户组可访问的应用
				update_user_app_relation($conn, $group, $uid);
			}*/
		}
		
		return $ok;
	}
	
	/**
	 * 更新用户应用关联
	 * @param $gid 用户组ID
	 * @param $uid 用户ID
	 * @param $conn 数据库连接串
	 */
	function update_user_app_relation($conn, $gid, $uid){
		$apps = get_group_app($conn, $gid) ;
		
		if(!empty($apps)) {
			foreach($apps as $a) {
				$info = array('app_id' => $a['id'], 'user_id' => $uid , 'flag' => '0');
				add_or_update_appuserip($conn, $info);
			}
		}
	}
	
	
?>