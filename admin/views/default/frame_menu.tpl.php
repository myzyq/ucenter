<?php defined('U_CENTER') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php echo '<html xmlns="http://www.w3.org/1999/xhtml">';?>
<head>
<?php echo content_type('text/html', CHARSET);?>
<title>UCenter Administrator's Control Panel</title>
<?php echo css_link('menu.css', THEME_RESOURCE);?>
<meta content="Gyyx Inc." name="Copyright" />
</head>
<?php echo '<body>';?>
<div class="menu">
	<ul id="leftmenu">
		<li><a href="enter.php?m=frame&a=main" target="main" class="tabon">首页</a></li>
		<li><a href="enter.php?m=settings&a=index" target="main">基本设置</a></li>
		<li><a href="enter.php?m=admin&a=index" target="main">管理员</a></li>
		<li><a href="enter.php?m=app&a=index" target="main">应用管理</a></li>
		<li><a href="enter.php?m=user&a=index" target="main">用户管理</a></li>
		<li><a href="enter.php?m=group&a=index" target="main">用户组管理</a></li>
		<!--  <li><a href="enter.php?m=member&a=index" target="main">员工管理</a></li>-->
		<li><a href="enter.php?m=ip&a=index" target="main">IP组管理</a></li>
		<li><a href="enter.php?m=link&a=index" target="main">LINK管理</a></li>
		
	</ul>
</div>
<div class="footer">Powered by Gyyx <br />&copy; 2010 - 2100 <a href="http://www.gyyx.cn/" target="_blank">Gyyx</a> Inc.</div>
<script type="text/javascript">
	function cleartabon() {
	/*	if(lastmenu) {
			lastmenu.className = '';
		}*/
		for(var i = 0; i < menus.length; i++) {
			var menu = menus[i];
			menu.className = '';
		}
	}
	
	var menus = document.getElementById('leftmenu').getElementsByTagName('a');
	//var lastmenu = '';
	for(var i = 0; i < menus.length; i++) {
		var menu = menus[i];
		menu.onclick = function() {
			//setTimeout('cleartabon()', 1);
			cleartabon();
			this.className = 'tabon';
			this.blur();
		};
	}

	//cleartabon();
</script>

<?php view('footer');?>