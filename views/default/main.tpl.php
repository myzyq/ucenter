<?php defined('U_CENTER') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>用户中心</title>
<meta http-equiv="x-ua-compatible" content="ie=7" />
<?php echo content_type('text/html', CHARSET);?>
<?php echo css_link('admincp.css', THEME_RESOURCE);?>

<meta content="Gyyx" name="Copyright" />
</head>
<body>
<table cellpadding="0" cellspacing="0" width="100%" style="height:100%">
	<tr>
		<td colspan="2" height="69"><iframe src="enter.php?m=frame&a=header" name="header" width="100%" height="69" scrolling="no" frameborder="0"></iframe></td>
	</tr>

	<tr id='bodytr' style="height:100%">
		<td id='bodytd'  valign="top" width="160"><iframe src="enter.php?m=frame&a=menu" name="menu"  width="160" height="100%" scrolling="no" frameborder="0"></iframe></td>
		<td valign="top" ><iframe src="enter.php?m=app&a=index" name="main" width="100%" height="800" frameborder="0" scrolling="yes" style="overflow:visible;"></iframe></td>
	</tr>
</table>
<script type="text/javascript">
 var height = document.body.offsetHeight;
 var bodytr = document.getElementById('bodytr');
 bodytr.style.height = height - 69;
 //document.getElementById('bodytd').style.height = '100%';
 var Sys = {};
 var ua = navigator.userAgent.toLowerCase();
 window.ActiveXObject ? Sys.ie = ua.match(/msie ([\d.]+)/)[1] : 0 ;
 document.getBoxObjectFor ? Sys.firefox = ua.match(/firefox\/([\d.]+)/)[1] : 0 ;
 window.MessageEvent && !document.getBoxObjectFor ? Sys.chrome = (ua.match(/chrome\/([\d.]+)/) ? ua.match(/chrome\/([\d.]+)/)[1] : '') : 0;
 window.opera ? Sys.opera = ua.match(/opera.([\d.]+)/)[1] : 0;
 window.openDatabase ? Sys.safari = ua.match(/version\/([\d.]+)/)[1] : 0;

 if (!Sys.ie){

	  document.getElementById('bodytd').style.height = '100%';

 }

</script>
</body>
</html>
