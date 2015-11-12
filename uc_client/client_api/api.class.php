<?php 
/**
 * Client API 入口类，所有操作必经的类。
 * @author d-boy
 * @copyright $Id$
 */
class Api{
    private $paramhelper; //参数操作工具
    private $api; //实例化后的客户端操作接口实现类

    private $app; //APP ID
    private $token; //APP TOKEN
    private $param ;//URL 参数
    
    /**
     * 构造函数
     * @param  $config 配制信息 array('app'=>app, 'token'=>token, 'api'=>api, 'param' => param)
     */
    public function __construct($config = array()){
        $this->app = empty($config['app']) ? 0 : intval($config['app']); //APP ID
        $this->token = empty($config['token']) ? '' : $config['token']; //APP TOKEN
        $md5key = empty($this->token) ? '' : md5($this->token);
        $this->paramhelper = new ParamHelper($md5key);
        $this->api = $config['api'] ; //实例化后的客户端操作接口实现类 
        $this->param = $config['param'];
        //echo "param:" . $config['param'];
        $this->paramhelper->set_paramstr($this->param);
        $this->paramhelper->recover_param();
    }
    
    /**
     * 初始化函数
     * @param  $config 初始化参数
     */
    public function Api($config = array()) {
        $this->__construct($config);
    }
    
    /**
     * 运行，操作入口
     */
    public function go() {
    	$token = $this->paramhelper->get_param_by_key(ParamKey::key_token);
    	//print_r($this->paramhelper->get_param());
    	$righttoken = md5($this->token);
    	//echo "<p>token is :$token</p>";
    	//echo "<p>right is :$righttoken</p>";
    	if($token != $righttoken) {
    		echo 'err , token validate fail';
    		return;
    	}
    	
    	$this->do_option();
    }
    
    /**
     * 执行操作
     */
    private function do_option(){
    	$action = $this->paramhelper->get_param_by_key(ParamKey::key_action);
    	switch($action) {
    		case ParamKey::SYNC_LOGIN: //同步登录状态
    			echo $this->sync_login();
    			break;
    		case ParamKey::SYNC_LOGOUT: //同步注销状态
    			echo $this->sync_logout();
    			break;
    		case ParamKey::SYNC_USER: //同步用户信息
    			echo $this->sync_user();
    			break;
    		case ParamKey::SYNC_DELUSER: //同步删除用户
    			echo $this->sync_deluser();
    			break;
    		default:
    			echo "err. there is no proc named $action";
    			break;
    	}
    }    
    
    
    /**
     * 调用同步用户登录状态操作
     */
    private function sync_login(){
    	$uid = $this->paramhelper->get_param_by_key(ParamKey::key_uid);
    	$password = $this->paramhelper->get_param_by_key(ParamKey::key_password);
    	$expri = $this->paramhelper->get_param_by_key(ParamKey::key_expir);
    	return $this->api->sync_login($uid, $password, $expri);
    }
    
    /**
     * 调用注销状态同步
     */
    private function sync_logout(){
        return $this->api->sync_logout();
    }
    
    /**
     * 调用同步用户操作
     */
    private function sync_user(){    	
    	$uid = $this->paramhelper->get_param_by_key(ParamKey::key_uid);
        $password = $this->paramhelper->get_param_by_key(ParamKey::key_password);
        $username = $this->paramhelper->get_param_by_key(ParamKey::key_user_name);
        $username = str_replace("u","\\u",$username);
        echo $username;
        $email = $this->paramhelper->get_param_by_key(ParamKey::key_email);
        $user = array(
            ParamKey::key_uid => $uid,
            ParamKey::key_email => $email,
            ParamKey::key_password => $password,
            ParamKey::key_user_name => base64_decode($username)
        );
        return $this->api->sync_user($user);
    }
    
    /**
     * 调用同步删除用户操作
     */
    private function sync_deluser(){
    	$uid = $this->paramhelper->get_param_by_key(ParamKey::key_uid);
    	return $this->api->sync_deluser($uid);
    }
    
}
?>