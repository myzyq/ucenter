<?php defined('U_CENTER') or exit('Access Denied'); 
 /*==============================
  * settings inc 所用数据操作函数
  * @author d-boy
  * $Id: settings.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
  ==============================*/

	if(!defined('SETTINGS_TABLE')) define('SETTINGS_TABLE', table_name(UCENTER_DBNAME, 'settings', UCENTER_TABLE_PRE)); //配置 信息表名
	
	/**
	 * settings首页所用显示信息
	 * @return 首页所用显示信息
	 */
	function setting_index_data() {
		$db = conn_db();
		
		$viewData = array();
		$viewData['settings'] = get_settings($db);
		
		close_db($db);
		//print_r($viewData);
		return $viewData;
	}
	
	/**
	 * 更新置数据
	 * @param $info 表单提交过来的信息
	 * @return 页面显示数据
	 */
	function settings_update_data($info) {
		$db = conn_db();
		$viewData = array();
		
		//更新数据
		$rs = update_settings($db, $info);
		
		$viewData['rs']['flag'] = !empty($rs) ; //是否成功
		
		$viewData['rs']['msg'] = empty($rs) ? '更新失败！请重试！' : '更新成功！'; //提示信息
		
		$viewData['settings'] = get_settings($db);
		close_db($db);
		return $viewData;
	}
	
	/**
	 * 返回所有配置信息
	 * @param $db 数据库连接对象
	 * @return 所有配置信息 k => array();
	 */
	function get_settings($db) {
		$table = SETTINGS_TABLE;
		$sql = "select * from $table ";
		$rs = query($db, $sql);
		$data = array();
		if($rs) {
			$data = rs_to_arr($rs, 'k');
			mysql_freeresult($rs);
		}
		return $data;
	}
	
	/**
	 * 更新置数据
	 * @param $db 操作
	 * @param $data 数据
	 * @return 操作结果
	 */
	function update_settings($db, $data = array()) {
		$rs = 0;
		$table = SETTINGS_TABLE;
		$sql = " update $table set %s where %s;" ;
		
		foreach($data as $k=>$v) {
			$set = "v=" . quote_smart($v);
			$where = "k=" . quote_smart($k) ;
			$relsql = sprintf($sql, $set, $where);
			$rs += query($db, $relsql);
		}
		
		return $rs;
	}
	
	/**
	 * 配置列表
	 * @param $conn 数据库连接
	 * @param $keys 条件
	 */
	function list_settings($conn, $keys) {
		$table = SETTINGS_TABLE;
		$sql = "select * from $table " ;
		$sql = $sql  . gen_where($keys);
		$rs = select($conn, $sql);
		return $rs;
	} 
	
	
	/**
	 * 返回一个配置信息
	 * @param $conn 数据库连接
	 * @param $k
	 */
	function get_one_setting($conn, $k ) {
		$data = list_settings($conn, array('k' => $k));
		return empty($data) ? array() : $data[0];
	}
	
	/**
	 * 组织WHERE
	 * @param $keys
	 */
	function gen_where($keys) {
		$where = "";
		if(!empty($keys)){
		  if(!empty($keys["k"])) $where .= " and k like " . quote_smart("%" . $keys["k"] . "%");
		  if(! empty($where)) $where = " where 1=1 $where";  	
		}
		return $where;
	}
	
	/**
	 * 保存或更新信息
	 * @param $conn 数据库连接
	 * @param $info 数据
	 */
	function save_or_update_setting($conn, $info) {
				
		if(empty($info['id'])) {
		  unset($info['id']);
		  insert_data($conn, SETTINGS_TABLE, $info);
		}else{
		  $id = $info['id'];
		  unset($info['id']);
		  update_data($conn, SETTINGS_TABLE, $info, " k=" . quote_smart($id));
		}
	}
	
	/**
	 * 删除一个配置
	 * @param $conn 数据库连接
	 * @param $k KEY
	 */
	function delete_settings($conn, $k){
		$sql = "delete from " . SETTINGS_TABLE . " where k=" . quote_smart($k);
		return query($conn, $sql);		
	}
	
	/**
	 * 批量删除配置
	 * @param $conn
	 * @param $ks
	 */
	function batch_delete_settings($conn, $ks){
		$count = 0;
		foreach($ks as $k){
			$rs = delete_settings($conn, $k);
			if($rs > 0) $count ++;
		}
		return $count;
	}
?>