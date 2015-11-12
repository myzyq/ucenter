<?php
 defined('U_CENTER') or exit('Access Denied');
 
 /**
  * 基本设置控制器
  * @author d-boy
  * $Id: settings.inc.php 230 2010-12-10 03:22:21Z lunzhijun $
  */
 class Inc extends BaseInc {
 	var $templ = "msg[flag]=%s&msg[msg]=%s";
 	function __construct() {
 		parent::__construct();
 		$this->include_fun_file();
 	}
 	
 	/**
 	 * 首页
 	 * @see include/inc/BaseInc#_index()
 	 */
 	 function _index(){
 	 	
 	 	$viewData = setting_index_data();
 		parent::view('settings_index', $viewData);
 	}
 	
 	/**
 	 * 更新配置信息
 	 */
 	function _update() {
 	
 		$info = $_POST['info'];

 		$viewData = settings_update_data($info) ;
 		
 		//print_r($viewData);
 		parent::view('settings_index', $viewData);
 	}
 	
 	/**
 	 * 引入函数文件
 	 */
 	function include_fun_file() {
 		include FUN_ROOT . 'settings.fun.php';
 	}
 	
 	/**
 	 * 列表
 	 */
 	function _list(){
 		$viewData = array();
 		$keys = $viewData['keys'] = empty($_REQUEST['keys']) ? array() : $_REQUEST['keys']; //查询条件变量
 		$conn = $_ENV['db'];     
 		
 		$viewData['msg'] = empty($_REQUEST['msg']) ? array() : $_REQUEST['msg'];
 		$viewData["data"] = list_settings($conn ,$keys);
 		parent::view("settings_list", $viewData);
 	}
 	
 	/**
 	 * 修改一个配置
 	 */
 	function _edit(){
 		$k = empty($_REQUEST["id"]) ? "" : $_REQUEST["id"];
 		$viewData = array(); 		
 		if(empty($k)) {
 		    $viewData['msg'] = array('flag' => false , 'msg' => '没有找到你要改的配置');
 		}else{
 			$conn = $_ENV['db'];
 			$setting = get_one_setting($conn, $k);
 			if(empty($setting)){
 			    $viewData['msg'] = array('flag' => false , 'msg' => '没有找到你要改的配置');
 			}else{
 				$viewData['setting'] = $setting; 
 			} 			
 		}
 		parent::view("settings_edit", $viewData);
 	}
 	
 	/**
 	 * 保存一个设置信息
 	 */
 	function _save(){
 		$info = empty($_REQUEST['info']) ? array() : $_REQUEST['info'];
 		$msg = "";
 		if(empty($info)) {
 			$msg = sprintf($this->templ, 0, urlencode("参数错误"));
 		}else{
 			save_or_update_setting($_ENV['db'], $info);
 			$msg = sprintf($this->templ, 1, urlencode("操作成功"));
 		}
 		$url = "enter.php?m=settings&a=list&$msg";
 		header("Location:$url");
 	}
 	
 	/**
 	 * 批量删除
 	 */
 	function _batch_delete(){
 		$msg = '';
        if(empty($_REQUEST['ids'])){
            $msg = 'msg[flag]=false&msg[msg]=请选择要删除的配置' ;
        }else{
            $ids = $_REQUEST['ids'];
            
            $count = count($ids);
            $conn = $_ENV['db'];
            $rs = batch_delete_settings($conn, $ids);
            $msg = 'msg[flag]=true&msg[msg]=' . urlencode("成功{$rs}个,共{$count}个");           
        }
        
        header("location:enter.php?m=settings&a=list&$msg");
 	}
 	
 	/**
 	 * 添加配置
 	 */
 	function _add(){
 		$viewData = array('data' => array());
 		parent::view("settings_edit", $viewData);
 	}
 }
 
?>