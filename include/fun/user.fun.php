<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 用户管理操作函数库
 * @author d-boy
 * $Id: user.fun.php 128 2010-05-07 03:59:00Z lunzhijun $
 ========================*/
if(!defined('USER_TABLE')) define('USER_TABLE',table_name(UCENTER_DBNAME, 'user_base' , UCENTER_TABLE_PRE )); //用户数据表名

	/**
	 * 修改密码
	 * @param  $pwd 新密码
	 */
	function change_password($pwd){
		include_once UC_ROOT . "/uc_client/header.php";
		$user = curr_user();
		$info = array('id' => $user['id'], 'password' => $pwd, "email" => "");
		$data = $api->update_user($info);

		$result = array();
		$result['flag'] = $api->is_ok($data) ;
		$result['msg'] = $api->get_msg($data)  ;
		$result['msg'] .=   $api->gen_sync_triggers($data);
		return $result;
	}


    /**
     * 获取一个用户信息
     * @param $conn 数据库连接信息
     * @param $id ID
     * @param $flag 标志，默认只在正常的用户中查询
     * @return 用户基本信息
     */
    function get_one_user($conn, $id, $flag = false) {
        $id = intval($id);
        $users = array();
        $sql = "select * from " . USER_TABLE . " where id=$id " . ($flag ? "and flag=0" : "") ;
        $users = select($conn, $sql) ;

        return empty($users) ? array() : $users[0] ;
    }

?>