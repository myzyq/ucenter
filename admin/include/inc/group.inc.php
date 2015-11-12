<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 用户信息操作控制器
  * @author d-boy
  * $Id: group.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	
 	function __construct() {
 		parent::__construct();
 		include_once FUN_ROOT . 'group.fun.php';
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){ 	 
 	 	$viewData = array();
 	 	$conn = $_ENV['db'];
 	 	$keys = $viewData['keys'] = empty($_REQUEST['keys']) ? array() : $_REQUEST['keys'];
 	 	$viewData['msg'] = empty($_REQUEST['msg']) ? array() : $_REQUEST['msg'];
 	 	$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']); //页面码
		$pageSize = 50; //每页50条记录
		$total = count_group($conn, $keys); //总数
		
		include_pager(); //引入分页控件
		$myPager = new Pager(array('page' => $page, 'total' => $total, 'pagesize' => $pageSize, 'always' => false)); //分页控初始化
		//返回当前页最小和最大记录号
		$start = $myPager->get_start_no();
		//$end = $myPager->get_end_no();
		
		$viewData['data'] = search_group($conn, $keys, $start, $pageSize); //查询数据
		$viewData['pager'] = $myPager->exec_pager();
 	 	//close_db($conn);
 		parent::view('group_index', $viewData);
 	}
 	
 	/**
 	 * 保存新增用户组信息
 	 */
 	function _save(){
 		$info = $_REQUEST['info'] ;
 		$conn = $_ENV['db'];
 		$tmpl = "msg[flag]=%s&msg[msg]=%s";
 		$chk = check_group_info($conn, $info);
 		$msg = '';
 		if(!empty($chk)) {
 			$msg = sprintf($tmpl, '0', urlencode($chk));
 		}else {
 			$id = insert_group($conn,$info);
 			if(empty($id)) {
 				$msg = sprintf($tmpl, '0', urlencode('数据插入失败'));
 			}else {
 				$msg = sprintf($tmpl, '1', urlencode('用户组保存成功,新用户组ID是' . $id));
 			}
 		}
 		
 		//close_db($conn);
 		
 		header("location:enter.php?m=group&a=index&$msg");
 	}
 	
 	/**
 	 * 批量删除用户组
 	 */
 	function _batch_delete(){
 		$ids = $_REQUEST['ids'] ;
 		$tmpl = "msg[flag]=%s&msg[msg]=%s";
 		$msg = '';
 		if(empty($ids) || !is_array($ids)) {
 			$msg = sprintf($tmpl, '0', urlencode('参数有误，请选择要删除的记录'));
 		}else{ 		
	 		$conn = $_ENV['db'];
	 		$count = count($ids);
	 		$del = batch_delete_group($conn, $ids) ;
	 		$msg = sprintf($tmpl, '1', urlencode('成功' . $del . '个，共' . $count . '个'));
	 		//close_db($conn);
 		}
 		header("location:enter.php?m=group&a=index&$msg");
 	}
 	
 	
 	/**
 	 * 修改用户组信息
 	 */
 	function _edit() {
 		$id = $_REQUEST['id'];
 		$viewData = array();
 		if(empty($id)) {
 			$viewData['msg'] = array('flag' => false, 'msg' => '参数错误，请反回重试');
 			$viewData['data'] = array();
 		}else{
 			$conn = $_ENV['db'];
 			$viewData['data'] = get_one_group($conn, $id);
 			if(empty($viewData['data']) ) $viewData['msg'] = array('flag'=>false, 'msg'=>'找不到用户组') ;
 			
 	
 			include_once FUN_ROOT . 'app.fun.php' ;
 			$viewData['apps'] = search_apps($conn, array(), 0, 0);
 			//print_r($viewData['apps']);

 			//close_db($conn);
 		}
 		
 		parent::view('group_edit', $viewData);
 	}
 	
 	/**
 	 * 更新用户组信息
 	 */
 	function _update() {
 		$info = $_REQUEST['info'];
 		$viewData = array();
 		if(empty($info) || empty($info['id'])) {
 			$viewData['msg'] = array('flag' => false, 'msg' => '参数不正确，请重新操作');
 			$viewData['data'] = array();
 		}else {
 			$conn = $_ENV['db'];
 			
 			include_once FUN_ROOT . 'groupapp.fun.php' ;
 			include_once FUN_ROOT . 'app.fun.php' ;
 			
 			$id = $info['id']; 			
 			
 			$rs = update_group($conn, $info); 			
 			
 			if(empty($rs)) {
 				$viewData['msg'] = array('flag' => false, 'msg' => '更新失败，请重新操作'); 				
 			}else{
 				$viewData['msg'] = array('flag' => true, 'msg' => '更新成功');
 			}
 			$viewData['data'] = get_one_group($conn, $id);
 			$viewData['apps'] = search_apps($conn, array(), 0, 0);
 			
 			//$viewData['groupapp'] = get_group_app($conn, $id);
 			//close_db($conn);
 		}
 		
 		parent::view('group_edit', $viewData);
 	}
 }
 
?>