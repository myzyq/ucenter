<?php
/**
 * 测试返回用户信息
 * @author:d-boy
 */
 include "test.header.php" ;
global $api;
 $data=$api->get_user_info(72);
 if($api->is_ok($data)) {
 	$user = $api->sync_login_user($data);
 	print_r($user);
 }else {
 	echo "Failed: the message is :" . $api->get_msg($data);
 }
 ?>