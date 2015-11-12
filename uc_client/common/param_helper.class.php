<?php
/**
 * 通信参数Helper
 * @author d-boy
 * $Id$
 */ 
class ParamHelper{
	private $param ; //参数分解后的信息
	private $paramstr ; //参数串
	private $key; //通信密钥
	private $coder ; //常用编码类
	
	/**
	 * 构造函数
	 * @param string $todo 参数串
	 */
	public function __construct($key){
		$this->param = array();
		$this->coder = new UCCode();
		$this->set_key($key);
	}
	
	public function get_param(){
		return $this->param;
	}
	
	/**
	 * 设置KEY
	 * @param $key 加密的KEY
	 */
	public function set_key($key = '') {
		$this->key = empty($key) ? ParamKey::communications_serurity : $key ;
	}
	
	/**
	 * 设置参数串(加密后的)
	 * @param string $paramstr 参数串
	 */
	public function set_paramstr($paramstr = ''){
		$this->paramstr = $paramstr;
	}
	
	/**
	 * 设置参数列表
	 * @param array $param 参数列表
	 */
	public function set_param($param = array()){
		$this->param = $param;
	}
	
	/**
	 * MD5 加密串
	 * @param  $str 要加密的串
	 * @return MD5加密后的串
	 */
	public function md5($str) {
		return $this->coder->md5($str);
	}
	
	/**
	 * 解密参数串
	 * @reutrn 解密后的串
	 */
    public function decode_param(){
        if (empty($this->paramstr)) return '';
        $decode = CustomDES::encrypt_decrypt($this->paramstr, CustomDES::DECODE, $this->key);
        //echo $decode;
        return $decode;
    }
    
    /**
     * 加密参数串
     * @return 加密后的串
     */
    public function encode_param(){
    	if (empty($this->paramstr)) return '';
        $encode = CustomDES::encrypt_decrypt($this->paramstr, CustomDES::ENCODE, $this->key);
        return $encode;
    }
    
    /**
     * 将加密后的参数恢复成参数HASH表
     */
    public function recover_param(){
    	$decode = $this->decode_param();
 
    	$this->param = $this->unserialize($decode);
    	//print_r($this->param);
    }
    
    /**
     * 将HASH参数转为串
     */
    public function serialize_param() {
    	if(empty($this->param)) return '';
    	$this->paramstr = $this->serialize($this->param);
    	$str = CustomDES::encrypt_decrypt($this->paramstr, CustomDES::ENCODE, $this->key);
    	return $str;
    }
    
    /**
     * 按KEY得到参数的内容
     * @return 参数内容
     */
    public function get_param_by_key($key = ''){
    	if(empty($key)) return '';
    	if(empty($this->param) || empty($this->param[$key])) return '';
    	return $this->param[$key];
    }   
    
    /**
     * 向hash内添加参数
     * @param $key 参数KEY
     * @param $value 参数值
     */
    public function append_param($key, $value){
    	if(empty($this->param)) $this->param = array();
    	$this->param[$key] = $value;
    }
    
    /**
     * 转为BASE64编码
     * @param $string 要编码的串
     */
    public function tobase64($string = '') {
    	return $this->coder->gen_64_code($string);
    }
    
    /**
     * 将base64编码转为字符串
     * @param $code64 base64编码后的串
     */
    public function frombase64($code64 = '') {
    	return $this->coder->de_64_code($code64);
    }
    
    
    /**
     * JSON序列化对像
     * @param $code  要序列化的对像
     */
    public function serialize($code) {
    	return $this->coder->serialize($code);
    }
    
    /**
     * JSON返序列化
     * @param $str JSON串
     */
    public function unserialize($str) {
    	return $this->coder->unserialize($str);
    }
	
}

?>