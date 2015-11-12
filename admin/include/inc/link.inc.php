<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 用户信息操作控制器
  * @author d-boy
  * $Id: link.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	var $templ = 'msg[flag]=%s&msg[msg]=%s'; 
 	function __construct() {
 		parent::__construct();
 		include_once FUN_ROOT . 'link.fun.php';
 		
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){ 	 
 	 	$viewData = array();
 	 	$conn = $_ENV['db'];
 	 	$keys = $viewData['keys'] = empty($_REQUEST['keys']) ? array() : $_REQUEST['keys']; //查询条件变量		
		$viewData['msg'] = empty($_REQUEST['msg']) ? array() : $_REQUEST['msg'] ; //提示信息
		$viewData['keys'] = $keys;		
		$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']); //页面码
		$pageSize = 50; //每页50条记录
		$total = count_links($conn, $keys); //总数
		
		include_pager(); //引入分页控件
		$myPager = new Pager(array('page' => $page, 'total' => $total, 'pagesize' => $pageSize, 'always' => false)); //分页控初始化
		//返回当前页最小和最大记录号
		$start = $myPager->get_start_no();
		//$end = $myPager->get_end_no();
	
		$viewData['data'] = search_links($conn, $viewData['keys'], $start, $pageSize); //查询数据
		$viewData['pager'] = $myPager->exec_pager(); //分页条代码
 	 	//close_db($conn);
 		parent::view('link_index', $viewData);
 	}
 	
 	/**
 	 * 编辑或添加应用
 	 */
 	function _edit() {
 		$viewData = array();
 		$id = empty($_REQUEST['id']) ? 0 : $_REQUEST['id'] ;
 		$conn = $_ENV['db'];
 		$viewData['msg'] = empty($_REQUEST['msg']) ? array() :$_REQUEST['msg'];
 		$viewData['data'] = array();
 		if(empty($id)) {
 			//添加应用
 			$viewData['msg'] = array('flag' => true, 'msg' => '添加新应用') ; 					
 		}else { 			
 			$data = get_one_link($conn, $id);
 			
 			if(empty($data)) {
 				$viewData['msg'] = array('flag' => false, 'msg' => '没有找到id=' . $id . '的应用，想添加新应用请添加下面的表单后提交' ) ;
 				
 			}else {
 				$viewData['data'] = $data; 				
 			} 			
 		}
 		//close_db($conn);
 		parent::view('link_edit', $viewData);
 	}
 	
 	/**
 	 * 保存或更新应用信息
 	 */
 	function _save() {
 		$info = $_REQUEST['info'] ;
 		$templ = 'msg[flag]=%s&msg[msg]=%s'; 
 		$msg = "";
 		if(empty($info)) {
 			$msg = sprintf($templ, 0, "数据有误，请重试");
 			//$viewData['msg'] = array('flag' => false, 'msg' => '数据有误，请重试');
 		}else{
 			$chk = check_link($info);
 			if(!empty($chk)) {
 				$msg = sprintf($templ, 0, $chk);
 				//$viewData['msg'] = array('flag' => false, 'msg' => $chk);
 			}else{ 			
	 			//print_r($info['groups']);
	 			
	 			//验证通过
	 			$conn = $_ENV['db'];
	 			if(empty($info['id'])) {
	 				//添加信息 				
	 				save_link($conn, $info); 
	 					
	 			}else {
	 				//更新
	 				$id = $info['id'];
	 				$viewData['msg'] = update_link($conn, $info);
	 				
	 			}
	 			
	 			$msg = sprintf($templ, 1, "操作已成功");
 			}

 		}
 		$url = "";
	 	if ($_SERVER['HTTP_REFERER']){
	        $url = $_SERVER['HTTP_REFERER'];
		}
		if(empty($url)) {
			$url = "enter.php?m=link&a=index";
		}
		
	    $url .= strpos($url, "?") > 0 ? "&" : "?";
	    $url .= $msg;
		//echo $url;
		
 		header("Location:$url");
 	}
 	

 	
 	/**
 	 * 批量删除应用
 	 */
 	function _batch_delete() {
 		$ids = $_REQUEST['ids'];
 		$conn = $_ENV['db'];
 		$result = batch_delete_link($conn, $ids);
 		//close_db($conn);
 		$msg = "msg[flag]={$result['flag']}&msg[msg]=" . urlencode($result['msg']);
 		header("location:enter.php?m=link&a=index&$msg");
 	}

 	
 }
 
?>