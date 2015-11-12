<?php
/**
 * API 参数声名
 * @author:d-boy
 * @copyright:$Id$
 */
 class ParamKey{
 	const key_page = 'page'; //分页页码参数
    const key_pagesize = 'pagesize'; //分页页面大小参数
    const key_uid = 'uid' ; //用户ID 参数
    const key_email = 'email' ; //email 参数
    const key_password = 'password' ; //密码 参数
    const key_user_name = 'user_name';//参数 用户名
    const key_expir = 'expir'; //参数，过期时间
    const key_app = 'app'; //参数 应用ID
    const key_ip = 'ip'; //参数 客户端IP
    const key_forward = 'forward';// 参数 下一页面URL
    const key_todo = 'do' ;//操作参数
    const key_token = 'token'; //参数 APP 口令
    const communications_serurity = 'g@y!y(x' ; //默认通信加密KEY
    const key_action = 'a'; //通信参数 ACTION
    const key_model = 'm'; //通信参数　MODEL
    const key_trigger = 'trigger'; //返回信息中，同步触发器的键 
    const SYNC_LOGIN = 'sync_login';
    const SYNC_LOGOUT = 'sync_logout';
    const SYNC_USER = 'sync_user';
    const SYNC_DELUSER = 'sync_deluser';
    const FLAG_FLAG = 'flag' ;//返回结果标志
    const FLAG_MSG = 'msg' ; //返回结果的异常和提示等信息
    const FLAG_ERR = 'err'; //FLAG 错误
    const FLAG_OK = 'ok' ; //处理成功
    const FLAG_USER = 'user'; //USER
    
 }
?>