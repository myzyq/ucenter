<?php
/**
 * 配置文件
 * @author d-boy
 * $Id$
 */

 
//UCenter API 入口
define('UC_WEB' , 'http://ucenter.test.gyyx.cn/admin/api.php'); 

//UCENTER主数据库连接
define('UCENTER_DBHOST', '192.168.4.145'); //数据库主机
define('UCENTER_DBUSER', 'root'); //数据库用户名
define('UCENTER_DBPWD', '1q2w3e4r'); //数据库密码
define('UCENTER_DBNAME', 'ucenter_manager'); //数据库名称


define('UCENTER_TABLE_PRE', ''); //数据表前缀
define('UCENTER_DATABASE', 'mysql'); //数据库类型
define('UCENTER_PCONNECT', 0); //是否使用持久连接
define('UCENTER_DBCHARSET', 'utf8'); //数据库连接字符集


define('UCENTER_DOMAIN', '');
//备份数据库配置，连不上上面的库，就连备份
define('BK_UCENTER_DBHOST', ''); //数据库主机
define('BK_UCENTER_DBUSER', ''); //数据库用户名
define('BK_UCENTER_DBPWD', ''); //数据库密码
define('BK_UCENTER_DBNAME', ''); //数据库名称
define('BK_UCENTER_TABLE_PRE', ''); //数据表前缀
define('BK_UCENTER_DATABASE', 'mysql'); //数据库类型
define('BK_UCENTER_PCONNECT', 0); //是否使用持久连接
define('BK_UCENTER_DBCHARSET', 'utf8'); //数据库连接字符集

define('CHARSET', 'utf-8'); //网站字符集
define('COOKIE_PRE', 'ucfont');
define('COOKIE_DOMAIN', ''); //cookie 作用域
define('COOKIE_PATH', '/'); //cookie 作用路径
define('COOKIE_NAME', 'ucfont'); //cookie 名

define('SESSION_SAVEPATH', './cache/session/'); //session存储路径


define('DEBUG', 1); //启用调试信息

define('TIMEZONE', 'Etc/GMT-8'); //时区设置

define('GZIP_COMPRESS', 0); //启用GIP

define('ENABLE_ADMIN_LOG', 0); //启用后台操作日志

define('AUTH_KEY', 'ucenter_sync'); //通讯密钥
define('ROOT_PATH','/');//网站根目录

define('THEME', 'default'); //模认主题



/* 以下为应用自身的信息 */
define('APPID', 14); //app 注册ID
define('APPTOKEN', 'DBr*oj-VdX-ycYGo') ; //app 令牌

?>
