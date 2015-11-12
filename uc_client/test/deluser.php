<?php 
/**
 * 删除 用户
 * $Id: sync.class.php 77 2010-02-26 02:35:51Z lunzhijun $
 */
include "test.header.php" ;

 $data = $api->del_user( 88);
 print_r($data);
 if($api->is_ok($data)){
 	echo '<br />id of the user is ' . $api->gen_user_id($data);
 }else {
	echo "<br />FAILD : the information is :" ;
	echo $api->get_msg($data);
}

?>