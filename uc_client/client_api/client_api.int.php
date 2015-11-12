<?php
/**
 * CLIENT 为 UCENTER 提供的接口
 * @author d-boy
 * @copyright $Id$
 */
interface ClientApiInter{
	/**
	 * 同步登录状态接口
	 * @param $uid 用户ID
	 * @param $password 用户密码
	 * @param $expri COOKIE 过期时间和现在的距离 0的话就是关浏览器过期
	 * @return 要输出的信息
	 */
	public function sync_login($uid = 0, $password = '', $expri = 0);
	
	/**
	 * 同步注销状态接口
	 * @return 要输出的信息
	 */
	public function sync_logout();
	
	/**
	 *  同步用户信息的接口
	 * @param  $user 用户信息
	 * @return 要输出的信息
	 */
	public function sync_user($user = array());
	
	/**
	 * 同步删除用户
	 * @param  $uid 用户ID
	 * @return 要输出的信息
	 */
	public function sync_deluser($uid = 0);
}


?>