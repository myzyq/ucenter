<?php
/**
 * API 操作接口实现
 * @author d-boy
 * @copyright $Id$
 */
class ClientApiImp implements ClientApiInter{
	private $enkey ; //自定义的加密方式的KEY
	private $cookiekey ; //COOKIE KEY
	
	public function __construct(){
        $this->enkey = md5(APPTOKEN);
        $this->cookiekey = COOKIE_NAME;    
        include_once API_PATH . "/../common/common.fun.php" ;   
	}
	
    /**
     * 同步登录状态接口实现
     * @param $uid 用户ID
     * @param $password 用户密码
     * @param $expri COOKIE 过期时间和现在的距离 0的话就是关浏览器过期
     * @return 要输出的信息
     */
	public function sync_login($uid = 0, $password = '', $expri = 0) {
    	if(empty($uid) || empty($password)) return "err. the param is none";
    	
    	//写COOKIE
    	$val = $uid ."|".$password ;
    	$expri = empty($expri) ? 0 : intval($expri) * 24 * 60 * 60;
        set_cookie($this->cookiekey, $val, $expri);
    	return 'ok';
    }
    
    /**
     * 同步注销状态
     */
    public function sync_logout(){
    	//写COOKIE
        set_cookie($this->cookiekey, "", -3000000);
        return 'ok';
    }
    
    /**
     * 同步用户信息
     * @param $user 用户信息
     */
    public function sync_user($user = array()){
    	//do save or update
    	$array = var_export($user, true);
    	file_put_contents("log", $array, FILE_APPEND);
    	
    	return 'ok';
    }
    
    /**
     * 同步删除用户
     * @param $uid 用户ID
     */
    public function sync_deluser($uid = 0) {
    	// do delete
    	return 'ok';
    }
}
?>