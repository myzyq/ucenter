<?php 

/**
 * 自定义通信加密解密算法
 * @author: d-boy
 * $Id: customdes.class.php 230 2010-12-10 03:22:21Z lunzhijun $
 */
class CustomDES{
	const DECODE='DECODE';
	const ENCODE='ENCODE';	
	/**
	 * 对称加密算法
	 * @param $string 要加密的串
	 * @param $operation 操作  DECODE 或 ENCODE;
	 * @param $key
	 * @param $expiry
	 * @return 加密或解密后的串
	 */
	public static function encrypt_decrypt($string, $operation, $key = '', $expiry = 0) {
	    $operation == self::ENCODE ? $string = base64_encode($string) : '';
	    $authkey = "gyyx";
	    $ckey_length = 4;   // 随机密钥长度 取值 0-32;
	    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
	    // 当此值为 0 时，则不产生随机密钥
	    
	    $key = md5($key ? $key : $authkey);
	    $keya = md5(substr($key, 0, 16));
	    $keyb = md5(substr($key, 16, 16));
	    $keyc = $ckey_length ? ($operation == self::DECODE ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	    
	    $cryptkey = $keya.md5($keya.$keyc);
	    $key_length = strlen($cryptkey);
	    
	    $string = $operation == self::DECODE ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	    $string_length = strlen($string);
	    
	    $result = '';
	    $box = range(0, 255);
	    
	    $rndkey = array();
	    for($i = 0; $i <= 255; $i++) {
	        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
	    }
	    
	    for($j = $i = 0; $i < 256; $i++) {
	        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
	        $tmp = $box[$i];
	        $box[$i] = $box[$j];
	        $box[$j] = $tmp;
	    }
	    
	    for($a = $j = $i = 0; $i < $string_length; $i++) {
	        $a = ($a + 1) % 256;
	        $j = ($j + $box[$a]) % 256;
	        $tmp = $box[$a];
	        $box[$a] = $box[$j];
	        $box[$j] = $tmp;
	        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	    }
	    if($operation == self::DECODE) {
	        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0)) {
	        	//return substr($result, 26);
	            return base64_decode(substr($result, 26));
	        } else {
	            return '';
	        }
	    } else {
	        return $keyc.base64_encode($result);
	    }
	}
}
?>