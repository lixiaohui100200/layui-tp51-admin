/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : qing_cms

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2019-07-08 09:29:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_user
-- ----------------------------
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '用户姓名',
  `login_name` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL COMMENT '登录手机号',
  `email` varchar(50) DEFAULT NULL COMMENT '登录邮箱',
  `password` varchar(32) DEFAULT NULL,
  `head_img` varchar(200) DEFAULT NULL COMMENT '用户头像',
  `status` tinyint(2) DEFAULT NULL COMMENT '状态 1 正常 0 待审核 -1 删除 -2 冻结',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `create_by` int(11) DEFAULT NULL COMMENT '创建人id',
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_user
-- ----------------------------
INSERT INTO `admin_user` VALUES ('1', '管理员', 'admin', '', '', 'b8c6551bbe8f6f6e653b2bc854b24379', null, '1', '1556601911', '0', '');
INSERT INTO `admin_user` VALUES ('2', '阿斯玛', 'asuma', 'sqiu_li@163.com', null, 'b8c6551bbe8f6f6e653b2bc854b24379', null, '1', null, null, null);

-- ----------------------------
-- Table structure for auth_group
-- ----------------------------
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 开启 -2 关闭 -1 删除',
  `rules` varchar(200) NOT NULL DEFAULT '',
  `remark` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_group
-- ----------------------------
INSERT INTO `auth_group` VALUES ('1', '超级管理员', '1', 'all', '最高管理员权限，拥有所有权限');
INSERT INTO `auth_group` VALUES ('2', '管理员', '1', '1,2,3,4,5', '拥有除权限管理、操作日志外的所有权限');

-- ----------------------------
-- Table structure for auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `auth_group_access`;
CREATE TABLE `auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_group_access
-- ----------------------------
INSERT INTO `auth_group_access` VALUES ('1', '1');
INSERT INTO `auth_group_access` VALUES ('2', '2');

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '',
  `title` char(20) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL COMMENT '规则类型 1 模块 2 子模块 3 节点',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 正常 -2 关闭 -1删除',
  `condition` char(100) NOT NULL DEFAULT '',
  `sorted` smallint(1) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '上级ID',
  `run_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '表现类型 1 普通 2 异步',
  `is_menu` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否菜单 1 是 0 否',
  `icon` varchar(30) DEFAULT NULL,
  `is_logged` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否记录日志 1 是 0 否',
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------
INSERT INTO `auth_rule` VALUES ('1', 'index', '首页', '1', '1', '', '0', '0', '1', '1', 'layui-icon-home', '0', '');
INSERT INTO `auth_rule` VALUES ('2', 'Panel/index', '控制台', '3', '1', '', '0', '1', '1', '1', 'layui-icon-console', '0', '');
INSERT INTO `auth_rule` VALUES ('3', 'AuthSet', '权限', '1', '1', '', '999', '0', '1', '1', 'layui-icon-vercode', '0', '');
INSERT INTO `auth_rule` VALUES ('4', 'AuthSet/admins', '后台管理员', '3', '1', '', '1', '3', '1', '1', 'layui-icon-circle', '0', '');
INSERT INTO `auth_rule` VALUES ('5', 'AuthSet/roles', '角色管理', '3', '1', '', '2', '3', '1', '1', 'layui-icon-circle', '0', '');
INSERT INTO `auth_rule` VALUES ('6', 'AuthSet/permissions', '权限管理', '3', '1', '', '3', '3', '1', '1', 'layui-icon-circle', '0', '');
INSERT INTO `auth_rule` VALUES ('7', 'AuthSet/operationLog', '操作日志', '3', '1', '', '4', '3', '1', '1', 'layui-icon-circle', '0', '');

-- ----------------------------
-- Table structure for operation_log
-- ----------------------------
DROP TABLE IF EXISTS `operation_log`;
CREATE TABLE `operation_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `auth_name` varchar(50) NOT NULL DEFAULT '' COMMENT '权限标识',
  `auth_title` varchar(80) DEFAULT NULL COMMENT '权限名称',
  `auth_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '行为描述',
  `ip` varchar(80) NOT NULL DEFAULT '',
  `record_time` datetime NOT NULL,
  `behavior_user` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;