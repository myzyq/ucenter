<?php defined('U_CENTER') or exit('Access Denied');
/*=======================
 * 数据库连接操作函数库
 * @author d-boy
 * $Id: conn.fun.php 230 2010-12-10 03:22:21Z lunzhijun $
 ========================*/

/**
 * 返回当前应该用哪个连接
 */
function curr_conn_flag(){
	$fname = UC_ROOT."/conn.flag";
	$f = fopen($fname, 'w+');
	$size = filesize($fname)  ? filesize($fname) : 1;
	$str = fread($f,$size );
	$result = FALSE;
	echo $str;
	if (intval($str) >= 40 || intval($str) == 0){
		if (flock($f, LOCK_EX) ) {
			fwrite($f, '0');
			flock($f, LOCK_UN);
		}
		$result = TRUE;
	}else{
		$str = intval($str) + 1;
		if (flock($f, LOCK_EX) ) {
			fwrite($f, $str . '');
			flock($f, LOCK_UN);
		}
		$result = FALSE;
	}
	fflush($f);
	fclose($f);
	return $result;
}

/**
 * 禁用第一个连接配置
 */
function disable_first_conn(){
	$fname = UC_ROOT . "/conn.flag";
	$f = fopen($fname, 'w+');
	if (flock($f, LOCK_EX) ) {
		fwrite($f, 1);
		flock($f, LOCK_UN);
	}
	fflush($f);
	fclose($f);
}


/**
 * 连接数据库
 * @return 返回数据库连接
 */
function conn_db() {
	$db = new MysqlDb();
	//从文件读取当前状态，如果现役服务器不能用，就用备份
	//$flag = curr_conn_flag();

	$db->connect(UCENTER_DBHOST, UCENTER_DBUSER, UCENTER_DBPWD, UCENTER_DBNAME, 0);

	if(!$db->connid) {
		$db->connect(BK_UCENTER_DBHOST, BK_UCENTER_DBUSER, BK_UCENTER_DBPWD, BK_UCENTER_DBNAME, 0);
	}

	if ($db->connid) {
		$db->query("SET NAMES " . UCENTER_DBCHARSET);;
	}else{
		$db->halt(" can not connect mysql");
	}

	return $db;
}


/**
 * 执行SQL 返回查询结果
 * @param $db 数据库连接对象
 * @param $sql SQL语句
 * @return 查询结果
 */
function query($db, $sql) {
	return $db->query($sql);
}

/**
 * 查询数据库，返回查询结果数组
 * @param $db 数据库连接对像
 * @param $sql SQL语句
 * @return 查询结果数组
 */
function select($db, $sql) {
	$rs = query($db, $sql);
	$data = array();
	if($rs) {
		$data = rs_to_arr($rs);
		mysql_free_result($rs);
	}
	return $data;
}

/**
 * 关闭连接
 * @param $db 数据库连接对象
 * @return 关闭连接
 */
function close_db($db) {
	if(!empty($db)) {
		$db->close();
	}
	return true;
}

/**
 * sql 安全过滤
 * @param $value 数据
 * @return 过滤后的数据
 */
function quote_smart($value)    {
	if (get_magic_quotes_gpc()) $value = stripslashes($value);
	if($value == '') $value = 'NULL';
	else if (!is_numeric($value) || $value[0] == '0') $value = "'" . mysql_real_escape_string($value) . "'"; //Quote if not integer
	return $value;
}

/**
 * 结果集转成数组
 * @param $rs 结果集
 * @param $key 做为键的列
 * @return 数组数据集合
 */
function rs_to_arr($rs, $key='') {
	$data = array();
	if(empty($key)) {
		while($row = mysql_fetch_array($rs)) {
			$data[] = $row;
		}
	} else {
		while($row = mysql_fetch_array($rs)) {
			$data[$row[$key]] = $row;
		}
	}
	//print_r($data);
	return $data;
}

/**
 * 执行count语句 返回结果
 * @param $db 数据库连接对象
 * @param $table 表名
 * @param $where 条件
 * @return 数目
 */
function exec_count($db, $table, $where = '') {
	$sql = "select count(*) as num from $table $where";
	$data = select($db, $sql) ;
	//print_r($data);
	$num = $data[0]['num'] ;
	return $num;
}

/**
 * 得到MYSQL的版本信息
 * @param $db 数据库连接对象
 * @return 数据库版本信息
 */
function get_mysql_version($db) {
	$sql = "SELECT VERSION() as ver" ;
	$rs = query($db, $sql);
	$data = mysql_fetch_array($rs) ;
	mysql_free_result($rs);
	return $data['ver'];
}

/**
 * 显示数据库表信息
 * @param $db 数据库连接对象
 * @param $pre 数据表名
 * @return 表信息列表
 */
function show_table_status($db, $pre) {
	$pre = quote_smart("$pre%");
	$sql = "SHOW TABLE STATUS LIKE $pre" ;
	$rs = query($db, $sql) ;
	$data = array();
	if($rs) {
		$data = rs_to_arr($rs);
		mysql_free_result($rs);
	}
	return $data;
}

/**
 * 组织表名
 * @param $dbname 数据库名
 * @param $tablename 表名
 * @param $pre 表名前缀
 * @return 完整表名
 */
function table_name($dbname, $tablename, $pre = '') {
	return "`{$dbname}`.`{$pre}{$tablename}`" ;
}

/**
 * 将条件组装成 $split $k = $v 的形式 并做MYSQL不安全过虑
 * @param $coli 数据 k=>v 形式数组
 * @param $split 结果分隔 默认为','
 * @return 组装好的 $split $k = $v 串
 */
function compile_query($coli = array(),$split=','){

	$condition = '' ;
	foreach($coli as $k=>$v){
		$tmpstr = $k . ' = ' . quote_smart($v);
		$condition = empty($condition) ? $tmpstr : $condition . $split . $tmpstr;
	}
	return $condition;
}

/**
 * 插入数据
 * @param $db 数据库连接对象
 * @param $table 数据表名
 * @param $data 数据
 * @return 新增数据ID
 */
function insert_data($db, $table, $data) {
	$db->insert($table, $data);
	return $db->insert_id();
}

/**
 * 插入信息， 向没有自增标识的表里插数据
 * @param $conn 数据库连接
 * @param $table 表
 * @param $data 数据
 * @return 是否成功
 */
function insert_data_noiden($conn, $table, $data) {
	return $conn->insert($table, $data);
}

/**
 * 返回一行数据
 * @param $conn 数据库连接
 * @param $sql SQL语句
 * @return 一行数据的数组
 */
function get_one($conn, $sql) {
	$rs = query($conn, $sql) ;
	$data = mysql_fetch_array($rs);
	mysql_free_result($rs);
	return $data;
}

/**
 * 更新信息
 * @param $conn 数据库连接
 * @param $table 表名
 * @param $info 数据
 * @param $where 条件
 * @return 操作结果
 */
function update_data($conn, $table, $info = array(), $where = '') {
	$set = compile_query($info, ',');
	if(!empty($where)) $where = ' where ' . $where;
	$sql = "update $table set $set $where";
	return query($conn, $sql);
}

/**
 * 删除信息
 * @param $conn 数据库连接
 * @param $table 表名
 * @param $where 条件
 * @return 操作结果
 */
function delete_data($conn, $table, $where = '') {
	if(!empty($where)) $where = ' where ' . $where;
	$sql = "delete from $table $where";
	//echo $sql;
	return query($conn, $sql);
}
?>