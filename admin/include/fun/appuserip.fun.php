<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 应用用户IP绑定管理操作函数库
 * @author d-boy
 * $Id: appuserip.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/

	if(!defined('APP_TABLE')) define('APP_TABLE',table_name(UCENTER_DBNAME, 'application',  UCENTER_TABLE_PRE)); //应用数据表名
	if(!defined('APPUSERIP_TABLE')) define('APPUSERIP_TABLE',table_name(UCENTER_DBNAME, 'app_user_ip',  UCENTER_TABLE_PRE)); //应用数据表名
	if(!defined('USER_TABLE')) define('USER_TABLE',table_name(UCENTER_DBNAME, 'user_base',  UCENTER_TABLE_PRE)); //应用数据表名
	if(!defined('USERGROUP_TABLE')) define('USERGROUP_TABLE',table_name(UCENTER_DBNAME, 'user_group', UCENTER_TABLE_PRE )); //用户组关系数据表全名
	if(!defined('GROUP_TABLE')) define('GROUP_TABLE',table_name(UCENTER_DBNAME, 'base_group', UCENTER_TABLE_PRE )); //用户组数据表全名
	if(!defined('GROUPAPP_TABLE')) define('GROUPAPP_TABLE', table_name(UCENTER_DBNAME, 'group_app',  UCENTER_TABLE_PRE)); //应用用户组关系数据表全名
	
	/**
	 * 添加或更新应用用户IP绑定信息
	 * @param $conn 数据库连接
	 * @param $info 信息
	 */
	function add_or_update_appuserip($conn, $info = array()) {
		//print_r($info);
		if(!empty($info) && !empty($info['user_id']) && !empty($info['app_id'])) {
			$data = get_appuserip($conn, $info['app_id'], $info['user_id']) ;
			if(!empty($data)) {
				//更新
				$appid = $info['app_id'] ; 
				$userid = $info['user_id'] ;
				unset($info['app_id']);
				unset($info['user_id']);
				//$info['flag'] = 0;
				update_appuserip($conn, $info, $appid, $userid) ;
			} else {
				//新增
				//$info['flag'] = 0;
				insert_appuserip($conn, $info);
			}
			return true;
		}
		return false;
	}
	
	
	/**
	 * 返回一个应用下所有的用户信息
	 * @param $conn 数据连接
	 * @param $appid 应用ID
	 * @return 用户信息
	 */
	function get_app_user($conn, $appid , $ignoreFlag = false) {
		$appid = intval($appid);
		/*$sql ="select id,name,email from " . USER_TABLE . "
			   where id in (
 			   select distinct ug.user_id from " . APP_TABLE . " app 
 			   inner join " . GROUPAPP_TABLE . " ga on app.id = ga.app_id
 			   inner join " . USERGROUP_TABLE . " ug on ga.group_id = ug.group_id
 			   where app.id = $appid
			   )";*/
		$flag = $ignoreFlag ? ' ' : ' and flag = 0';
		$sql ="select id,name,email from " . USER_TABLE . "
			   where id in (
 			   select user_id from " . APPUSERIP_TABLE . "  			 
 			   where app_id = $appid $flag
			   )";
		//echo $sql;
		return select($conn, $sql);
	}
	
	/**
	 * 按KEY分组用户
	 * @param $conn 数据连接
     * @param $appid 应用ID
	 */
	function get_app_user_keys_by_uid($conn, $appid ){
		$data = get_app_user($conn, $appid);
		if(empty($data)) return array();
		$rs = array();
		foreach($data as $item) {
			$id = $item['id'];
			if(!array_key_exists($id, $rs)) {
				$rs[$id] = array();
			}
			$rs[$id] = $item;
		}
		print_r($rs);
		return $rs;
	}
	
	/**
	 * 更新应用用户IP绑定
	 * @param $conn 数据库连接
	 * @param $info 数据
	 * @param $appid 应用ID
	 * @param $userid 用户ID
	 */
	function update_appuserip($conn, $info = array(), $appid, $userid) {
		$where = " app_id=" . intval($appid) . " and user_id=" . intval($userid);
		return update_data($conn, APPUSERIP_TABLE, $info, $where) ; 
	}
	
	/**
	 * 插入新的应用用户IP绑定
	 * @param $conn 数据库连接
	 * @param $info 数据
	 */
	function insert_appuserip($conn, $info = array()) {
		insert_data($conn, APPUSERIP_TABLE, $info);
	}
	
	/**
	 * 返回一个应用用户IP绑定
	 * @param $conn 数据连接
	 * @param $appid 应用ID
	 * @param $uid 用户ID
	 * @return 应用用户IP
	 */
	function get_appuserip($conn, $appid, $uid) {
		
		$keys = array('app_id' => $appid, 'user_id' => $uid);
		
		$data = search_appuserip($conn, $keys, 0, 0);
		return empty($data) ? array() : $data[0];
	}
	
	/**
	 * 查询应用IP绑定个数
	 * @param $conn 数据连接
	 * @param $keys 条件数组
	 * @return 符合条件的数据的个数
	 */
	function count_appuserip($conn, $keys = array()) {
		$where = get_appuserip_where($keys);
		$table = get_appuerip_join();
		return exec_count($conn, $table, $where);
	}
	
	/**
	 * 搜索应用用户IP绑定
	 * @param $conn 数据库连接
	 * @param $keys 条件
	 * @param $start 开始记录号
	 * @param $end 结束记录号
	 * @param $order 排序条件
	 * @return 符合条件的数据
	 */
	function search_appuserip($conn, $keys = array(), $start = 0, $end = 0, $order = ' user_id desc') {
		$where = get_appuserip_where($keys);
		$table = get_appuerip_join();
		if(!empty($order)) $where .= ' order by ' . $order;
		if(!empty($end))  $where .= ' limit ' . intval($start) . ',' . intval($end);
		$sql = " select app.app_name,aui.*,u.name as user_name from $table $where ;";
		//print_r($keys);
		//echo $sql;
		$data = select($conn, $sql);
		return $data;
	}
	
	/**
	 * 组建WHERE子句
	 * @param $keys 条件
	 * @return where 了句
	 */
	function get_appuserip_where($keys = array()) {
		$where = '';
		
		if(!empty($keys)) {
			$where .= empty($keys['app_id']) ? '' : ' and aui.app_id = ' . intval($keys['app_id']);
			$where .= empty($keys['user_id']) ? '' : ' and aui.user_id = ' . intval($keys['user_id']); 
			$where .= empty($keys['group']) ? '' : ' and aui.user_id in (select user_id from  ' . GROUP_TABLE 
					  . ' g inner join ' . USERGROUP_TABLE . ' ug on g.id = ug.group_id where g.group_name =' . quote_smart($keys['group']) . ')'; 
			$where .= empty($keys['user_name']) ? '' : ' and aui.user_id in (select id from  ' . USER_TABLE 
					  . ' where name =' . quote_smart($keys['user_name']) . ')'; 		  
		}
		
		return empty($where) ? $where : ' where 1=1 ' . $where;		
	}
	
	/**
	 * 得到用户IP绑定的表连接
	 * @return 表连接串
	 */
	function get_appuerip_join(){
		return APP_TABLE ." app 
				inner join " . APPUSERIP_TABLE . " aui on app.id = aui.app_id
				inner join " . USER_TABLE . " u on aui.user_id = u.id ";
	}
	
	/**
	 * 禁用用户应用关系
	 * @param $conn 数据库连接
	 * @param $uid 用户ID
	 * @param $app 应用ID
	 * @param $disable 是否要禁用，true 禁用; false 启用
	 * @return 操作结果
	 */
	function up_user_app_flag($conn, $uid = 0, $app = 0 , $disable = true){
		if(empty($uid) && empty($app)) return false; //什么也不做
		$where = '';
		
		//只有一个应用ID的话，就是禁用这个应用下的所有用户
		if(!empty($app)) {
			$where = ' app_id= ' . intval($app);
		}
				
		//只有一个用户ID的话，就禁用这个用户对所有应用的访问
		if(!empty($uid)) {
			$where = $where . (empty($where) ? '' : ' and ') . ' user_id = ' . intval($uid);
		}
		$set = $disable ? ' flag = 1 ' : ' flag = 0 ';
		$sql = ' update ' . APPUSERIP_TABLE . ' set ' . $set . ' where ' . $where;
		return query($conn, $sql);
	}
	
	/**
	 * 禁用或启用IP绑字
	 * @param $conn 数据库连接
	 * @param $uid 用户ID
	 * @param $app 应用ID
	 * @param $disable 是否要禁用，true 禁用; false 启用
	 * @return 操作结果
	 */
	function up_user_app_is_band($conn, $uid = 0, $app = 0 , $disable = true){
		if(empty($uid) || empty($app)) return false; //什么也不做
		$where = '';
		
		$where = ' app_id= ' . intval($app);
		$where = $where . (empty($where) ? '' : ' and ') . ' user_id = ' . intval($uid);
		
		$set = $disable ? ' is_band = 0 ' : ' is_band = 1 ';
		$sql = ' update ' . APPUSERIP_TABLE . ' set ' . $set . ' where ' . $where;
		return query($conn, $sql);
	}
	
	/**
	 * 批量更新一个用户和多个应用之前的关系
	 * @param $conn 数据库连接对象
	 * @param $uid 用户ID
	 * @param $apps APP组
	 */
	function update_user_app_relations($conn, $uid, $apps = array()){
		if(empty($apps)) return false;
		$uid = intval($uid);
		if(empty($uid) ) return false;		
		foreach($apps as $a) {
			$info = array('user_id' => $uid, 'app_id' => intval($a), 'flag' => '0');
			add_or_update_appuserip($conn, $info);
		}
	}
	
	/**
     * 更新一个APP和多个用户之间的关系
     * @param $conn 数据库连接
     * @param $app 应用
     * @param $users 用户ID数组
     */
    function update_app_user_relations($conn, $app, $users = array()){
        if(empty($app) || empty($users)) return false;
        $app = intval($app);        
        //先清掉原来APP和用户间的关系
        up_user_app_flag($conn, 0, $app, true);
        //再重新建立用户和APP间的关系
        //print_r($users);
        foreach($users as $u) {          	 
              $info = array('user_id' => $u['id'], 'app_id' => intval($app), 'flag' => 0);
              add_or_update_appuserip($conn, $info);
        }
    }
?>