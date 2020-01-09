/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50723
Source Host           : localhost:3306
Source Database       : information

Target Server Type    : MYSQL
Target Server Version : 50723
File Encoding         : 65001

Date: 2020-01-09 17:05:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL,
  `create_at` int(11) NOT NULL,
  `source` tinyint(3) NOT NULL,
  `token` char(32) NOT NULL,
  `last_login_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin_users
-- ----------------------------
INSERT INTO `admin_users` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '10000000', '1', 'a9003c3c0e161d7ee77d162d00fd3e2a', '1578558616');

-- ----------------------------
-- Table structure for group
-- ----------------------------
DROP TABLE IF EXISTS `group`;
CREATE TABLE `group` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_manager` tinyint(3) NOT NULL DEFAULT '0',
  `create_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of group
-- ----------------------------
INSERT INTO `group` VALUES ('1', 'admin管理员', '1', '0');

-- ----------------------------
-- Table structure for group_user
-- ----------------------------
DROP TABLE IF EXISTS `group_user`;
CREATE TABLE `group_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_user_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `create_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of group_user
-- ----------------------------
INSERT INTO `group_user` VALUES ('1', '1', '1', '1000000');

-- ----------------------------
-- Table structure for login_log
-- ----------------------------
DROP TABLE IF EXISTS `login_log`;
CREATE TABLE `login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_user_id` int(11) NOT NULL,
  `ip` int(11) NOT NULL,
  `create_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of login_log
-- ----------------------------
INSERT INTO `login_log` VALUES ('1', '1', '2130706433', '1578474283');
INSERT INTO `login_log` VALUES ('2', '1', '2130706433', '1578474851');
INSERT INTO `login_log` VALUES ('3', '1', '2130706433', '1578555165');
INSERT INTO `login_log` VALUES ('4', '1', '2130706433', '1578558616');

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` bigint(20) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `level` tinyint(3) NOT NULL DEFAULT '0',
  `pid` tinyint(3) NOT NULL DEFAULT '0',
  `is_menu` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', '101', '后台', '', '1', '0', '0');
INSERT INTO `menu` VALUES ('2', '10101', '首页', 'index.html', '2', '1', '1');
INSERT INTO `menu` VALUES ('3', '10102', '发布', 'release.html', '2', '1', '1');
INSERT INTO `menu` VALUES ('4', '102', '管理', '', '1', '0', '0');
INSERT INTO `menu` VALUES ('5', '10201', '分类管理', 'category.html', '2', '4', '1');
INSERT INTO `menu` VALUES ('6', '10202', '内容管理', 'content.html', '2', '4', '1');
INSERT INTO `menu` VALUES ('7', '103', '数据', '', '1', '0', '0');
INSERT INTO `menu` VALUES ('8', '10301', '阅读数据', 'subscribe.html', '2', '7', '1');
INSERT INTO `menu` VALUES ('9', '104', '设置', '', '1', '0', '0');
INSERT INTO `menu` VALUES ('10', '10401', '账号信息', 'info.html', '2', '9', '1');

-- ----------------------------
-- Table structure for permission
-- ----------------------------
DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL DEFAULT '0',
  `uri` varchar(255) NOT NULL DEFAULT '',
  `short_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of permission
-- ----------------------------
