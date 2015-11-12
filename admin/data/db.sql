/*
SQLyog Enterprise - MySQL GUI v5.19
Host - 5.1.23-rc-log : Database - ucenter
*********************************************************************
Server version : 5.1.23-rc-log
*/

SET NAMES utf8;

SET SQL_MODE='';

create database if not exists `ucenter`;

USE `ucenter`;

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `admin_info` */

DROP TABLE IF EXISTS `admin_info`;

CREATE TABLE `admin_info` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `admin_name` varchar(20) DEFAULT NULL COMMENT '¹ÜÀíÔ±ÓÃ»§Ãû',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='¹ÜÀíÔ±';

/*Data for the table `admin_info` */

/*Table structure for table `application` */

DROP TABLE IF EXISTS `application`;

CREATE TABLE `application` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `app_name` varchar(50) DEFAULT NULL COMMENT 'Ó¦ÓÃÃû³Æ',
  `api_addr` varchar(255) DEFAULT NULL COMMENT '»Øµ÷APIµØÖ·',
  `create_date` datetime DEFAULT NULL COMMENT '´´½¨ÈÕÆÚ',
  `is_band` tinyint(4) DEFAULT NULL COMMENT 'ÊÇ·ñ¿ªÆôIP°ó¶¨',
  `ip_band` text COMMENT 'IP°ó¶¨',
  `memo` varchar(255) DEFAULT NULL COMMENT '±¸×¢',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Ó¦ÓÃ³ÌÐò£¨×ÓÏµÍ³£©';

/*Data for the table `application` */

/*Table structure for table `base_group` */

DROP TABLE IF EXISTS `base_group`;

CREATE TABLE `base_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_name` varchar(20) DEFAULT NULL COMMENT '×éÃû\n            ',
  `memo` varchar(255) DEFAULT NULL COMMENT '±¸×¢',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ÓÃ»§×é';

/*Data for the table `base_group` */

/*Table structure for table `department` */

DROP TABLE IF EXISTS `department`;

CREATE TABLE `department` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(20) DEFAULT NULL COMMENT '²¿ÃÅÃû',
  `parent_id` int(11) DEFAULT NULL COMMENT 'ÉÏ¼¶²¿ÃÅ',
  `director` int(11) DEFAULT NULL COMMENT '¸¶ÔðÈË',
  `memo` varchar(255) DEFAULT NULL COMMENT 'memo',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COMMENT='²¿ÃÅ';

/*Data for the table `department` */

insert into `department` (`id`,`name`,`parent_id`,`director`,`memo`) values (1,'ÔËÎ¬ÖÐÐÄ',0,0,NULL),(2,'ÏµÍ³¹ÜÀí²¿',1,0,NULL),(3,'ÓªÏúÖÐÐÄ',0,0,'ÓªÏúÖÐÐÄ'),(4,'µØÃæÍÆ¹ã²¿',3,0,'µØÃæÍÆ¹ã²¿');

/*Table structure for table `group_app` */

DROP TABLE IF EXISTS `group_app`;

CREATE TABLE `group_app` (
  `group_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL COMMENT 'Ó¦ÓÃID',
  PRIMARY KEY (`group_id`,`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='×é¿ÉÒÔ·ÃÎÊµÄAPP';

/*Data for the table `group_app` */

/*Table structure for table `member` */

DROP TABLE IF EXISTS `member`;

CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `real_name` varchar(20) DEFAULT NULL COMMENT 'Ô±¹¤ÐÕÃû',
  `sex` tinyint(4) DEFAULT NULL COMMENT 'ÐÔ±ð \n            1£ºÅ®\n            0£ºÄÐ     ',
  `birthday` date DEFAULT NULL COMMENT '³öÉúÈÕÆÚ',
  `dep_id` int(11) DEFAULT NULL COMMENT '²¿ÃÅID',
  `position` varchar(20) DEFAULT NULL COMMENT 'Ö°Î»',
  `mobile` varchar(12) DEFAULT NULL COMMENT 'ÊÖ»ú',
  `tel` varchar(12) DEFAULT NULL COMMENT '°ì¹«µç»°',
  `memo` varchar(255) DEFAULT NULL COMMENT 'memo',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COMMENT='Ô±¹¤ÐÅÏ¢';

/*Data for the table `member` */

insert into `member` (`id`,`real_name`,`sex`,`birthday`,`dep_id`,`position`,`mobile`,`tel`,`memo`) values (1,'Â×Ö¾¾ý',1,'1985-12-10',2,'¿ª·¢','1300000000','13000000','É­'),(2,'ËÕÁ¢´æ',1,'1985-12-10',2,'¿ª·¢','189000000','189000000','XXX'),(3,'ÑîÎ°Ã÷',1,'1985-12-10',2,'¿ª·¢','1300000000','189000000','É­'),(6,'ÉòÍþ',1,'0000-00-00',2,'','000000','000000',''),(7,'ÕÅ¹ú¸¶',1,'0000-00-00',2,'','','',''),(8,'ËÎÁ¢¹ú',1,'0000-00-00',2,NULL,NULL,NULL,'ÀÏËÎ'),(13,'ÎÒÃÇ¿ªÊ¼',1,'1985-12-10',1,'position','1300000000','000000','memo');

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `k` varchar(20) NOT NULL COMMENT 'ÉèÖÃ¼ü',
  `v` text COMMENT 'ÉèÖÃÖµ',
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ÅäÖÃÐÅÏ¢';

/*Data for the table `settings` */

insert into `settings` (`k`,`v`) values ('ip_band','127.0.0.1\r\n192.168.8.54'),('is_band','0');

/*Table structure for table `user_base` */

DROP TABLE IF EXISTS `user_base`;

CREATE TABLE `user_base` (
  `id` int(11) NOT NULL COMMENT 'ÓÃ»§ID',
  `name` varchar(20) DEFAULT NULL COMMENT 'ÓÃ»§Ãû',
  `password` varchar(32) DEFAULT NULL COMMENT 'ÃÜÂë\n            ',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email',
  `last_login_time` datetime DEFAULT NULL COMMENT '×îºóµÇÂ¼Ê±¼ä',
  `last_login_ip` varchar(20) DEFAULT NULL COMMENT '×îºóµÇÂ¼IP',
  `login_times` int(11) DEFAULT NULL COMMENT 'µÇÂ¼´ÎÊý',
  `ip_band` text COMMENT 'IP°ó¶¨',
  `enable_band_ip` int(11) DEFAULT NULL COMMENT 'ÊÇ·ñ¿ªÆôIP°ó¶¨\n            0 ²»¿ªÆô£»1 ¿ªÆô',
  `pas_sou` varchar(6) DEFAULT NULL COMMENT 'ÃÜÂëÔ­ÎÄ',
  `memo` varchar(255) DEFAULT NULL COMMENT '±¸×¢',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ÓÃ»§';

/*Data for the table `user_base` */

insert into `user_base` (`id`,`name`,`password`,`email`,`last_login_time`,`last_login_ip`,`login_times`,`ip_band`,`enable_band_ip`,`pas_sou`,`memo`) values (6,'ÉòÍþ','9133ad8fdc174082a430bd9cd047809c',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL),(13,'ÎÒÃÇ¿ªÊ¼','08ae1dbee819380e29ba41ac7ebfecc0',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL);

/*Table structure for table `user_group` */

DROP TABLE IF EXISTS `user_group`;

CREATE TABLE `user_group` (
  `user_id` int(11) NOT NULL COMMENT 'ÓÃ»§ID',
  `group_id` int(11) NOT NULL COMMENT 'ÓÃ»§×éID',
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ÓÃ»§ËùÊô×é';

/*Data for the table `user_group` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
