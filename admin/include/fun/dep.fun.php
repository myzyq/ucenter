<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 部门管理操作函数库
 * @author d-boy
 * $Id: dep.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/

	if(!defined('MEMBER_TABLE')) define('MEMBER_TABLE',table_name(UCENTER_DBNAME, 'member',  UCENTER_TABLE_PRE)); //员工数据表名
	if(!defined('DEP_TABLE')) define('DEP_TABLE',table_name(UCENTER_DBNAME, 'department',  UCENTER_TABLE_PRE)); //部门数据表名	
	
	/**
	 * 查询部门数
	 * @param $db 数据库连接对象
	 * @param $keys 条件
	 * @return 符合条件的部门数
	 */
	function count_depts($db, $keys = array()) {
		$table = get_join();
		$where = get_where($keys);
				
		$data = exec_count($db, $table, $where);
		
		return $data;
	}
	
	/**
	 * 查询部门信息
	 * @param $db 数据库连接对象
	 * @param $keys 查询条件
	 * @param $start 开始记录
	 * @param $end 结束记录
	 * @param $o 排序条件
	 * @return 符合条件的部门信息
	 */
	function search_depts($db, $keys = array(), $start = 0, $end = 0, $o = ' d.id ') {
		$table = get_join();
		$where = get_where($keys);
		if(empty($o)) $o = ' d.id';
		$where .= ' order by ' . $o;
		if(!empty($end)) $where .= ' limit ' . intval($start) . ',' . intval($end);
		$sql = "select d.* , p.name as parent_name, m.real_name as director_name from $table $where"	;	
		$data = select($db, $sql);
		
		return $data;
	}
	
	/**
	 * 返回WHERE子句
	 * @param $keys 条件集合
	 * @return WHERE子句
	 */
	function get_where($keys) {
		if(empty($keys)) return '';
		$where = '';
		$where .= empty($keys['id']) ? '' : ' and d.id=' . intval($keys['id']);
		$where .= empty($keys['name']) ? '' : ' and d.name like ' . quote_smart("%{$keys['name']}%") ;
		$where .= empty($keys['parent']) ? '' : ' and d.parent_id = ' . quote_smart($keys['parent']) ;
		$where .= empty($keys['director']) ? '' : ' and d.director = ' . quote_smart($keys['director']) ;
		return empty($where) ? $where : ' where 1=1 ' . $where;
	}
	
	/**
	 * 返回JOIN子句
	 * @return JOIN子句
	 */
	function get_join() {
		$table = DEP_TABLE;
		$table .= ' d left join ' . $table . ' p on d.parent_id = p.id' ;
		$table .= ' left join ' . MEMBER_TABLE; 
		$table .= ' m on d.director=m.id';
		return $table;
	}
	
	/**
	 * 部门管理首页数据
	 * @return 部门管理首页数据
	 */
	function dept_index_data() {
		$viewData = array();
		$viewData['msg'] = empty($_REQUEST['msg']) ? array() : $_REQUEST['msg'] ; //提示信息
		$keys = empty($_REQUEST['keys']) ? array() : $_REQUEST['keys'];
		$viewData['keys'] = $keys;
		$pageSize = empty($_REQUEST['pagesize']) ? 50 : intval($_REQUEST['pagesize']); 
		$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);
		
		$conn = $_ENV['db']; //打开数据库连接
		
		$total = count_depts($conn, $keys); //总数
		
		include_pager(); //引入分页控件
		$myPager = new Pager(array('page' => $page, 'total' => $total, 'pagesize' => $pageSize, 'always' => false)); //分页控初始化
		//返回当前页最小和最大记录号
		$start = $myPager->get_start_no();
		$end = $myPager->get_end_no();
		
		$viewData['data'] = search_depts($conn, $viewData['keys'], $start, $end); //查询数据
		$viewData['pager'] = $myPager->exec_pager(); //分页条代码
		
		//close_db($conn); //关数据库连接
		return $viewData;
	}
	
	/**
	 * 保存部门信息
	 * @param $conn 数据库连接
	 * @param $info 数据
	 */
	function save_dept($conn, $info) {
		$table = DEP_TABLE;
		$rs = insert_data($conn, $table,$info);		
		$msg = array();
		if(empty($rs)) {
			$msg['flag'] = false;
			$msg['msg'] = '添加失败，请重试！';
		} else {
			$msg['flag'] = true;
			$msg['msg'] = '操作已成功！'; 
		}
		//$msg = "msg[flag]=$flag&msg[msg]=$msg";
		return $msg;
	}
	
	/**
	 * 返回一个部门信息
	 * @param $conn 数据库连接
	 * @param $id ID
	 * @return 一个部门信息
	 */
	function get_one_dept($conn, $id) {
		$table = get_join();
		$id = intval($id);
		$sql = "select d.* , p.name as parent_name, m.real_name as director_name from $table where d.id = $id";
		$data = get_one($conn, $sql);
		return $data;
	}
	/**
	 * 更新编辑的数据
	 * @param $conn 数据库连接
	 * @param $info 数据
	 * @return 更新结果
	 */
	function update_dept_data($conn, $info) {
		$flag = FALSE;
		$msg = '';
		if(!empty($info['id'])) {
		 $where = " id = {$info['id']}" ;
		 unset($info['id']);	
		 $rs = update_data($conn, DEP_TABLE, $info, $where) ;
		 if(empty($rs)) {
		 	$flag = FALSE;
		 	$msg = '操作失败，请重试';
		 }else {
		 	$flag = TRUE;
		 	$msg = '操作已成功！' ;
		 }		 
		}else{
			$flag = FALSE;
		 	$msg = '参数不正确，请重试';
		}
		
		return array('flag' => $flag, 'msg' => $msg);
	}
	
	/**
	 * 删除部门信息
	 * @param $conn 数据库连接
 	 * @param $info 数据
	 * @return 操作结果
	 */
	function delete_dept($conn, $id) {
		$id = intval($id);
		$where = " id = $id";
		$table = DEP_TABLE;
		return delete_data($conn, $table, $where);		
	}
	
	/**
	 * 批量删除部门
	 * @param $conn 数据库连接
	 * @param $ids 要删除的数据的ID
	 * @return 操作结果
	 */
	function batch_delete_dept($conn,$ids = array()) {
		$rows = 0;
		
		foreach($ids as $id) {
			
			$rows += delete_dept($conn, $id);
		}
		return $rows;
	}
?>