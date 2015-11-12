<?php
/**
 * 测试更新用户信息
 * @author:d-boy
 */
 include "test.header.php" ;
 global $api;
 
 //用户名是改不了的
 $data=$api->update_user(array(
 	 'id' => 88,
 	 'name' => '注册用户',
 	 'email' => 'lunzhijun@gyy1x.cn',
 	 'password' => '654321'
 ));
 
 if($api->is_ok($data)) {
 	echo 'OK';
 }else {
 	echo "Failed: the message is :" . $api->get_msg($data);
 }
 ?>