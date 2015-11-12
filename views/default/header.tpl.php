<?php defined('U_CENTER') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php echo '<html xmlns="http://www.w3.org/1999/xhtml">';?>
<head>
<?php echo content_type('text/html', CHARSET);?>
<title>用户中心</title>
<meta http-equiv="x-ua-compatible" content="ie=7" />	
<?php echo css_link('admincp.css', THEME_RESOURCE);?>
<?php echo css_link('jquery-ui-1.7.2.custom.css', JS_ROOT . 'jquery/ui/css/redmond/');?>
<?php echo script_link('jquery-1.3.2.min.js', JS_ROOT . 'jquery/');?>
<?php echo script_link('jquery-ui-1.7.2.custom.min.js', JS_ROOT . 'jquery/');?>

<meta name="Copyright" content="Gyyx Inc."/>
</head>
<?php echo "<body>" ;?>
<div id="append"></div>

<script type="text/javascript">
	function headermenu(ctrl) {
		ctrl.className = ctrl.className == 'otherson' ? 'othersoff' : 'otherson';
		var menu = document.getElementById('header_menu_body');
		if(!menu) {
			menu = document.createElement('div');
			menu.id = 'header_menu_body';
			menu.innerHTML = '<ul>' + document.getElementById('header_menu_menu').innerHTML + '</ul>';
			var obj = ctrl;
			var x = ctrl.offsetLeft;
			var y = ctrl.offsetTop;
			while((obj = obj.offsetParent) != null) {
				x += obj.offsetLeft;
				y += obj.offsetTop;
			}
			menu.style.left = x + 'px';
			menu.style.top = y + ctrl.offsetHeight + 'px';
			menu.className = 'togglemenu';
			menu.style.display = '';
			document.body.appendChild(menu);
		} else {
			menu.style.display = menu.style.display == 'none' ? '' : 'none';
		}
	}
</script>
