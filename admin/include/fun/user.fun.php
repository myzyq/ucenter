<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 用户管理操作函数库
 * @author d-boy
 * $Id: user.fun.php 250 2011-03-15 08:28:08Z lunzhijun $
 ========================*/

	if(!defined('USER_TABLE')) define('USER_TABLE',table_name(UCENTER_DBNAME, 'user_base' , UCENTER_TABLE_PRE )); //用户数据表名
	if(!defined('USERGROUP_TABLE')) define('USERGROUP_TABLE',table_name(UCENTER_DBNAME, 'user_group', UCENTER_TABLE_PRE )); //用户组关系数据表全名
	if(!defined('GROUP_TABLE')) define('GROUP_TABLE',table_name(UCENTER_DBNAME, 'base_group', UCENTER_TABLE_PRE )); //用户组数据表全名

	include_once UC_ROOT . '/include/sync.class.php' ;


	/**
	 * 查询用户数
	 * @param $db 数据库连接对象
	 * @param $where 条件数组
	 * @return 符合条件的用户数
	 */
	function count_users($db, $where = array()) {
		$table = USER_TABLE;
		$w = get_where_str($where);
		return exec_count($db, $table, $w);
	}

	/**
	 * 用户管理首页显示数据
	 * @return 用户管理首页数据
	 */
	function user_index_data() {
		$db = conn_db();

		$viewData = array();
		$keys = $viewData['keys'] = empty($_REQUEST['keys']) ? array() : $_REQUEST['keys']; //查询条件变量
		//$keys['flag'] = 0;
		$viewData['msg'] = empty($_REQUEST['msg']) ? null : $_REQUEST['msg'] ; //提示信息
		$viewData['keys'] = $keys;
		if(isset($keys['group_id']) && $keys['group_id'] ==-1) unset($keys['group_id']);
		$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']); //页面码

		$pageSize = 20; //每页20条记录
		$total = count_users($db, $keys); //总数

		include_pager(); //引入分页控件
		$myPager = new Pager(array('page' => $page, 'total' => $total, 'pagesize' => $pageSize, 'always' => false)); //分页控初始化
		//返回当前页最小和最大记录号
		$start = $myPager->get_start_no();
		//$max = $myPager->get_end_no();

		$viewData['data'] = search_user_by_keys($db, $keys, $start, $pageSize, ' flag asc, id desc '); //查询数据
		$viewData['pager'] = $myPager->exec_pager(); //分页条代码
		$viewData['groups'] = search_group($db, array(), 0,0);
		close_db($db);
		return $viewData;
	}

	/**
	 * 查询用户信息
	 * @param $db 数据库连接对象
	 * @param $keys 查询条件
	 * @return 查询结果数据
	 */
	function search_user_by_keys($db, $keys, $start = 0, $max = 0) {
		$w = get_where_str($keys);

		$limit = empty($max) ? "" : " order by id desc limit $start,$max " ;
		$w .= $limit;

		$table = USER_TABLE . 'u left join ' . GROUP_TABLE . ' g on u.group_id = g.id';
		$sql = "select u.*, g.group_name from $table $w ";
		//echo $sql;
		$data = select($db, $sql);
		return $data;
	}



	/**
	 * 返回查询条件 where子句
	 * @param $keys 条件数组
	 * @return 查询条件 where子句
	 */
	function get_where_str($keys) {
		$where = ' where flag=0 ' ;
		if(!empty($keys)){
		$where .= empty($keys['name']) ? '' : ' and name like ' . quote_smart("%{$keys['name']}%") ;
		$where .= empty($keys['email']) ? '' : ' and email like ' . quote_smart("%{$keys['email']}%");
		$where .= empty($keys['id']) ? '' : ' and u.id=' . quote_smart($keys['id']);
		$where .= !isset($keys['flag']) ? '' : ' and flag=' . intval($keys['flag']);
		$where .= !isset($keys['group_id']) || $keys['group_id'] == '-1'  ? '' : ' and group_id = '   .  intval($keys['group_id']) ;
		//$where = empty($where) ? '' : ' where 1=1 ' . $where;
		}
		return $where;
	}

	/**
	 * 按用户名查用户信息
	 * @param $conn 数据库连接
	 * @param $uname 用户名
	 * @param $fromall 是否从所有的数据中查找（包括删除的）
	 */
	function get_user_by_name($conn, $uname, $fromall = false) {
	   if(empty($uname)) {return array();}
	   $keys = array('name'=>$uname);
	   $data = array();
	   if(!$fromall)
	       $data = search_user_by_keys($conn, $keys, 0, 0);
	   else{
	       $sql = "select * from " . USER_TABLE . " where name=" . quote_smart($uname);
	       $data = select($conn, $sql);
	   }
	   return empty($data) ? array() : $data[0];
	}



	/**
	 * 用户信息以用户组分组
	 * @param $data 用户数据
	 */
	function array_key_by_group($data = array()){
		if(empty($data )) return $data;
		$result = array();
		foreach($data as $item){
			$group = $item['group_id'];
			if(! array_key_exists($group, $result)){
				$result[$group] = array('name'=> $item['group_name'], 'users' => array());
			}
			array_push($result[$group]['users'], $item);
		}
		return $result;
	}

	/**
	 * 将用户移到某一组下
	 * @param $conn 数据库连接
	 * @param $id 用户ID
	 * @param $group 用户组
	 */
	function move_user($conn, $id, $group){
		$user = array('id' => $id, 'group_id' => $group);
		return update_user($conn, $user);
	}

	/**
	 * 全部用户，以用户组分组
	 * @param $conn 数据库连接
	 */
	function all_user_group_by_group($conn){
		$data = search_user_by_keys($conn, array(), 0, 0);
		$rs = array_key_by_group($data);
		return $rs;
	}

	/**
	 * 创建用户信息
	 * @param $conn 数据库连接
	 * @param $id 员工ID
	 * @return 是否成功，及信息
	 */
	function create_user_old($conn, $id) {
		//查询用户对应的员工存不存在
		include_member_fun();
		$mem = get_one_member($conn, $id);
		$user = get_one_user($conn, $id, true); //查一下用户是不是存在
		$flag = FALSE;
		$msg = '';
		if(!empty($mem) && empty($user)) {
			//存在，创建基本信息
			$pass = rand_str(); //随机6位密码
			$md5_pas = md5($pass);//md5 加密密码
			$info = array('id' => $mem['id'], 'name'=>$mem['real_name'],'pas_sou'=>$pass , 'password'=>$md5_pas,'is_band' => 0);
		    $info['create_time'] = date('Y-m-d h:i:s');
			insert_user($conn, $info);

			$flag = TRUE;

		}else if(empty($mem)) {
			$msg = '员工不存在！';
		}else if(!empty($user) && $user['flag'] == 1) {
			update_user($conn, array('id' => $user['id'], 'flag' => 0)) ;
		}else {
			$msg = '用户已经存在！';
		}
		return array('flag'=>$flag, 'msg' => $msg);
	}

	/**
	 * 创建新的用户
	 * @param $conn 数据库连接
	 * @param $uname 用户名
	 * @param $pwd 用户密码，如果密码为空，就会自动生成一个密码
	 * @param $email 用户EMAIL
	 * @param $group 用户组ID
	 */
	function create_user($conn, $uname, $pwd='' , $email, $group = 0) {
		   $msg = "";
		   $result = false;
	       //校验一下数据
	       if(empty($uname)) {return array('flag'=> false, 'msg'=> "用户名不能为空");}
	       if(empty($pwd)) $pwd = rand_str(10);
	       //用户是否已存在
	       $user = get_user_by_name($conn, $uname, true);
	       if(!empty($user) && $user['flag'] == 0) {
	           $msg = '用户已经存在！';
	           $result = false;
	       }else{
	           if(!empty($user)) {
	           	 //原来有的就更新
	           	 $udata = array();
	           	 $udata['name'] = $uname;
	           	 $udata['email'] = $email;
	           	 $udata['pas_sou'] = $pwd;
	           	 $udata['password'] = md5($pwd);
	           	 $udata['flag'] = 0;
	           	 $udata['id'] = $user['id'];
	           	 $udata['group_id'] = $group;
	           	// print_r($udata);
	           	 update_user($conn, $udata) ;
	           }else{
	             $user = array();
	             $user['name'] = $uname;
                 $user['email'] = $email;
                 $user['pas_sou'] = $pwd;
                 $user['password'] = md5($pwd);
                 $user['flag'] = 0;
                 $user['group_id'] = $group;
                 $user['create_time'] = date('Y-m-d h:i:s');
                 $user['id'] = insert_user($conn, $user);
	           }
	           $msg = "成功,新用户密码是 $pwd,请您牢记！";
	           $result = true;
	       }
	       return array('flag'=>$result, 'msg' => $msg, 'user' => $user);
	}

	/**
	 * 用基本信息创建用户
	 * @param $conn 数据库连接
	 * @param $name 用户名
	 * @param $email EMAIL
	 * @return 操个信息
	 */
	function create_user_usebaseinfo($conn, $name, $email){
			$user = one_user_by_name($conn, $name); //查一下用户是不是存在
			if(!empty($user)) {
				return array('flag'=>false, 'msg'=>'用户名己经存在');
			}
			include_member_fun();
			$mem = get_member_byname($conn, $name);
			if(empty($mem)) {
				return array('flag'=>false, 'msg'=>'员工不存在，请添加这个员工');
			}

			$user = get_one_user($conn, $mem['id'], true);

			if(!empty($user)) {
				//以前有过这个用户，只是删除了，我们把状态改过来就行了
				$info=array('email' => $email, 'flag' => 0 , 'id' => $mem['id']);

				update_user($conn, $info);
			}else{

			$pass = rand_str(); //随机6位密码
			$md5_pas = md5($pass);//md5 加密密码
			$info = array('id' => $mem['id'], 'name'=>$name,'pas_sou'=>$pass , 'password'=>$md5_pas,'email' => $email,'is_band' => 0);
			insert_user($conn, $info);
			}
			$flag = TRUE;
			return array('flag' => true, 'msg' => '用户信息已保存');
	}

	/**
	 * 插入用户数据
	 * @param $conn 数据库连接
	 * @param $info 用户信息
	 * @return 是否成功！
	 */
	function insert_user($conn, $info = array()) {
		$table = USER_TABLE;
		
		$result = insert_data($conn, $table, $info);

		//sync_user($conn, $info['id']);

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
		if(!$flag) {
			$users = search_user_by_keys($conn, array('id' => $id), 0, 0);
		} else {
			$sql = "select * from " . USER_TABLE . " where id=$id" ;
			$users = select($conn, $sql) ;
		}
		return empty($users) ? array() : $users[0] ;
	}

	/**
	 * 按用户名查询用户，返回一个用户信息
	 * @param $conn 数据库连接
	 * @param $name 用户名
	 * @return 用户信息
	 */
	function one_user_by_name($conn, $name, $flag = false) {
		$name = quote_smart($name);
		$table = USER_TABLE;
		$f = $flag ? ' 1=1 ' : ' flag = 0 ';
		$sql = "select * from $table where $f and name = $name";
		$rs = query($conn, $sql);
		//echo $sql;
		$row = mysql_fetch_array($rs) ;
		mysql_free_result($rs);
		return $row;
	}

	/**
	 * 更新用户数据
	 * @param $conn 数据库连接
	 * @param $info 数据
	 * @return 更新结果
	 */
	function update_user($conn, $info) {
		if(empty($info['id'])) return 0;
		$id = $info['id'];
		unset($info['id']);
		$where = ' id=' . intval($id);
		$table = USER_TABLE;
		$result = update_data($conn, $table, $info, $where);
		if(!empty($result)) {
			//sync_user($conn, $id); //同步用户信息
		}

		return $result;
	}

	/**
	 * 重新生成用户的密码
	 * @param $conn 数据库连接
	 * @param $id ID
	 * @param $pwd 新密码
	 * @return 操作结果
	 */
	function reset_user_pwd($conn, $id, $pwd) {
		$id = intval($id);
		$pass = empty($pwd) ? rand_str() : $pwd ; //随机6位密码
		$md5_pas = md5($pass);//md5 加密密码
		$info = array('id' => $id, 'password' => $md5_pas, 'pas_sou' => $pass);
		$rs = update_user($conn, $info);
		if(empty($rs)) {
			return array('flag'=>false,'msg'=>'操作失败');
		}else {
			//sync_user($conn, $id); //同步用户信息
			return array('flag'=>true,'msg'=>'操作已成功，用户ID:' . $id . '的新密码是' . $pass);
		}
	}

	/**
	 * 批量删除用户
	 * @param $conn 数据库连接
	 * @param $ids ID数组
	 * @return 删除结果
	 */
	function batch_delete_user($conn, $ids) {
		$rs = array();
		foreach($ids as $id) {
			$rs[] = delete_user($conn, $id);
		}
		return count($rs);
	}

	/**
	 * 删除用户信息
	 * @param $conn 数据库连接
	 * @param $id 用户ID
	 * @return 删除结果
	 */
	function delete_user($conn, $id) {
		$table = USER_TABLE;
		return update_data($conn, $table, array('flag' => 1) , ' id=' . intval($id));
	}

	/**
	 * 引入员工操作函数库
	 */
	function include_member_fun(){
		include_once FUN_ROOT . 'member.fun.php';
	}

	/**
	 * 更新用户登录信息
	 * @param $conn 数据库连接
	 * @param $id 用户ID
	 * @param $ip 用户IP
	 * @return 用户名
	 */
	function update_login_info_byid($conn, $id , $ip) {
		$curr_time = date('Y-m-d H:i:s');
		$id = intval($id);
		$ip = quote_smart($ip);
		$sql = " update " . USER_TABLE . " set last_login_time = '$curr_time', last_login_ip = $ip ,login_times = if(login_times is null, 0 +1 , login_times +1) where id=$id";
		return query($conn, $sql);
	}

	/**
	 * 同步用户信息
	 * @param $conn 数据库连接
	 * @param $uid 用户IDeas
	 * @return 同步操作结果
	 */
	function sync_user($conn, $uid) {
		//同步信息
		$sync = $_ENV['sync'];

		$sync->add_sync_user_note($uid);
		$sync->sync();
	}
?>