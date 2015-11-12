<?php
/**
 * 访问远程文件类
 * @author d-boy
 * $Id: ucsocket.class.php 230 2010-12-10 03:22:21Z lunzhijun $
 */
class UCSocket{
	/**
	 *  远程打开URL
	 *  @param string $url		打开的url，　如 http://www.baidu.com/123.htm
	 *  @param int $limit		取返回的数据的长度
	 *  @param string $post		要发送的 POST 数据，如uid=1&password=1234
	 *  @param string $cookie	要模拟的 COOKIE 数据，如uid=123&auth=a2323sd2323
	 *  @param bool $bysocket	TRUE/FALSE 是否通过SOCKET打开
	 *  @param string $ip		IP地址
	 *  @param int $timeout		连接超时时间
	 *  @param bool $block		是否为阻塞模式
	 *  @return			取到的字符串
	 */
	function fopen2($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 4, $block = TRUE) {
		$__times__ = isset($_GET['__times__']) ? intval($_GET['__times__']) + 1 : 1;
		if($__times__ > 2) {
			return '';
		}
		$url .= (strpos($url, '?') === FALSE ? '?' : '&')."__times__=$__times__";
		return $this->fopen($url, $limit, $post, $cookie, $bysocket, $ip, $timeout, $block);
	}

	/**
	 *  远程打开URL
	 *  @param string $url		打开的url，　如 http://www.baidu.com/123.htm
	 *  @param int $limit		取返回的数据的长度
	 *  @param string $post		要发送的 POST 数据，如uid=1&password=1234
	 *  @param string $cookie	要模拟的 COOKIE 数据，如uid=123&auth=a2323sd2323
	 *  @param bool $bysocket	TRUE/FALSE 是否通过SOCKET打开
	 *  @param string $ip		IP地址
	 *  @param int $timeout		连接超时时间
	 *  @param bool $block		是否为阻塞模式
	 *  @return			取到的字符串
	 */
	function fopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 4, $block = TRUE) {
		$return = '';
		$matches = parse_url($url);
		!isset($matches['host']) && $matches['host'] = '';
		!isset($matches['path']) && $matches['path'] = '';
		!isset($matches['query']) && $matches['query'] = '';
		!isset($matches['port']) && $matches['port'] = '';
		$host = $matches['host'];
		$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
		$port = !empty($matches['port']) ? $matches['port'] : 80;
		if($post) {
			$out = "POST $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			//$out .= "Referer: $boardurl\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host\r\n";
			$out .= 'Content-Length: '.strlen($post)."\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cache-Control: no-cache\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
			$out .= $post;
		} else {
			$out = "GET $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			//$out .= "Referer: $boardurl\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
		}
		$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
		if(!$fp) {
			return '';//note $errstr : $errno \r\n
		} else {
			stream_set_blocking($fp, $block);
			stream_set_timeout($fp, $timeout);
			@fwrite($fp, $out);
			$status = stream_get_meta_data($fp);
			if(!$status['timed_out']) {
				while (!feof($fp)) {
					if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
						break;
					}
				}

				$stop = false;
				while(!feof($fp) && !$stop) {
					$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
					$return .= $data;
					if($limit) {
						$limit -= strlen($data);
						$stop = $limit <= 0;
					}
				}
			}
			@fclose($fp);
			return $return;
		}
	}
}
?>