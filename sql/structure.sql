-- MySQL dump 10.16  Distrib 10.1.36-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: manager
-- ------------------------------------------------------
-- Server version	10.1.36-MariaDB

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

--
-- Table structure for table `vp_category`
--

DROP TABLE IF EXISTS `vp_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_category` (
  `category_name` varchar(32) DEFAULT NULL,
  `parent_id` int(32) DEFAULT NULL,
  `icon` varchar(32) DEFAULT NULL,
  `childs` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `category_id` int(32) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_comments`
--

DROP TABLE IF EXISTS `vp_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_comments` (
  `content` mediumtext,
  `user_tel` varchar(32) DEFAULT NULL,
  `user_name` varchar(32) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `department` varchar(32) DEFAULT NULL,
  `status` int(32) DEFAULT '1',
  `comment_id` int(32) NOT NULL AUTO_INCREMENT,
  `create_time` varchar(32) DEFAULT NULL,
  `qrcode` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_company`
--

DROP TABLE IF EXISTS `vp_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_company` (
  `company_name` varchar(255) DEFAULT NULL,
  `address` mediumtext,
  `street` varchar(255) DEFAULT NULL,
  `company_id` int(32) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1521 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_department`
--

DROP TABLE IF EXISTS `vp_department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_department` (
  `department_id` int(32) NOT NULL AUTO_INCREMENT,
  `dep_name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_group`
--

DROP TABLE IF EXISTS `vp_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_group` (
  `group_id` int(32) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_group_category`
--

DROP TABLE IF EXISTS `vp_group_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_group_category` (
  `group_category_id` int(32) NOT NULL AUTO_INCREMENT,
  `group_id` int(32) DEFAULT NULL,
  `category_id` int(32) DEFAULT NULL,
  PRIMARY KEY (`group_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_group_pri`
--

DROP TABLE IF EXISTS `vp_group_pri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_group_pri` (
  `group_pri_id` int(32) NOT NULL AUTO_INCREMENT,
  `group_id` int(32) DEFAULT NULL,
  `pri_id` int(32) DEFAULT NULL,
  PRIMARY KEY (`group_pri_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_item`
--

DROP TABLE IF EXISTS `vp_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_item` (
  `item_id` int(32) NOT NULL AUTO_INCREMENT,
  `item_no` varchar(32) DEFAULT NULL,
  `account_time` varchar(32) DEFAULT NULL,
  `account_no` varchar(32) DEFAULT NULL,
  `method` varchar(32) DEFAULT NULL,
  `get_time` varchar(32) DEFAULT NULL,
  `forward` varchar(32) DEFAULT NULL,
  `mark_time` varchar(32) DEFAULT NULL,
  `status` varchar(32) DEFAULT NULL,
  `user_id` int(32) DEFAULT NULL,
  `purpose` varchar(32) DEFAULT NULL,
  `comments` varchar(32) DEFAULT NULL,
  `create_time` varchar(32) DEFAULT NULL,
  `model_id` int(32) DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_level`
--

DROP TABLE IF EXISTS `vp_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_level` (
  `level_id` int(32) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(32) DEFAULT NULL,
  `level` int(32) DEFAULT '0',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_message`
--

DROP TABLE IF EXISTS `vp_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_message` (
  `message_id` int(32) NOT NULL AUTO_INCREMENT,
  `type` int(32) DEFAULT NULL,
  `create_time` varchar(32) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_model`
--

DROP TABLE IF EXISTS `vp_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_model` (
  `model_id` int(32) NOT NULL AUTO_INCREMENT,
  `model_name` varchar(32) DEFAULT NULL,
  `price` varchar(32) DEFAULT NULL,
  `category` varchar(32) DEFAULT NULL,
  `model_no` varchar(32) DEFAULT NULL,
  `num` int(32) DEFAULT '0',
  `account_id` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_position`
--

DROP TABLE IF EXISTS `vp_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_position` (
  `position_id` int(32) NOT NULL AUTO_INCREMENT,
  `position_name` varchar(32) DEFAULT NULL,
  `level` int(32) DEFAULT '0',
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_pri`
--

DROP TABLE IF EXISTS `vp_pri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_pri` (
  `pri_id` int(32) NOT NULL AUTO_INCREMENT,
  `pri_name` varchar(32) DEFAULT NULL,
  `module_name` varchar(32) DEFAULT NULL,
  `controller_name` varchar(32) DEFAULT NULL,
  `method` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`pri_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_roam`
--

DROP TABLE IF EXISTS `vp_roam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_roam` (
  `roam_id` int(32) NOT NULL AUTO_INCREMENT,
  `item_id` int(32) DEFAULT NULL,
  `status` int(32) DEFAULT '0',
  `create_time` varchar(32) DEFAULT NULL,
  `apply_user_id` int(32) DEFAULT NULL,
  `use_user_id` int(32) DEFAULT NULL,
  `apply_approval_user_id` int(32) DEFAULT NULL,
  `use_approval_user_id` int(32) DEFAULT NULL,
  `office_approval_user_id` int(32) DEFAULT NULL,
  `type` int(32) DEFAULT '1',
  PRIMARY KEY (`roam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_roam_end_log`
--

DROP TABLE IF EXISTS `vp_roam_end_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_roam_end_log` (
  `roam_end_log_id` int(32) NOT NULL AUTO_INCREMENT,
  `user_id` int(32) DEFAULT NULL,
  `to_user_id` int(32) DEFAULT NULL,
  `create_time` varchar(32) DEFAULT NULL,
  `item_id` int(32) DEFAULT NULL,
  PRIMARY KEY (`roam_end_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_roam_log`
--

DROP TABLE IF EXISTS `vp_roam_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_roam_log` (
  `roam_log_id` int(32) NOT NULL AUTO_INCREMENT,
  `user_id` int(32) DEFAULT NULL,
  `type` int(32) DEFAULT NULL,
  `create_time` varchar(32) DEFAULT NULL,
  `item_id` int(32) DEFAULT NULL,
  PRIMARY KEY (`roam_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_upload`
--

DROP TABLE IF EXISTS `vp_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_upload` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `time` varchar(32) DEFAULT NULL,
  `qrcode` varchar(32) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_user`
--

DROP TABLE IF EXISTS `vp_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_user` (
  `user_id` int(32) NOT NULL AUTO_INCREMENT,
  `pwd` varchar(32) DEFAULT NULL,
  `uname` varchar(32) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `last_time` varchar(32) DEFAULT NULL,
  `level_id` int(32) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_user_department`
--

DROP TABLE IF EXISTS `vp_user_department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_user_department` (
  `user_id` int(32) DEFAULT NULL,
  `department_id` int(32) DEFAULT NULL,
  `position_id` int(32) DEFAULT NULL,
  `user_department_id` int(32) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`user_department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_user_group`
--

DROP TABLE IF EXISTS `vp_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_user_group` (
  `user_group_id` int(32) NOT NULL AUTO_INCREMENT,
  `user_id` int(32) DEFAULT NULL,
  `group_id` int(32) DEFAULT NULL,
  PRIMARY KEY (`user_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_user_message`
--

DROP TABLE IF EXISTS `vp_user_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_user_message` (
  `user_message_id` int(32) NOT NULL AUTO_INCREMENT,
  `user_id` int(32) DEFAULT NULL,
  `message_id` int(32) DEFAULT NULL,
  `status` int(32) DEFAULT '0',
  `create_time` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`user_message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_user_roam`
--

DROP TABLE IF EXISTS `vp_user_roam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_user_roam` (
  `user_roam_id` int(32) NOT NULL AUTO_INCREMENT,
  `user_id` int(32) DEFAULT NULL,
  `roam_id` int(32) DEFAULT NULL,
  `create_time` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`user_roam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_zj_company`
--

DROP TABLE IF EXISTS `vp_zj_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_zj_company` (
  `zj_company_id` int(32) NOT NULL AUTO_INCREMENT,
  `company_id` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `release_date` varchar(32) DEFAULT NULL,
  `due_date` varchar(32) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `user_name` varchar(32) DEFAULT NULL,
  `user_tel` varchar(32) DEFAULT NULL,
  `status` int(32) DEFAULT '1',
  `comments` varchar(255) DEFAULT NULL,
  `is_show` int(32) DEFAULT '1',
  `origin` int(32) DEFAULT NULL,
  `qid` int(32) DEFAULT NULL,
  PRIMARY KEY (`zj_company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=232 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vp_zj_job`
--

DROP TABLE IF EXISTS `vp_zj_job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vp_zj_job` (
  `zj_job_id` int(32) NOT NULL AUTO_INCREMENT,
  `job_name` varchar(32) DEFAULT NULL,
  `age` varchar(32) DEFAULT NULL,
  `education` varchar(32) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `comments` mediumtext,
  `is_show` int(32) DEFAULT '1',
  `create_time` varchar(32) DEFAULT NULL,
  `due_time` varchar(32) DEFAULT NULL,
  `origin` int(32) DEFAULT NULL,
  `zj_company_id` int(32) DEFAULT NULL,
  `working_life` varchar(32) DEFAULT NULL,
  `money` varchar(32) DEFAULT NULL,
  `nature` varchar(32) DEFAULT NULL,
  `category_id` int(32) DEFAULT NULL,
  PRIMARY KEY (`zj_job_id`)
) ENGINE=InnoDB AUTO_INCREMENT=748 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-07-10 14:07:32
