<?php defined('U_CENTER') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo content_type('text/html', CHARSET);?>
<title>UCenter Administrator's Control Panel</title>
<?php echo css_link('admincp.css', THEME_RESOURCE);?>
<meta content="Gyyx Inc." name="Copyright" />
</head>
<body>
<div class="mainhd">
	<div class="logo">UCenter Administrator's Control Panel</div>
	<div class="uinfo">
		<p>你好, <em><?php echo $viewData['userName'];?></em> [ <a href="enter.php?m=login&a=logout" target="_top">退出</a> ]</p>
			<!-- <p id="others"><a href="#" class="othersoff" ></a></p> -->
			
	</div>
</div>
</body>
</html>