<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 应用管理操作函数库
 * @author d-boy
 * $Id: app.fun.php 116 2010-04-12 01:44:06Z lunzhijun $
 ========================*/

	if(!defined('LINK_TABLE')) define('LINK_TABLE',table_name(UCENTER_DBNAME, 'link',  UCENTER_TABLE_PRE)); //应用数据表名
	
	/**
	 * 查询Link数
	 * @param $db 数据库连接对象
	 * @param $keys 条件
	 * @return 符合条件的应用数
	 */
	function count_links($db, $keys = array()) {
		$table = LINK_TABLE;
		$where = get_apps_where($keys);
		$data = exec_count($db, $table, $keys);
		
		return $data;
	}
	
	/**
	 * 查询Link信息
	 * @param $conn 数据库连接
	 * @param $keys 条件
	 * @param $start 开始
	 * @param $end 结束
	 * @param $order 排序条件
	 * @return 符合条件的数据
	 */
	function search_links($conn, $keys=array(), $start, $end, $order=' id desc') {
		$table = LINK_TABLE;
		$where = get_links_where($keys);
		if(!empty($order)) $where .= ' order by ' . $order;
		if(!empty($end)) $where .= ' limit ' . intval($start) . ',' . intval($end);
		$sql = "select * from $table $where";

		return select($conn, $sql);
	}
	
	/**
	 * 返回WHERE子句
	 * @param $keys 条件
	 * @return WHERE子句
	 */
	function get_links_where($keys) {		
		$where = ' where 1 = 1 ' ;
		if(!empty($keys)){
			if(!empty($keys['id'])) $where .= ' and id=' . intval($keys['id']);
			if(!empty($keys['title'])) $where .= ' and title like ' . quote_smart('%'.$keys['title'].'%');
			if(!empty($keys['category'])) $where .= ' and category like ' . quote_smart('%' . $keys['category'] . '%');
			if(isset($keys['url'])) $where .= ' and url like ' . quote_smart('%' . $keys['url'] . '%');
		}
		return $where;
	}
	
	/**
	 * 查询一个LINK信息
	 * @param $conn 数据连接
	 * @param $id ID
	 * @return 一个LINK信息
	 */
	function get_one_link($conn, $id) {
		$data = search_links($conn, array('id'=> intval($id)), 0, 0);
		return empty($data) ? array() : $data[0];
	}
	
	/**
	 * 插入新的LINK数据
	 * @param $conn 数据库连接
	 * @param $info 应用信息
	 * @return 成功返回ID
	 */
	function save_link($conn, $info = array()) {
		$table = LINK_TABLE;
		return insert_data($conn, $table, $info);
	}
	
	/**
	 * 更新应用信息
	 * @param $conn 数据库连接
	 * @param $info 信息
	 * @return 成功返回大于0的数
	 */
	function update_link($conn, $info) {		
		$id = intval($info['id']);
		unset($info['id']);
		$table = LINK_TABLE;
		$where = ' id=' . $id;
		return update_data($conn, $table, $info, $where);
	}
	
	
	/**
	 * 检查LINK信息是否合法
	 * @param $info
	 * @return 操作结果
	 */
	function check_link($info) {
		if(empty($info)) {
			return '没有任何信息可保存';
		}
		if(empty($info['title'])) {
			return '应用名不能为空' ;
		}
	
		if(empty($info['url']) || !preg_match("/^(http:\/\/).*/", $info['url'])) {
			return 'LINK URL 格式不正确';
		}
		return '';
	}
	
	/**
	 * 删除一个LINK信息
	 * @param $conn 数据连接
	 * @param $id ID
	 * @return 成功返回大于0的数
	 */
	function delete_link($conn, $id) {
		$table = LINK_TABLE;
		$where = ' id=' . intval($id);
		$rs = delete_data($conn, $table, $where);
		return $rs;
	}
	
	/**
	 * 批量删除LINK信息
	 * @param $conn 数据库连接
	 * @param $ids ID数组
	 * @return array() 操作结果
	 */
	function batch_delete_link($conn, $ids) {
		$result = array();
		if(empty($ids) || !is_array($ids)) {
			$result['falg'] = '0';
			$result['msg'] = '删除失败,参数不正确';
		}else {
			$ok = 0;
			$count = count($ids);
			foreach($ids as $id) {
				if(delete_link($conn, $id) > 0) $ok++;
			}
			$result['flag'] = '1';
			$result['msg'] = '成功删除' . $ok . '个，共' . $count . '个';
		}
		
		return $result;
	}
	
	/**
	 * 返回所有的LINK
	 */
	function all_links($conn){
	   $data = search_links($conn, array(), 0 ,0);
	   return $data;
	}
	
	/**
	 * 返回所有的LINK
	 */
	function links_group_by_category($conn){
	   $data = all_links($conn);
	   $result = array();
	   $categorys = array();
	   
	   if(!empty($data)) {
	   	   foreach($data as $item){
	   	   	   $cat = $item["category"];
	   	   	   if (!in_array($cat, $categorys)){
	   	   	   	   $categorys[] = $cat;
	   	   	   	   $result[$cat] = array();
	   	   	   }
	   	   	   $result[$cat][] = $item;
	   	   }
	   }
	   return $result;
	}
	
?>