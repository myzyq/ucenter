<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 应用管理操作函数库
 * @author d-boy
 * $Id: group.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/

	if(!defined('GROUP_TABLE')) define('GROUP_TABLE',table_name(UCENTER_DBNAME, 'base_group', UCENTER_TABLE_PRE)); //用户组数据表名
		
	/**
	 * 查询用户组数
	 * @param $db 数据库连接对象
	 * @param $keys 条件
	 * @return 符合条件的应用数
	 */
	function count_group($db, $keys = array()) {
		$table = GROUP_TABLE;
		$where = get_group_where($keys) ;
		$data = exec_count($db, $table, $where);
		
		return $data;
	}
	
	/**
	 * 查询用户组信息
	 * @param $conn 数据连接
	 * @param $keys 条件
	 * @param $start 开始记录号
	 * @param $end 结束记录号
	 * @return 信息
	 */
	function search_group($conn, $keys = array(), $start, $end = 0, $order = ' id desc') {
		$table = GROUP_TABLE;
		$where = get_group_where($keys) ;
		if(!empty($order)) $where .= ' order by ' . $order;
		if(!empty($end)) $where .= ' limit ' . intval($start) . ',' . intval($end);
		$sql = "select * from $table $where";
		$data = select($conn, $sql);
		return $data;
	}
	
	/**
	 * 生成where子句
	 * @param $keys 条件
	 * @return where条件子句
	 */
	function get_group_where($keys) {
		if(empty($keys)) return '';
		$where = '' ;
		if(!empty($keys['group_name']) ) $where = ' and group_name like ' . quote_smart("%{$keys['group_name']}%") ;
		return empty($where) ? '' : ' where 1=1 ' . $where;
	}
	
	/**
	 * 按组名查询组信息，返回一个组，全匹配
	 * @param $conn 数数据连接
	 * @param $name 名
	 * @return 组信息
	 */
	function get_group_by_name($conn, $name){
		$name = quote_smart($name);
		$table = GROUP_TABLE;
		$sql = "select * from $table where group_name= $name";
		$data = select($conn, $sql);
		return empty($data) ? array() : $data[0];
	}
	
	/**
	 * 返回一个用户组的信息
	 * @param $conn 数据连接
	 * @param $id 用户组ID
	 * @return 用户组信息
	 */
	function get_one_group($conn, $id) {
		$id = intval($id);
		$table = GROUP_TABLE;
		$sql = "select * from $table where id=$id";
		return get_one($conn, $sql) ;
	}
	
	/**
	 * 将新的用户组插入到数据库中
	 * @param $conn 数据库连接
	 * @param $info 用户组数据
	 * @return 是否成功，成功返回ID
	 */
	function insert_group($conn, $info) {
		$table = GROUP_TABLE;
		$id = insert_data($conn, $table, $info) ;
		return $id;
	}
	
	/**
	 * 检验用户组信息是否正确
	 * @param $conn 数据库连接
	 * @param $info 用户组信息
	 * @return 验证结果信息
	 */
	function check_group_info($conn, $info) {
		if(empty($info)) {
			return '用户信息不完整';
		}
		$name = $info['group_name'];
		if(empty($name)) {
			return '用户组名不能为空';
		}
		$group = get_group_by_name($conn, $name);
		if(!empty($group)) {
			return '组名已经存在！';
		}
		return '';
	}
	
	/**
	 * 删除一个用户组
	 * @param $conn 数据连接
	 * @param $id ID
	 * @return 操作结果
	 */
	function delete_group($conn, $id) {
		$id = intval($id);
		$table = GROUP_TABLE;
		return delete_data($conn, $table, ' id=' . $id); 
	}
	
	/**
	 * 批量删除用户组
	 * @param $conn 数据库连接
	 * @param $ids ID数组
	 * @return 删除成功的个数
	 */
	function batch_delete_group($conn, $ids) {
		$rs = array();
		foreach($ids as $id) {
			$rows = delete_group($conn, $id);
			if(!empty($rows)) {
				$rs[] = $rows;
			}
		}
		return count($rs);
	}
	
	/**
	 * 更新用户组信息
	 * @param $conn 数据库连接
	 * @param $info 数据
	 * @return 更新结果
	 */
	function update_group($conn, $info) {
		if(empty($info) || empty($info['id'])) return 0;
		$id = intval($info['id']) ;
		unset($info['id']);
		
		$apps = array();
		if(isset($info['apps'])) {
			$apps = $info['apps'];
			unset($info['apps']);
		}				
		
		$table = GROUP_TABLE;
		$rs = update_data($conn, $table, $info, ' id=' . $id);
		//echo $id;
		update_group_app($conn, $id, $apps);
		return $rs;
	}
?>