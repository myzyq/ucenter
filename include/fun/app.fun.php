<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 应用管理操作函数库
 * @author d-boy
 * $Id: app.fun.php 116 2010-04-12 01:44:06Z lunzhijun $
 ========================*/

	if(!defined('APP_TABLE')) define('APP_TABLE',table_name(UCENTER_DBNAME, 'application',  UCENTER_TABLE_PRE)); //应用数据表名
	if(!defined('APPUSERIP_TABLE')) define('APPUSERIP_TABLE',table_name(UCENTER_DBNAME, 'app_user_ip',  UCENTER_TABLE_PRE)); //应用数据表名
	if(!defined('USER_TABLE')) define('USER_TABLE',table_name(UCENTER_DBNAME, 'user_base',  UCENTER_TABLE_PRE)); //应用数据表名
	include_once FUN_ROOT . 'ip.fun.php';
	/**
	 * 查询应用数
	 * @param $db 数据库连接对象
	 * @param $keys 条件
	 * @return 符合条件的应用数
	 */
	function count_apps($db, $keys = array()) {
		$table = APP_TABLE;
		$where = get_apps_where($keys);
		$data = exec_count($db, $table, $keys);
		
		return $data;
	}
	
	/**
	 * 查询应用信息
	 * @param $conn 数据库连接
	 * @param $keys 条件
	 * @param $start 开始
	 * @param $end 结束
	 * @param $order 排序条件
	 * @return 符合条件的数据
	 */
	function search_apps($conn, $keys=array(), $start, $end, $order=' id desc') {
		$table = APP_TABLE;
		$where = get_apps_where($keys);
		if(!empty($order)) $where .= ' order by ' . $order;
		if(!empty($end)) $where .= ' limit ' . intval($start) . ',' . intval($end);
		$sql = "select * from $table $where";

		return select($conn, $sql);
	}
	
	/**
	 * 返回WHERE子句
	 * @param $keys 条件
	 * @return WHERE子句
	 */
	function get_apps_where($keys) {
		
		$where = ' where flag = 0 ' ;
		if(!empty($keys)){
			if(!empty($keys['id'])) $where .= ' and id=' . intval($keys['id']);
			if(!empty($keys['app_name'])) $where .= ' and app_name like ' . quote_smart('%'.$keys['app_name'].'%');
			if(!empty($keys['create_date'])) $where .= ' and create_date = ' . quote_smart($keys['create_date']);
			if(isset($keys['flag'])) $where .= ' and flag = ' . intval($keys['flag']);
		}
		return $where;
	}
	
	/**
	 * 查询一个应用信息
	 * @param $conn 数据连接
	 * @param $id ID
	 * @return 一个应用信息
	 */
	function get_one_app($conn, $id) {
		$data = search_apps($conn, array('id'=> intval($id)), 0, 0);
		return empty($data) ? array() : $data[0];
	}
	


	
	/**
	 * 查询一个用户可访问的APP
	 * @param $conn 数据库连接
	 * @param $uid 用户ID
	 * @param $enabledFlag 是否查询可用的
	 * @return 用户可访问的APP
	 */
	function get_user_apps($conn, $uid , $enabledFlag = false) {
		$uid = intval($uid);
		$en = $enabledFlag ? ' and flag = 0 ' : ''; 
		
		$sql = "select * from " . APP_TABLE ." where flag=0 and id in (
				select app_id from  " . APPUSERIP_TABLE . " where user_id = $uid $en
				)";
		
		$data = select($conn, $sql) ;
		return $data;
	}
	
	function apps_the_user_can_login($conn, $id, $clientip){
	   //查询该用户所有有权限访问的应用
        $userapps = get_user_apps($conn, $id, true);
        if(empty($userapps)) {//没有应用被允许访问
            return array();
        }
        $allowed = array();
        //print_r($userapps);
        //找出这些应用中用户IP不受限的 
        foreach($userapps as $appinfo) {
            $ips = get_user_legal_ips($conn, $id, $appinfo['id']); 
            //print_r($ips);    
            if(check_ip($ips, $clientip)) {
                $allowed[] = $appinfo;
            }
        }
        //print_r($allowed);
        if(empty($allowed)) { //所有应用都不允许这个用户IP登录
            return array();
        }
        return $allowed;
	}
	
	/**
     * 验证IP合法性，用户自身，应用， 应用绑定用户IP都允许才行
     * @param $ips array('user' => , 'app' => , 'appuserip' => )
     * @param $ip 当前用户IP
     * @return 返回结果
     */
    function check_ip($ips = array() , $ip) {
        //print_r($ips['user']);
        return check_ip_arr($ips['user'], $ip) && check_ip_arr($ips['app'], $ip) && check_ip_arr($ips['appuserip'], $ip);
    }
    
    /**
     * 得到用户合法的IP集合
     * @param $uid 用户ID
     * @param $appid APP ID
     * @return 用户在要个应用下合法的IP地址
     */
    function get_user_legal_ips($conn, $uid, $appid) {
        //用户自身IP绑定
        $user = get_one_user($conn, $uid);
        $userIP = empty($user['ip_band']) || empty($user['is_band']) ? array() : explode(";", $user['ip_band']);
        //IP组       
        if (!empty($user['ip_group']) && !empty($user['ip_band'])) {
            $ipgroupid = $user['ip_group'];
            $userIPGroup = get_one_ip_group($conn, $ipgroupid);
            if(!empty($userIPGroup) && !empty($userIPGroup['ip'])){
                $ips = explode(";", $userIPGroup['ip']);
                $userIP = array_merge($userIP, $ips);
            }
        }       
        if(empty($userIP)) $userIP[0] = '[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}';
        
        //应用自身IP绑定
        $app = get_one_app($conn, $appid) ;
        $appIP = empty($app['is_band']) || empty($app['ip_band']) ? array() : explode(";", $app['ip_band']) ;
        //IP组
        if(!empty($app['is_band']) && !empty($app['ip_group'])) {
            $ipid = $app['ip_group'];
            $ipgroup = get_one_ip_group($conn, $ipid);
            if(!empty($ipgroup) && !empty($ipgroup['ip'])){
                $ips = explode(";", $ipgroup['ip']);
                $appIP = array_merge($appIP, $ips);
            }
        }
        if(empty($appIP)) $appIP[0] = '[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}';
        
        //应用绑定用户IP
        $appuser = get_appuserip($conn, $appid, $uid) ;
        $appUserIP = empty($appuser['is_band']) || empty($appuser['ip_band']) ? array('[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}') : explode(";", $appuser['ip_band']);
        //IP组
        if(!empty($appuser['is_band']) && !empty($appuser['ip_group'])) {
            $ipid = $appuser['ip_group'];
            $ipgroup = get_one_ip_group($conn, $ipid);
            if(!empty($ipgroup) && !empty($ipgroup['ip'])){
                $ips = explode(";", $ipgroup['ip']);
                $appUserIP = array_merge($appUserIP, $ips);
            }
        }
        if(empty($appUserIP)) $appUserIP[0] = '[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}';
        
        //print_r($appUserIP);
        $ip = array('user' => $userIP, 'app' => $appIP, 'appuserip' => $appUserIP);
        //print_r($ip);     
        return $ip;
    }
    
    /**
     * 验证IP是否在集合中
     * @param $arr_ip IP集合
     * @param $ip IP
     * @return IP是否在合法集合中
     */
    function check_ip_arr($arr_ip = array() , $ip) {
        $result = false;
        foreach($arr_ip as $item) {
            $item = str_replace(array('.','*'), array('\.','[0-9]{1,3}') , $item);
            //echo $item;
            if(preg_match('/^' . $item . '$/i', $ip)) {
                $result = true;
                break;
            }   
        }
        return $result;
    }
    
	
?>