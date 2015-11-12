<?php
/**
 * 测试更新用户信息
 * @author:d-boy
 */
 include "test.header.php" ;
 global $api;
 $data=$api->delete_user_app_relation(88);
 if($api->is_ok($data)) {
 	echo 'OK';
 }else {
 	echo "Failed: the message is :" . $api->get_msg($data);
 }
 ?>