<?php 
	include_once 'header.php';
	global $api;
	$forward = empty($_REQUEST['forward']) ? '' : $_REQUEST['forward'];
	echo "<p>&nbsp;</p>";
	if(! empty($_REQUEST['dopost'])) { //提交过来的
		
		$userName = $_REQUEST['username'];
		$password = $_REQUEST['password'] ;
		$expir = $_REQUEST['expir'] ;
		$data = $api->login($forward, $userName, $password, $expir);
		
		//print_r($data);
		if($api->is_ok($data)) {
			echo '<script type="text/javascript" src="' . CLIENT_WEB . 'js/jquery/jquery-1.3.2.min.js"></script>';
			echo $api->gen_login_sync($data);
			echo '<script type="text/javascript" >
						$().ready(function(){
							//alert("登录成功");
							//location.href="' . $api->get_forward($data) . '";
						});
			      </script>';
		}else {
			
			//echo '<script type="text/javascript">alert("' . $api->get_msg($data) .'");history.back();</script>"';
		}
		exit;
	}
?>
<!doctype html public "-//w3c//dtd html 4.01 transitional//en" "http://www.w3c.org/tr/1999/rec-html401-19991224/loose.dtd">
<html>
<head>
<title>用户登录</title>
<link href="themes/default/images/default.css" type=text/css rel=stylesheet />
<link href="themes/default/images/xtree.css" type=text/css rel=stylesheet />
<link href="themes/default/images/user_login.css" type=text/css rel=stylesheet />
<meta http-equiv=content-type content="text/html; charset=gb2312" />
<meta content="mshtml 6.00.6000.16674" name=generator />
</head>
<body id=userlogin_body>
<div></div>
<form action="?" method="post" >
<input type="hidden" name='forword' value="<?php echo empty($_REQUEST['forward']) ? '' : $_REQUEST['forward'];?>" />
<input type="hidden" name='dopost' value='post' />
<div id=user_login>
<dl>
  <dd id=user_top>
  <ul>
    <li class=user_top_l></li>
    <li class=user_top_c></li>
    <li class=user_top_r></li></ul>
  </dd>  
  <dd id=user_main>
  <ul>
    <li class=user_main_l></li>
    <li class=user_main_c>
    <div class=user_main_box>
    <ul>
      <li class=user_main_text>用户名： </li>
      <li class=user_main_input><input class=txtusernamecssclass id=txtusername 
      maxlength=20 name=username> </li></ul>
    <ul>
      <li class=user_main_text>密 码： </li>
      <li class=user_main_input><input class=txtpasswordcssclass id=txtpassword 
      type=password name=password> </li></ul>
    <ul>
      <li class=user_main_text>cookie： </li>
      <li class=user_main_input><select id=expir name=expir> 
        <option value=none selected>不保存</option> <option value='1'>保存一天</option> 
        <option value='30'>保存一月</option> <option 
      value='365'>保存一年</option></select> </li></ul></div></li>
    <li class=user_main_r><input class=ibtnentercssclass id=ibtnenter 
    style="border-top-width: 0px; border-left-width: 0px; border-bottom-width: 0px; border-right-width: 0px" 
    onclick='javascript:webform_dopostbackwithoptions(new webform_postbackoptions("ibtnenter", "", true, "", "", false, false))' 
    type=image src="themes/default/images/user_botton.gif" name=ibtnenter> </li></ul>
   </dd> 
  <dd id=user_bottom>
  <ul>
    <li class=user_bottom_l></li>
    <li class=user_bottom_c><span style="margin-top: 40px"></span> </li>
    <li class=user_bottom_r></li></ul></dd></dl></div><span id=valrusername 
style="display: none; color: red"></span><span id=valrpassword 
style="display: none; color: red"></span><span id=valrvalidatecode 
style="display: none; color: red"></span>
<div id=validationsummary1 style="display: none; color: red"></div>
</form>
<div></div>


</body></html>
