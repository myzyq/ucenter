<?php defined('U_CENTER') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php echo '<html xmlns="http://www.w3.org/1999/xhtml">';?>
<head>
<?php echo content_type('text/html', CHARSET);?>
<title>用户中心</title>
<?php echo css_link('menu.css', THEME_RESOURCE);?>
<meta content="Gyyx Inc." name="Copyright" />
</head>
<?php echo '<body>';?>

<div class="menu">
	<ul id="leftmenu">
		<li><a href="enter.php?m=app&a=index" target="main" class="tabon">首页</a></li>
		<li><a href="enter.php?m=user&a=editpwd" target="main">修改密码</a></li>
		<?php if(!empty($viewData['user_group']) && !empty($viewData['user_group']['is_system'])) {?>
		<li><a href="enter.php?m=link&a=index" target="main">快速通道</a></li>
		<li><a href="enter.php?m=frame&a=main" target="main">系统信息</a></li>
		<?php }?>
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