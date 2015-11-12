<?php
/**
 * 分页类
 * @author d-boy
 * $Id: pager.class.php 230 2010-12-10 03:22:21Z lunzhijun $
 */
class Pager {
	var $page ; //当前页码
	var $total; //总记录数
	var $pageSize; //每页多少记录
	var $pageCount; //总页数
	var $always; //是否总显示
	var $callback ; //ajax回调函数
	var $dataurl ; //ajax分页数据服务URL

	
	function __construct($config = array()) {
		$this->page = empty($config['page']) ? 1 : intval($config['page']);
		$this->total = empty($config['total']) ? 0 : intval($config['total']);
		$this->pageSize = empty($config['pagesize']) ? 1 : intval($config['pagesize']) ;
		$this->always = empty($config['always']) ? false : intval($config['always']) ;
		$this->callback = empty($config['callback']) ? 'callback' : $config['callback'] ;
		$this->dataurl = empty($config['dataurl']) ? '' : $config['dataurl'] ;
		//记算总页数
		
		$this->pageCount = empty($this->pageSize) ? 0 :  ceil($this->total/($this->pageSize * 1.0));  
	}
	
	/**
	 * 返回开始记录号
	 * @return 本页开始记录号
	 */
	function get_start_no() {
		return ($this->page - 1) * $this->pageSize; 
	}
	
	/**
	 * 返回结束记录号
	 * @return 本页结束记录号
	 */
	function get_end_no() {
		return $this->page * $this->pageSize  ;
	}
	
	/**
	 * 返回总页数
	 * @return 总页数
	 */
	function get_total_pages() {
		return $this->pageCount;
	}
	
	/**
	 * 返回分页代码
	 * @return 分页代码
	 */
	function exec_pager() {
		$url = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
		
		if( (empty($this->total) || empty($url)) && !$this->always) return false; //没有总数或总数为0且不总输出，不输出
		
		if($url) $url = preg_replace("/([&]*page=[0-9]*)/i", "", $url);
		
		//echo $url;
		$s = strpos($url, '?') === FALSE ? '?' : '&';
		
		$page = min($this->pageCount,$this->page);
		$prepg = $page-1;
		$nextpg = $page == $this->pageCount ? 0 : ($page+1);
		if($this->total < 1) return FALSE;
		$pagenav = "总数：<b>{$this->total}</b>&nbsp;";
		$pagenav .= $prepg ? "&nbsp;&nbsp;<a href='$url{$s}page=1'>第一页</a>&nbsp;&nbsp;<a href='$url{$s}page=$prepg'>上一页</a>" : "&nbsp;&nbsp;<a href=\"#\">第一页</a>&nbsp;&nbsp;<a href=\"#\">上一页</a>";
		$pagenav .= $nextpg ? "&nbsp;&nbsp;<a href='$url{$s}page=$nextpg'>下一页</a>&nbsp;&nbsp;<a href='$url{$s}page={$this->pageCount}'>尾页</a>" : "&nbsp;&nbsp;<a href=\"#\">下一页</a>&nbsp;&nbsp;<a href=\"#\">尾页</a>&nbsp;";
		$pagenav .= "页码：<b><font color=red>$page</font>/{$this->pageCount}</b>&nbsp;&nbsp;<input type='text' name='page' id='page' class='page' size='2' onKeyDown=\"if(event.keyCode==13) 
					{window.location='{$url}{$s}page='+this.value; return false;}\"> 
					&nbsp;&nbsp;<input type='button' value='GO' class='gotopage' style='width:30px' 
					onclick=\"window.location='{$url}{$s}page='+document.getElementById('page').value\">\n";
		return $pagenav;	
	}
	
	/**
	 * 返回ajax 分页代码  要引入jquery才能用这个功能！
	 * @return ajax 分页代码
	 */
	function exec_ajax_pager() {
	
		if( empty($this->total)  && !$this->always) return false; //没有总数或总数为0且不总输出，不输出
				
		$page = min($this->pageCount,$this->page);
		$prepg = $page-1;
		$nextpg = $page == $this->pageCount ? 0 : ($page+1);
		if($this->total < 1) return FALSE;
		$pagenav = "总数：<b>{$this->total}</b>&nbsp;";
		$pagenav .= $prepg ? "<a href='#' onclick='ajaxPager(1);'>第一页</a><a href='#' onclick='ajaxPager($prepg)'>上一页</a>" : "<a href=\"#\">第一页</a><a href=\"#\">上一页</a>";
		$pagenav .= $nextpg ? "<a href='#' onclick='ajaxPager($nextpg);'>下一页</a><a href='#' onlick='ajaxPager({$this->pageCount});'>尾页</a>" : "<a href=\"#\">下一页</a><a href=\"#\">尾页</a>&nbsp;";
		$pagenav .= "页码：<b><font color=red>$page</font>/{$this->pageCount}</b>&nbsp;&nbsp;<input type='text' name='page' id='page' class='page' size='2' onKeyDown=\"if(event.keyCode==13) 
					{ajaxPager(this.lvalue); return false;}\"> 
					<input type='button' value='GO' class='gotopage' style='width:30px' 
					onclick=\"return ajaxPager(document.getElementById('page').value);\">\n";
		return $pagenav;	
	}
	
	/**
	 * 返回js角本代码
	 * @return js角本代码
	 */
	function get_script() {
		$url = sprintf($this->dataurl, $this->callback);
		$script = " 
			<script type='text/javascript'>
				function ajaxPager(page) {
					var size = {$this->pageSize};
					var url = '$url';			
					$.getScript(url);
					return false;
				}
			</script>
		";
		
		return $script;
	}
} 
?>