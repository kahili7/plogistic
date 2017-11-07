/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;



DROP TABLE IF EXISTS `_constants`;
CREATE TABLE `_constants` (
  `rid` int(12) NOT NULL auto_increment,
  `code` varchar(20) default NULL,
  `name` varchar(255) default NULL,
  `descr` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` datetime default NULL,
  `modifyDT` datetime default NULL,
  `modifier_users_rid` int(12) default NULL,
  `archive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`rid`),
  KEY `FK__constants_1` (`modifier_users_rid`),
  KEY `FK__constants_2` (`owner_users_rid`),
  CONSTRAINT `FK__constants_1` FOREIGN KEY (`modifier_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__constants_2` FOREIGN KEY (`owner_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `_constants` WRITE;
/*!40000 ALTER TABLE `_constants` DISABLE KEYS */;
INSERT INTO `_constants` VALUES (1,'COMPANY_NAME','Название компании','',1,'2009-07-10 11:22:07','2009-08-24 23:24:12',1,0),
(2,'DIRECTOR','ФИО директора','',1,'2009-07-11 14:31:43','2009-07-11 14:31:43',1,0);
/*!40000 ALTER TABLE `_constants` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS `_emp_to_positions_headers`;
CREATE TABLE `_emp_to_positions_headers` (
  `rid` int(12) NOT NULL auto_increment,
  `_objects_rid` int(12) default NULL,
  `date_obj` date default NULL,
  `descr` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` datetime default NULL,
  `modifyDT` datetime default NULL,
  `modifier_users_rid` int(12) default NULL,
  `archive` tinyint(1) default '0',
  PRIMARY KEY  (`rid`),
  KEY `FK__emp_to_positions_headers1` (`_objects_rid`),
  KEY `FK__emp_to_positions_headers2` (`modifier_users_rid`),
  KEY `FK__emp_to_positions_headers3` (`owner_users_rid`),
  KEY `NewIndex1` (`date_obj`),
  CONSTRAINT `FK__emp_to_positions_headers1` FOREIGN KEY (`_objects_rid`) REFERENCES `_objects` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__emp_to_positions_headers2` FOREIGN KEY (`modifier_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__emp_to_positions_headers3` FOREIGN KEY (`owner_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



DROP TABLE IF EXISTS `_emp_to_positions_rows`;
CREATE TABLE `_emp_to_positions_rows` (
  `rid` int(12) NOT NULL auto_increment,
  `_emp_to_positions_headers_rid` int(12) default NULL,
  `_employeers_rid` int(12) default NULL,
  `_positions_rid` int(12) default NULL,
  `_filials_rid` int(12) default NULL,
  `descr` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` datetime default NULL,
  `modifyDT` datetime default NULL,
  `modifier_users_rid` int(12) default NULL,
  `archive` tinyint(1) default '0',
  `bdate` date NOT NULL,
  PRIMARY KEY  (`rid`),
  KEY `FK__emp_to_positions_rows1` (`owner_users_rid`),
  KEY `FK__emp_to_positions_rows2` (`modifier_users_rid`),
  KEY `FK__emp_to_positions_rows3` (`_employeers_rid`),
  KEY `FK__emp_to_positions_rows4` (`_emp_to_positions_headers_rid`),
  KEY `FK__emp_to_positions_rows5` (`_positions_rid`),
  CONSTRAINT `FK__emp_to_positions_rows1` FOREIGN KEY (`owner_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__emp_to_positions_rows2` FOREIGN KEY (`modifier_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__emp_to_positions_rows3` FOREIGN KEY (`_employeers_rid`) REFERENCES `_employeers` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__emp_to_positions_rows4` FOREIGN KEY (`_emp_to_positions_headers_rid`) REFERENCES `_emp_to_positions_headers` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__emp_to_positions_rows5` FOREIGN KEY (`_positions_rid`) REFERENCES `_positions` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `_emp_to_positions_rows` WRITE;
/*!40000 ALTER TABLE `_emp_to_positions_rows` DISABLE KEYS */;
INSERT INTO `_emp_to_positions_rows` VALUES (1,1,1,1,1,NULL,1,'2008-01-01 00:00:00','2009-07-29 13:06:41',1,0,'2008-01-01');
/*!40000 ALTER TABLE `_emp_to_positions_rows` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS `_employeers`;
CREATE TABLE `_employeers` (
  `rid` int(11) NOT NULL auto_increment,
  `f_name` varchar(255) default NULL,
  `s_name` varchar(255) default NULL,
  `l_name` varchar(255) default NULL,
  `f_name_lat` varchar(255) default NULL,
  `s_name_lat` varchar(255) default NULL,
  `l_name_lat` varchar(255) default NULL,
  `birthday` date default NULL,
  `phone` varchar(50) default NULL,
  `email` varchar(128) default NULL,
  `descr` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` datetime default NULL,
  `modifyDT` datetime default NULL,
  `modifier_users_rid` int(12) default NULL,
  `archive` tinyint(1) default NULL,
  `is_legal` tinyint(4) default NULL,
  PRIMARY KEY  (`rid`),
  KEY `FK__employeers1` (`modifier_users_rid`),
  KEY `FK__employeers2` (`owner_users_rid`),
  CONSTRAINT `FK__employeers1` FOREIGN KEY (`modifier_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__employeers2` FOREIGN KEY (`owner_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `_employeers` WRITE;
/*!40000 ALTER TABLE `_employeers` DISABLE KEYS */;
INSERT INTO `_employeers` VALUES (1,'--','--','Admin','--','Admin','--','1979-07-17','','kahili@yandex.ru','Администратор CRM',1,'2008-01-11 18:15:18','2009-08-02 18:18:31',1,0,0);
/*!40000 ALTER TABLE `_employeers` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS `_filials`;
CREATE TABLE `_filials` (
  `rid` int(12) NOT NULL auto_increment,
  `_cities_rid` int(12) default NULL,
  `code` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `adress` varchar(255) default NULL,
  `phones` varchar(255) default NULL,
  `fax` varchar(255) default NULL,
  `mobile_phones` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `descr` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  `archive` tinyint(1) default '0',
  PRIMARY KEY  (`rid`),
  KEY `FK__filials1` (`_cities_rid`),
  KEY `FK__filials2` (`modifier_users_rid`),
  KEY `FK__filials3` (`owner_users_rid`),
  CONSTRAINT `FK__filials1` FOREIGN KEY (`_cities_rid`) REFERENCES `_cities` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__filials2` FOREIGN KEY (`modifier_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__filials3` FOREIGN KEY (`owner_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `_filials` WRITE;
/*!40000 ALTER TABLE `_filials` DISABLE KEYS */;
INSERT INTO `_filials` VALUES (1,1,'ЦО','Центральный офис','','','','','','',1,'2008-01-11 10:02:55','2009-07-29 09:44:45',1,0);
/*!40000 ALTER TABLE `_filials` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS `_modules`;
CREATE TABLE `_modules` (
  `rid` int(12) NOT NULL auto_increment,
  `module_name` varchar(150) default NULL,
  `module_controller` varchar(150) default NULL,
  `descr` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  `archive` tinyint(1) default '0',
  PRIMARY KEY  (`rid`),
  UNIQUE KEY `item_name` (`module_name`,`module_controller`),
  KEY `FK__menu_items1` (`modifier_users_rid`),
  KEY `FK__menu_items2` (`owner_users_rid`),
  CONSTRAINT `FK__modules_2` FOREIGN KEY (`owner_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__modules_1` FOREIGN KEY (`modifier_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

LOCK TABLES `_modules` WRITE;
/*!40000 ALTER TABLE `_modules` DISABLE KEYS */;
INSERT INTO `_modules` VALUES (1,'Склады','warehouse','Справочник складов',1,'2009-08-23 16:49:34','2009-08-23 22:05:32',1,0),
(2,'Константы','constants','Справочник констант',1,'2009-08-23 16:50:25','2009-08-24 18:43:17',1,0),
(3,'Группы продуктов','groupsystem','Справочник групп систем',1,'2009-08-23 16:50:48','2009-08-23 22:06:29',1,0),
(4,'Сотрудники','employeers','Справочник сотрудников',1,'2009-08-23 16:53:10','2009-08-23 22:07:49',1,0),
(5,'Назначения сотрудников','emptopositions','Документ назначения сотрудников',1,'2009-08-23 16:53:44','2009-08-23 22:08:07',1,0),
(6,'Филиалы','filials','Справочник филиалов',1,'2009-08-23 16:54:27','2009-08-23 22:08:27',1,0),
(7,'Модули','modules','Модули системы',1,'2009-08-23 16:57:39','2009-08-23 22:11:47',1,0),
(8,'Должности','positions','Справочник должностей',1,'2009-08-23 16:58:05','2009-08-23 22:11:24',1,0),
(9,'Меню должностей','positionsmenu','Справочник меню для должностей',1,'2009-08-23 16:58:44','2009-08-23 22:11:04',1,0),
(10,'Клиенты','client','Справочник клиентов',1,'2009-08-23 17:03:24','2009-08-23 19:04:47',1,0),
(11,'Пользователи','users','Справочник пользователей',1,'2009-08-23 17:03:24','2009-08-23 19:04:47',1,0),
(12,'Импорт','import','Импорт',1,'2009-08-23 17:03:24','2009-08-23 19:04:47',1,0),
(13,'Элементы','tableall','Элементы',1,'2009-08-23 17:03:24','2009-08-23 19:04:47',1,0),
(14,'Приходы','tablein','Приходы',1,'2009-08-23 17:03:24','2009-08-23 19:04:47',1,0);
/*!40000 ALTER TABLE `_modules` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS `_modules_permissions`;
CREATE TABLE `_modules_permissions` (
  `rid` int(12) NOT NULL auto_increment,
  `_modules_rid` int(12) default NULL,
  `_positions_rid` int(12) default NULL,
  `add_allow` tinyint(1) default '0',
  `edit_allow` tinyint(1) default '0',
  `details_allow` tinyint(1) default '0',
  `delete_allow` tinyint(1) default '0',
  `move_allow` tinyint(1) default '0',
  `archive_allow` tinyint(1) default '0',
  `viewed_space` varchar(10) default NULL,
  `descr` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  `archive` tinyint(1) default '0',
  PRIMARY KEY  (`rid`),
  KEY `FK__modules_permissions_1` (`modifier_users_rid`),
  KEY `FK__modules_permissions_2` (`owner_users_rid`),
  KEY `FK__modules_permissions_3` (`_modules_rid`),
  KEY `FK__modules_permissions_4` (`_positions_rid`),
  CONSTRAINT `FK__modules_permissions_1` FOREIGN KEY (`modifier_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__modules_permissions_2` FOREIGN KEY (`owner_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__modules_permissions_3` FOREIGN KEY (`_modules_rid`) REFERENCES `_modules` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__modules_permissions_4` FOREIGN KEY (`_positions_rid`) REFERENCES `_positions` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

LOCK TABLES `_modules_permissions` WRITE;
/*!40000 ALTER TABLE `_modules_permissions` DISABLE KEYS */;
INSERT INTO `_modules_permissions` VALUES (1,1,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(2,2,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(3,3,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(4,4,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(5,5,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(6,6,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(7,7,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(8,8,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(9,9,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(10,10,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(11,11,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(12,12,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(13,13,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0),
(14,14,1,1,1,1,1,1,1,'ALL',NULL,NULL,'2009-08-23 19:04:47',NULL,NULL,0);
/*!40000 ALTER TABLE `_modules_permissions` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS `_positions`;
CREATE TABLE `_positions` (
  `rid` int(12) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `descr` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` datetime default NULL,
  `modifyDT` datetime default NULL,
  `modifier_users_rid` int(12) default NULL,
  `archive` tinyint(1) default '0',
  PRIMARY KEY  (`rid`),
  UNIQUE KEY `name` (`name`),
  KEY `FK__positions1` (`modifier_users_rid`),
  KEY `FK__positions2` (`owner_users_rid`),
  CONSTRAINT `FK__positions1` FOREIGN KEY (`modifier_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__positions2` FOREIGN KEY (`owner_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `_positions` WRITE;
/*!40000 ALTER TABLE `_positions` DISABLE KEYS */;
INSERT INTO `_positions` VALUES (1,'Администратор',NULL,1,'2007-11-13 16:02:28','2007-11-13 16:02:28',1,0),
(2,'Директор',NULL,1,'2008-01-04 14:20:25','2008-01-04 14:20:25',1,0),
(3,'Коммерческий директор','',1,'2008-01-04 14:20:01','2008-01-04 14:20:01',1,0),
(4,'Менеджер по продажам','',1,'2008-01-24 11:00:40','2008-01-24 11:00:40',1,0),
(5,'Маркетолог','Старший менеджер филиала',1,'2008-02-12 11:03:26','2008-02-12 11:03:26',1,0);
/*!40000 ALTER TABLE `_positions` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS `_positions_menus`;
CREATE TABLE `_positions_menus` (
  `rid` int(12) NOT NULL auto_increment,
  `_positions_rid` int(12) default NULL,
  `_modules_rid` int(12) default NULL,
  `item_name` varchar(255) default NULL,
  `parent` int(12) default NULL,
  `item_order` int(11) default '0',
  `descr` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` datetime default NULL,
  `modifyDT` datetime default NULL,
  `modifier_users_rid` int(12) default NULL,
  `archive` tinyint(1) default '0',
  PRIMARY KEY  (`rid`),
  UNIQUE KEY `p1` (`_positions_rid`,`_modules_rid`),
  KEY `_positions_rid` (`_positions_rid`),
  KEY `FK__positions_menus3` (`modifier_users_rid`),
  KEY `FK__positions_menus1` (`owner_users_rid`),
  KEY `FK__positions_menus_4` (`_modules_rid`),
  CONSTRAINT `FK__positions_menus1` FOREIGN KEY (`owner_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__positions_menus2` FOREIGN KEY (`_positions_rid`) REFERENCES `_positions` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__positions_menus3` FOREIGN KEY (`modifier_users_rid`) REFERENCES `_users` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__positions_menus_4` FOREIGN KEY (`_modules_rid`) REFERENCES `_modules` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `_positions_menus` WRITE;
/*!40000 ALTER TABLE `_positions_menus` DISABLE KEYS */;
INSERT INTO `_positions_menus` VALUES (1,1,NULL,'Администрирование',0,0,'',1,'2007-12-08 19:01:42','2009-08-24 12:09:11',1,0),
(2,1,2,'Константы',1,0,'',1,'2009-07-08 12:15:53','2009-08-24 12:19:26',1,0),
(3,1,9,'Меню должностей',1,0,'',1,'2007-12-08 18:53:30','2009-08-24 12:10:24',1,0),
(4,1,7,'Модули',1,0,'',1,'2009-08-23 18:17:47','2009-08-24 12:15:58',1,0),
(5,1,11,'Пользователи',1,0,'',1,'2007-11-21 14:52:42','2009-08-24 12:15:22',1,0),

(6,1,NULL,'Сотрудники',0,0,'',1,'2007-11-29 15:07:34','2009-08-24 12:11:52',1,0),
(7,1,8,'Должности',6,0,'',1,'2007-12-08 19:01:01','2009-08-24 12:12:42',1,0),
(8,1,5,'Назначения сотрудников',6,0,'',1,'2007-12-05 18:43:26','2009-09-02 15:26:27',1,0),
(9,1,4,'Сотрудники',6,0,'Справочник сотрудников',1,'2007-12-10 18:21:22','2009-08-24 12:12:17',1,0),

(10,1,NULL,'Логистика',0,0,'',1,'2007-12-11 10:31:51','2009-08-24 12:07:09',1,0),
(11,1,1,'Склады',10,0,'',1,'2007-12-08 19:00:43','2009-08-24 12:10:43',1,0),
(12,1,3,'Группы продуктов',10,0,'',1,'2007-12-08 19:01:26','2009-08-24 12:11:27',1,0),
(13,1,10,'Клиенты',10,0,'',1,'2007-12-08 19:01:17','2009-08-24 12:11:07',1,0),
(14,1,6,'Филиалы',10,0,'',1,'2007-12-08 19:00:47','2009-08-24 12:13:08',1,0),
(15,1,12,'Импорт',10,0,'',1,'2007-12-08 19:01:17','2009-08-24 12:11:07',1,0),
(16,1,NULL,'На складе',10,0,'',1,'2007-12-08 19:00:43','2009-08-24 12:10:43',1,0),
(17,1,13,'Элементы',16,0,'',1,'2007-12-08 19:00:47','2009-08-24 12:13:08',1,0),
(18,1,14,'Приход',16,0,'',1,'2007-12-08 19:01:17','2009-08-24 12:11:07',1,0);
/*!40000 ALTER TABLE `_positions_menus` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS `_sessions`;
CREATE TABLE `_sessions` (
  `session_id` varchar(40) NOT NULL default '0',
  `ip_address` varchar(16) NOT NULL default '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `session_data` text,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `_users`;
CREATE TABLE `_users` (
  `rid` int(12) NOT NULL auto_increment,
  `_employeers_rid` int(12) default NULL,
  `user_login` varchar(20) default NULL,
  `user_passwd` varchar(255) default NULL,
  `edate_passwd` timestamp NULL default NULL,
  `chdate_passwd` timestamp NULL default NULL,
  `descr` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  `archive` tinyint(1) default '0',
  PRIMARY KEY  (`rid`),
  UNIQUE KEY `IX__users_1` (`user_login`),
  KEY `_employeers_rid` (`_employeers_rid`),
  CONSTRAINT `FK__users1` FOREIGN KEY (`_employeers_rid`) REFERENCES `_employeers` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `_users` WRITE;
/*!40000 ALTER TABLE `_users` DISABLE KEYS */;
INSERT INTO `_users` VALUES (1,1,'admin','admin','2019-12-31 22:00:00','2009-10-04 21:00:00','Администратор системы',1,'2007-11-28 11:55:12','2009-07-29 10:11:35',1,0);
/*!40000 ALTER TABLE `_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;



DROP TABLE IF EXISTS `_warehouse`;
CREATE TABLE `_warehouse` (
  `rid_wh` int(12) NOT NULL auto_increment,
  `_id_wh` varchar(12) default NULL,
  `name_wh` varchar(200) default NULL,
  `descr_wh` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  PRIMARY KEY  (`rid_wh`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



DROP TABLE IF EXISTS `_client`;
CREATE TABLE `_client` (
  `rid_cl` int(12) NOT NULL auto_increment,
  `_id_client` varchar(12) default NULL,
  `name_cl` varchar(100) default NULL,
  `descr_cl` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  PRIMARY KEY  (`rid_cl`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



DROP TABLE IF EXISTS `_groupsystem`;
CREATE TABLE `_groupsystem` (
  `rid_gs` int(12) NOT NULL auto_increment,
  `_id_gs` varchar(5) default NULL,
  `name_gs` varchar(200) default NULL,
  `descr_gs` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  PRIMARY KEY  (`rid_gs`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



DROP TABLE IF EXISTS `_art_nr`;
CREATE TABLE `_art_nr` (
  `rid_art` int(12) NOT NULL auto_increment,
  `_id_art` varchar(12) default NULL,
  `name_art` varchar(100) default NULL,
  `descr_art` text,
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  PRIMARY KEY  (`rid_art`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



DROP TABLE IF EXISTS `_table_all`;
CREATE TABLE `_table_all` (
  `rid_ta` int(12) NOT NULL auto_increment,
  `_id_wh` varchar(12) default NULL,
  `_id_gs` varchar(5) default NULL,
  `art_nr` varchar(12) default NULL,
  `name` varchar(200) default NULL,
  `svobodno` varchar(12) default NULL,
  `vsevo_na_sklade` varchar(12) default NULL,
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  PRIMARY KEY  (`rid_ta`),
  KEY `art_nr` (`art_nr`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



DROP TABLE IF EXISTS `_table_zakaz`;
CREATE TABLE `_table_zakaz` (
  `rid_tr` int(12) NOT NULL auto_increment,
  `_id_wh` varchar(12) default NULL,
  `art_nr` varchar(12) default NULL,
  `_id_client` varchar(12) default NULL,
  `zakaz_date` varchar(10) default '00.00.0000',
  `zakaz_nr` varchar(10) default NULL,
  `zakaz_pos` varchar(4) default NULL,
  `zakaz_flag` varchar(10) default '-',
  `zakaz_count` varchar(12) default NULL,
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  PRIMARY KEY  (`rid_tr`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



DROP TABLE IF EXISTS `_table_prixod`;
CREATE TABLE `_table_prixod` (
  `rid_tp` int(12) NOT NULL auto_increment,
  `_id_wh` varchar(12) default NULL,
  `art_nr` varchar(12) default NULL,
  `_id_client` varchar(12) default NULL,
  `prixod_date` varchar(10) default '00.00.0000',
  `prixod_nr` varchar(10) default NULL,
  `prixod_pos` varchar(4) default NULL,
  `prixod_count` varchar(12) default NULL,
  `prixod_flag` varchar(10) default '-',
  `owner_users_rid` int(12) default NULL,
  `createDT` timestamp NULL default CURRENT_TIMESTAMP,
  `modifyDT` timestamp NULL default NULL,
  `modifier_users_rid` int(12) default NULL,
  PRIMARY KEY  (`rid_tp`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;