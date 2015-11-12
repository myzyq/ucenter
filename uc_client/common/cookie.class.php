<?php
/**
 * COOKIE 操作类
 * @author d-boy
 * @copyright $Id$
 */ 
class Cookie{
	
	/**
	 * 写COOKIE
	 * @param $key 键
	 * @param $val 值
	 */
	public static function write($key, $val, $expri = 0, $path = '/', $domain = ''){
		$time = empty($expri) ? 0 : (time() + intval($expri));
		
        setcookie($key, $val, $time, $path, $domain);

	}
	
	/**
	 * 读COOKIE
	 * @param  $key
	 */
	public static function read($key) {
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : FALSE;
	}
	
	/**
	 * 删除COOKIE
	 * @param  $key
	 */
    public static function clear($key, $path = '/', $domain = '') {
    	ob_clean();
    	Cookie::write($key, '', -3000, $path, $domain);
    }
}
?>