<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 员工管理操作函数库
 * @author d-boy
 * $Id: member.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/

if(!defined('MEMBER_TABLE')) define('MEMBER_TABLE',table_name(UCENTER_DBNAME, 'member',  UCENTER_TABLE_PRE)); //员工数据表名
if(!defined('DEP_TABLE')) define('DEP_TABLE',table_name(UCENTER_DBNAME, 'department',  UCENTER_TABLE_PRE)); //部门数据表名	
	
	/**
	 * 查询员工数
	 * @param $db 数据库连接对象
	 * @param $keys 条件
	 * @return 符合条件的员工数
	 */
	function count_member($db, $keys = array()) {
		$table = MEMBER_TABLE;
		$table = $table . ' m left join ' . DEP_TABLE . ' d on m.dep_id = d.id ';
		$where = get_where($keys); 
		$data = exec_count($db, $table, $where);
		
		return $data;
	}
	
	/**
	 * 查询员工信息
	 * @param $db 数据库连接对象
	 * @param $keys 查询条件
	 * @param $start 开始记录号
	 * @param $end 结束记录号 如果结数为0 就是查全部的数据
	 * @param $order 排序条件
	 * @return 符合条件的数据
	 */
	function select_member($db, $keys = array(), $start = 0, $end = 0, $order = ' m.id desc') {
		$table = MEMBER_TABLE;
		$table = $table . ' m left join ' . DEP_TABLE . ' d on m.dep_id = d.id ';
		$where = get_where($keys); 
		//echo $where;
		$limit = empty($end) ? "" : " limit {$start},{$end} ";
		$o = empty($order) ? ' order by id desc' : ' order by ' . $order;
		$where = $where . $o . $limit;
		
		$sql = "select m.* , d.name as dep_name from $table $where";
		$rs = query($db, $sql) ;
		$data = array();
		if($rs) {
			$data = rs_to_arr($rs);
			mysql_free_result($rs) ;
		}
		
		return $data;
	}
	
	/**
	 * 查询用户信息action 所用数据
	 * @param $isAjax 是否以ajax形式获取数据
	 * @param $conn 数据库连接
	 * @return 页面所需的数据
	 */
	function search_member_data($isAjax = false , $conn) {		
		$keys = empty($_REQUEST['keys']) ? array() : $_REQUEST['keys'] ; //查询条件
 		$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']); //当前页码
 		$pageSize = empty($_REQUEST['pagesize']) ? 50 : intval($_REQUEST['pagesize']); //页面大小
 		$order = empty($_REQUEST['order']) ? '' : ' order by ' . $_REQUEST['order']; //排序条件
 		$callback = empty($_REQUEST['callback']) ? '' :  $_REQUEST['callback']; //ajax回调函数
 		$dataurl = empty($_REQUEST['dataurl']) ? '' :  $_REQUEST['dataurl']; //ajax数据服务url
 		
 		$viewData = array();
 		$viewData['callback'] = empty($_REQUEST['callback']) ? 'selected_mem' : $_REQUEST['callback'];
 		
 		
 		$total = count_member($conn, $keys) ; //查询数据总数
 		
 		include_pager(); //引入分页类
 		$pager = new Pager(array(
 						'page' => $page,
 						'pagesize' => $pageSize,
 						'total' => $total,
 						'always' => false,
 						'callback' => $callback,
 						'dataurl' => $dataurl,
 						)); //实例化分页控件
 		
 		$start = $pager->get_start_no(); //开始记录
 		//$end = $pager->get_end_no(); //结束记录
 		
 		$viewData['data'] = select_member($conn, $keys, $start, $pageSize, $order) ;
 		//print_r($viewData);
 	    if($isAjax) {
 	    	$viewData['pager'] = $pager->exec_ajax_pager();
 	    	$viewData['script'] = $pager->get_script();
 	    }else {
 	    	$viewData['pager'] = $pager->exec_pager();
 	    }
 		$viewData['keys'] = $keys;
 	
 		return $viewData;
	}
	
	/**
	 * 生成查询where子句
	 * @param $keys 
	 * @return where子句
	 */
	function get_where($keys = array()) {
		
		$where = ' where flag = 0 ';
		if(!empty($keys)) {
			$where .= empty($keys['real_name']) ? '' : ' and real_name like ' . quote_smart("%{$keys['real_name']}%");
			$where .= empty($keys['mobile']) ? '' : ' and mobile like ' . quote_smart("%{$keys['mobile']}%");
			$where .= empty($keys['tel']) ? '' : ' and tel like ' . quote_smart("%{$keys['tel']}%");
			$where .= !isset($keys['id']) || $keys['id'] == '' ? '' : ' and m.id = ' . quote_smart($keys['id']);
		}
		return $where;
	}
	

	/**
	 * 保存员工数据
	 * @param $db 数据库连接对象
	 * @param $member 员工信息
	 * @return 员工ID
	 */
	function save_member_data($db, $member) {
		$table = MEMBER_TABLE;
		$id = insert_data($db, $table, $member);		
		return $id;
	}
	
	/**
	 * 返回一个员工信息
	 * @param $conn 数据库连接
	 * @param $id 员工ID
	 * @return 这个员工的信息
	 */
	function get_one_member($conn, $id) {
		$id = intval($id);
		$data = select_member($conn, array('id'=>$id),0,0);
		return empty($data) ? array() : $data[0];
	} 
	
	/**
	 * 更新员工信息
	 * @param $conn 数据库连接
	 * @param $info 元素
	 * @return 是否成功
	 */
	function update_member($conn, $info) {
		if(empty($info['id'])) return false;
		$id = $info['id'] ;
		unset($info['id']);
		$set = compile_query($info,',');
		$table = MEMBER_TABLE;
		$where = ' id =' . $id;
		return update_data($conn,$table,$info,$where);
	}
	
	/**
	 * 按姓名查询信息，全匹配
	 * @param $conn 数据库连接
	 * @param $name 姓名
	 * @return 一个员工信息
	 */
	function get_member_byname($conn, $name) {
		$name = quote_smart($name);
		$table = MEMBER_TABLE;
		$sql = "select * from $table where real_name=$name and flag=0";
		$rs = query($conn, $sql);
		$row = mysql_fetch_array($rs);
		mysql_free_result($rs);
		return $row;
	}
	
	/**
	 * 批量删除员工信息
	 * @param $conn 数据库连接
	 * @param $ids 要删除的用户ID数组
	 * @return 是否成功！
	 */
	function batch_delete_member($conn, $ids) {
		$rs = array();
		foreach($ids as $id) {
			$rs[] = delete_member($conn,$id);
		}
		return $rs;
	}
	
	/**
	 * 删除一个员工信息
	 * @param $conn 数据库连接
	 * @param $id ID
	 * @return 是否成功！
	 */
	function delete_member($conn, $id) {
		$id = intval($id);
		$table = MEMBER_TABLE;
		$where = " id=$id";
		return update_data($conn, $table, array('flag' => 1), $where);
	}
?>