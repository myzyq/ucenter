<?php
//获取接口URL  和 app_id
$QUERY_STRING = $_SERVER['QUERY_STRING'];
$param = explode('&',$QUERY_STRING);
for($i=0;$i<4;$i++){
	$url[$i]= $param[$i]; 
}
$url = implode('&',$url);
$app_id = $param[4];
$app_id = (int)$app_id;
$OAurl = "http://api.oa.gyyx.cn".$url;

//获取接口返回  用户信息
$ch = curl_init($OAurl);
curl_setopt($ch, CURLOPT_HEADER, 0);   
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$cont = curl_exec($ch);
curl_close($ch);
$content = json_decode($cont,true);
//echo  $cont;
if(empty($app_id)){
	echo  json_encode("false");
	exit;
}
//查询ucenter 是否存在该用户
$Account = $content ['Account'];
$con = mysql_connect("192.168.4.176","ucenter","g2H1M6tQlF_ghSJI");
$sql = "SELECT id FROM `ucenter_manager`.`user_base` where email = '".$Account."@gyyx.cn' limit 1";
$res = mysql_query($sql,$con);
$result = mysql_fetch_row($res);

//检查  该用户是否有app登陆权限
if($result[0]){
	$user_id = $result[0];
	$res_app = mysql_query("SELECT * FROM `ucenter_manager`.`app_user_ip` where user_id = '$user_id' and app_id = '$app_id' limit 1",$con);
	$result_app = mysql_fetch_row($res_app);
	if($result_app){
		$content['UC_userid'] = $user_id;
	}
}
$content['App_id'] = $app_id;
echo  json_encode($content);