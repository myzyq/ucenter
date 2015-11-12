<?php 
	/**
	 * 重新同步登录token
	 * @author:d-boy
	 * $Id: resynclogin.php 119 2010-04-12 02:11:15Z lunzhijun $
	 */

   include "test.header.php" ;
   
   $data = $api->login('http://192.168.56.80/assets/','注册用户', '123456', 60000);
		
		//print_r($data);
		if($api->is_ok($data)) {
			echo '<script type="text/javascript" src="' . CLIENT_WEB . 'js/jquery/jquery-1.3.2.min.js"></script>';
			foreach($data['allowed'] as $app) {
				echo '<img style="display:none;" src="' . $app . '" width=0 height=0 />' . "\n";
			}
			echo '<script type="text/javascript" >
						$().ready(function(){
							alert("登录成功");
							location.href="' . $api->get_forward($data) . '";
						});
			      </script>';
		}else {
			
			echo '<script type="text/javascript">alert("' . $api->get_msg($data) .'");history.back();</script>"';
		}
		exit;
?>