<?php
	 include_once 'header.php';
     global $api;
	 $forward = empty($_REQUEST['forward']) ? '' : $_REQUEST['forward'];
	 $uid = $_REQUEST['uid'];
	 $data = $api->logout($uid); //注销操作
 	 /*array('apis' => $loguotapis, 'userid' => $uid, 'forward' => $forward);远程接口 返回值*/
	 if(empty($data)) {
	 	echo '<script type="text/javascript">alert("内部错误， 请重试");history.back();</script>';
	 	exit();
	 }
	 
	 if($data[FLAG_FLAG] == FLAG_ERR) {
	 echo '<script type="text/javascript">alert("' . $api->get_msg($data) . '");history.back();</script>';
	 	exit(); 
	 }
	 
	 if($data[FLAG_FLAG] == FLAG_OK) {
	 		echo '<script type="text/javascript" src="' . CLIENT_WEB . 'js/jquery/jquery-1.3.2.min.js"></script>'; //JQUERY CORE
	 		foreach($data['apis'] as $api) {
	 			echo '<img style="display:none;" src="' . $api . '" width=0 height=0 />' . "\n";
	 		}
	 		$f = empty($data['forward']) ? 'index.php' : $data['forward']; 
	 		echo '<script type="text/javascript" >
					$().ready(function(){
						alert("注销成功");
						location.href="' . $f . '";
					});
		      </script>';
	 }
?>