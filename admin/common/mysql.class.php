<?php
/**
 * 数据库底层封装类
 *
 * 本类封装了大部分的数据库操作函数，并提供了错误处理机制。
 *
 * $Id: mysql.class.php 230 2010-12-10 03:22:21Z lunzhijun $
 */

defined('U_CENTER') or exit('Access Denied');

class MysqlDb {

	var $connid;
	var $querynum = 0;

	//连接数据库
	function connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect = 0, $dbcharset = 'utf8') {

			$func = $pconnect == 1 ? 'mysql_pconnect' : 'mysql_connect';
			if (!$this->connid = @$func($dbhost, $dbuser, $dbpw)) {
				//$this->halt('Can not connect to MySQL server');
				return null;
			}
			// 当mysql版本为4.1以上时，启用数据库字符集设置
			if ($this->version() > '4.1' && $dbcharset) {
				$serverset = $dbcharset ? "character_set_connection='" . $dbcharset . "',character_set_results='" . $dbcharset . "',character_set_client=binary" : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',')." sql_mode='' ") : '';
				$serverset && mysql_query("SET $serverset", $this->connid);
			}
			// 当mysql版本为5.0以上时，设置sql mode
			if($this->version() > '5.0') {
				mysql_query("SET sql_mode=''" , $this->connid);
			}
			if($dbname) {
				if(!@mysql_select_db($dbname , $this->connid)) {
					$this->halt('Cannot use database '.$dbname);
				}
			}

		return $this->connid;
	}

	//选择数据库
	function select_db($dbname) {
		return mysql_select_db($dbname , $this->connid);
	}

	//执行sql语句
	function query($sql , $type = '' , $expires = 3600, $dbname = '') {
		$func = $type == 'UNBUFFERED' ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql , $this->connid)) && $type != 'SILENT') {
			$this->halt('MySQL Query Error', $sql);
		}
		$this->querynum++;
		return $query;
	}

	//执行sql语句，只得到一条记录
	function get_one($sql, $type = '', $expires = 3600, $dbname = '') {
		$query = $this->query($sql, $type, $expires, $dbname);
		$rs = $this->fetch_array($query);
		$this->free_result($query);
		return $rs ;
	}

	//从结果集中取得一行作为关联数组
	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	//取得前一次 MySQL 操作所影响的记录行数
	function affected_rows() {
		return mysql_affected_rows($this->connid);
	}

	//取得结果集中行的数目
	function num_rows($query) {
		return mysql_num_rows($query);
	}

	//返回结果集中字段的数目
	function num_fields($query) {
		return mysql_num_fields($query);
	}

	//释放查询缓存
	function free_result($query) {
		return mysql_free_result($query);
	}

	//获取插入ID
	function insert_id() {
		return mysql_insert_id($this->connid);
	}

	//返回查询结果集
	function fetch_row($query)  {
		return mysql_fetch_row($query);
	}

	//返回查询结果集
	function result($query, $row) {
		return @mysql_result($query, $row);
	}

	//显示数据库版本
	function version() {
		return mysql_get_server_info($this->connid);
	}

	//关闭数据库连接
	function close() {
		return mysql_close($this->connid);
	}

	//获取mysql错误信息
	function error() {
		return @mysql_error($this->connid);
	}

	//获取mysql错误编号
	function errno() {
		return intval(@mysql_errno($this->connid)) ;
	}

	//返回数据表信息
	function table_status($table) {
		return $this->get_one("SHOW TABLE STATUS LIKE '$table'");
	}

	function insert($tablename, $array) {
		//echo "INSERT INTO $tablename(`".implode('`,`', array_keys($array))."`) VALUES('".implode("','", $array)."')";
		return $this->query("INSERT INTO $tablename(`".implode('`,`', array_keys($array))."`) VALUES('".implode("','", $array)."')");
	}

	function update($tablename, $array, $where = '') {
		if($where)
		{
			$sql = '';
			foreach($array as $k=>$v)
			{
				$sql .= ", `$k`='$v'";
			}
			$sql = substr($sql, 1);
			$sql = "UPDATE $tablename SET $sql WHERE $where";
		}
		else
		{
			$sql = "REPLACE INTO $tablename(`".implode('`,`', array_keys($array))."`) VALUES('".implode("','", $array)."')";
		}
		return $this->query($sql);
	}

	//显示mysql错误信息
	function halt($message = '', $sql = '') {
		exit("MySQL Query:$sql <br> MySQL Error:".$this->error()." <br> MySQL Errno:".$this->errno()." <br> Message:$message");
	}

}
?>