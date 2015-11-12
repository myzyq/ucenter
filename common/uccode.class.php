<?php
/**
 * 加密解密操作类
 * @author d-boy
 * $Id: uccode.class.php 21 2010-02-08 08:42:06Z lunzhijun $
 */
class UCCode{
	/**
	 * 变base64编码
	 * @param $var 数据
	 * @return base64编码后的信息
	 */
	function gen_64_code($var) {
		return base64_encode($var);
	}
	
	/**
	 * base64 编码解密
	 * @param $b64 base64编码
	 * @return 解密后的base64编码
	 */
	function de_64_code($b64) {
		return base64_decode($b64);
	}
	
	/**
	 * 转MD5
	 * @param $var
	 * @return md5加密后的串
	 */
	function md5($var) {
		return md5($var);
	}
	
	/**
	 * JSON输出
	 * @param $data 数据
	 * @return json序列化数据
	 */
	function serialize($data = array()) {
		//print_r($data);
		//print_r($this->gbk2utf8($data));
		return json_encode($this->gbk2utf8($data));
	}
	
	/**
	 * 反序列化JSON数据
	 * @param $str JSON序列化后的串
	 * @return 对象数组
	 */
	function unserialize($str) {
		$data = json_decode($str,  TRUE );
		$data = $this->utf82gbk($data);
		return $data;
	}
	
	/**
	 * GBK 转UTF8
	 * @param $data 数据
	 * @return UTF8 的数据
	 */
	function gbk2utf8($data) {
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$data[$k] = $this->gbk2utf8($v);
			}
		}else {
			$data = @iconv('GBK','UTF-8',$data);
		}	
		return $data;
	}
	
	/**
	 * GBK 转UTF8
	 * @param $data 数据
	 * @return UTF8 的数据
	 */
	function utf82gbk($data) {
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$data[$k] = $this->utf82gbk($v);
			}
		}else {
			$data = @iconv('UTF-8','GBK',$data);
		}	
		return $data;
	}
}
?>