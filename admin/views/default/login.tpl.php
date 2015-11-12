<?php defined('U_CENTER') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta http-equiv="x-ua-compatible" content="ie=7" />
<title>UCenter Administrator's Control Panel</title>
<?php echo css_link('admincp.css', THEME_RESOURCE);?>
<?php echo script_link('jquery-1.3.2.min.js', JS_ROOT . 'jquery/');?>
<meta content="Gyyx Inc." name="Copyright" />
</head>
<body><div id="append"></div>

<div class="container">
	<form action="enter.php?m=login&a=login" method="post" id="loginform" >
		<table class="mainbox">
			<tr>
				<td class="loginbox">
					<h1>UCenter</h1>
					<p>UCenter 是一个能沟通多个应用的桥梁，使各应用共享一个用户数据库，实现统一登录，注册，用户管理。</p>

				</td>
				<td class="login">
					<?php if(!empty($viewData['msg'])) {
						?>
					<div class="errormsg loginmsg"><p><?php echo $viewData['msg']?></p></div>		
					<?php }?>							
					<p id="usernamediv">用户名:<input type="text" name="username" class="txt" tabindex="1" id="username" value="<?php echo empty($viewData['username']) ? '' : $viewData['username'];?>" /></p>
					<p>密　码:<input type="password" name="password" class="txt" tabindex="2" id="password" value="" /></p>
					<p class="loginbtn"><input type="submit" name="submit" value="登 录" class="btn" tabindex="3" /></p>
				</td>
			</tr>
		</table>

	</form>
</div>
<script type="text/javascript">
	$('#username').focus();
</script>
<div class="footer">Powered by Gyyx  &copy; 2010 - 2100 <a href="http://www.gyyx.cn" target="_blank">Gyyx</a> Inc.</div>

</body>
</html>
