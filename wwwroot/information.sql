/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50723
Source Host           : localhost:3306
Source Database       : information

Target Server Type    : MYSQL
Target Server Version : 50723
File Encoding         : 65001

Date: 2020-01-10 17:39:03
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
INSERT INTO `admin_users` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '10000000', '1', 'fc95b2fe789f3831d3b715cca69ed2f6', '1578645250');

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
-- Table structure for group_permission
-- ----------------------------
DROP TABLE IF EXISTS `group_permission`;
CREATE TABLE `group_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `permission_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_group_id` (`group_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of group_permission
-- ----------------------------
INSERT INTO `group_permission` VALUES ('1', '1', '1');
INSERT INTO `group_permission` VALUES ('2', '1', '2');
INSERT INTO `group_permission` VALUES ('3', '1', '3');
INSERT INTO `group_permission` VALUES ('4', '1', '4');
INSERT INTO `group_permission` VALUES ('5', '1', '5');
INSERT INTO `group_permission` VALUES ('6', '1', '6');
INSERT INTO `group_permission` VALUES ('7', '1', '7');
INSERT INTO `group_permission` VALUES ('8', '1', '8');
INSERT INTO `group_permission` VALUES ('9', '1', '9');
INSERT INTO `group_permission` VALUES ('10', '1', '10');

-- ----------------------------
-- Table structure for group_user
-- ----------------------------
DROP TABLE IF EXISTS `group_user`;
CREATE TABLE `group_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_user_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `create_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_admin_user_id` (`admin_user_id`) USING BTREE,
  KEY `idx_group_id` (`group_id`) USING BTREE
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of login_log
-- ----------------------------
INSERT INTO `login_log` VALUES ('1', '1', '2130706433', '1578474283');
INSERT INTO `login_log` VALUES ('2', '1', '2130706433', '1578474851');
INSERT INTO `login_log` VALUES ('3', '1', '2130706433', '1578555165');
INSERT INTO `login_log` VALUES ('4', '1', '2130706433', '1578558616');
INSERT INTO `login_log` VALUES ('5', '1', '2130706433', '1578643750');
INSERT INTO `login_log` VALUES ('6', '1', '2130706433', '1578645034');
INSERT INTO `login_log` VALUES ('7', '1', '2130706433', '1578645055');
INSERT INTO `login_log` VALUES ('8', '1', '2130706433', '1578645148');
INSERT INTO `login_log` VALUES ('9', '1', '2130706433', '1578645250');

-- ----------------------------
-- Table structure for permission
-- ----------------------------
DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` bigint(20) NOT NULL DEFAULT '0',
  `permission_code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `level` tinyint(3) NOT NULL DEFAULT '0',
  `pid` tinyint(3) NOT NULL DEFAULT '0',
  `is_menu` tinyint(3) NOT NULL DEFAULT '1',
  `is_public` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of permission
-- ----------------------------
INSERT INTO `permission` VALUES ('1', '101', '', '后台', '', '1', '0', '1', '0');
INSERT INTO `permission` VALUES ('2', '10101', '', '首页', 'index.html', '2', '1', '1', '0');
INSERT INTO `permission` VALUES ('3', '10102', '', '发布', 'release.html', '2', '1', '1', '0');
INSERT INTO `permission` VALUES ('4', '102', '', '管理', '', '1', '0', '1', '0');
INSERT INTO `permission` VALUES ('5', '10201', '', '分类管理', 'category.html', '2', '4', '1', '0');
INSERT INTO `permission` VALUES ('6', '10202', '', '内容管理', 'content.html', '2', '4', '1', '0');
INSERT INTO `permission` VALUES ('7', '103', '', '数据', '', '1', '0', '1', '0');
INSERT INTO `permission` VALUES ('8', '10301', '', '阅读数据', 'subscribe.html', '2', '7', '1', '0');
INSERT INTO `permission` VALUES ('9', '104', '', '设置', '', '1', '0', '1', '0');
INSERT INTO `permission` VALUES ('10', '10401', '', '账号信息', 'info.html', '2', '9', '1', '0');
INSERT INTO `permission` VALUES ('11', '1010101', 'reportData', '首页浏览数据', '/api/admin/reportData', '3', '2', '0', '1');
INSERT INTO `permission` VALUES ('12', '1010102', 'getMenu', '获取菜单', '/api/admin/getMenu', '3', '2', '0', '1');
