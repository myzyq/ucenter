<?php
/**
 * 测试向UCENTER注册用户
 * @author : d-boy
 * $Id: re 77 2010-02-26 02:35:51Z lunzhijun $
 */
include "test.header.php" ;
global $api;
$data = $api->reg_user('注册用户', '123456', 'dboy.yes@163.com');
if($api->is_ok($data)) 
{
	print_r($data);
	echo "<br /> the information of the new user is :";
	print_r($api->sync_login_user($data));
}else {
	echo "<br /> FAILD : the information is this:" ;
	echo $api->get_msg($data);
}

 ?>