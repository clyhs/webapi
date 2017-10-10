/*
Navicat MySQL Data Transfer

Source Server         : 47.91.156.179
Source Server Version : 50173
Source Host           : 47.91.156.179:3306
Source Database       : webapi

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2017-10-10 16:11:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `t_auth`
-- ----------------------------
DROP TABLE IF EXISTS `t_auth`;
CREATE TABLE `t_auth` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL COMMENT '权限名称',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态(1:禁用,2:启用)',
  `sort` smallint(6) unsigned DEFAULT '0' COMMENT '排序权重',
  `desc` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `create_by` bigint(11) unsigned DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_system_auth_title` (`title`) USING BTREE,
  KEY `index_system_auth_status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统权限表';

-- ----------------------------
-- Records of t_auth
-- ----------------------------

-- ----------------------------
-- Table structure for `t_auth_node`
-- ----------------------------
DROP TABLE IF EXISTS `t_auth_node`;
CREATE TABLE `t_auth_node` (
  `auth` bigint(20) unsigned DEFAULT NULL COMMENT '角色ID',
  `node` varchar(200) DEFAULT NULL COMMENT '节点路径',
  KEY `index_system_auth_auth` (`auth`) USING BTREE,
  KEY `index_system_auth_node` (`node`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色与节点关系表';

-- ----------------------------
-- Records of t_auth_node
-- ----------------------------

-- ----------------------------
-- Table structure for `t_config`
-- ----------------------------
DROP TABLE IF EXISTS `t_config`;
CREATE TABLE `t_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '配置编码',
  `value` varchar(500) DEFAULT NULL COMMENT '配置值',
  PRIMARY KEY (`id`),
  KEY `index_system_config_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统参数配置';

-- ----------------------------
-- Records of t_config
-- ----------------------------
INSERT INTO `t_config` VALUES ('148', 'site_name', 'Think.Admin Demo');
INSERT INTO `t_config` VALUES ('149', 'site_copy', 'abigfish © 2014~2017');
INSERT INTO `t_config` VALUES ('164', 'storage_type', 'local');
INSERT INTO `t_config` VALUES ('165', 'storage_qiniu_is_https', '1');
INSERT INTO `t_config` VALUES ('166', 'storage_qiniu_bucket', 'static');
INSERT INTO `t_config` VALUES ('167', 'storage_qiniu_domain', '');
INSERT INTO `t_config` VALUES ('168', 'storage_qiniu_access_key', 'OAFHGzCgZjod2-s4xr-g5ptkXsNbxDO_t2fozIEC');
INSERT INTO `t_config` VALUES ('169', 'storage_qiniu_secret_key', 'gy0aYdSFMSayQ4kMkgUeEeJRLThVjLpUJoPFxd-Z');
INSERT INTO `t_config` VALUES ('170', 'storage_qiniu_region', '华东');
INSERT INTO `t_config` VALUES ('173', 'app_name', 'Think.Admin');
INSERT INTO `t_config` VALUES ('174', 'app_version', '2.00 dev');
INSERT INTO `t_config` VALUES ('176', 'browser_icon', '');
INSERT INTO `t_config` VALUES ('184', 'wechat_appid', 'wx60a43dd8161666d4');
INSERT INTO `t_config` VALUES ('185', 'wechat_appsecret', '062938ddcfe0d69786e4e3d9dcbb08aa');
INSERT INTO `t_config` VALUES ('186', 'wechat_token', 'mytoken');
INSERT INTO `t_config` VALUES ('187', 'wechat_encodingaeskey', 'KHyoWLoS7oLZYkB4PokMTfA5sm6Hrqc9Tzgn9iXc0YH');
INSERT INTO `t_config` VALUES ('188', 'wechat_mch_id', '1332187001');
INSERT INTO `t_config` VALUES ('189', 'wechat_partnerkey', 'A82DC5BD1F3359081049C568D8502BC5');
INSERT INTO `t_config` VALUES ('194', 'wechat_cert_key', '');
INSERT INTO `t_config` VALUES ('196', 'wechat_cert_cert', '');
INSERT INTO `t_config` VALUES ('197', 'tongji_baidu_key', 'aa2f9869e9b578122e4692de2bd9f80f');
INSERT INTO `t_config` VALUES ('198', 'tongji_cnzz_key', '1261854404');
INSERT INTO `t_config` VALUES ('199', 'storage_oss_bucket', 'think-oss');
INSERT INTO `t_config` VALUES ('200', 'storage_oss_keyid', 'WjeX0AYSfgy5VbXQ');
INSERT INTO `t_config` VALUES ('201', 'storage_oss_secret', 'hQTENHy6MYVUTgwjcgfOCq5gckm2Lp');
INSERT INTO `t_config` VALUES ('202', 'storage_oss_domain', 'think-oss.oss-cn-shanghai.aliyuncs.com');
INSERT INTO `t_config` VALUES ('203', 'storage_oss_is_https', '1');

-- ----------------------------
-- Table structure for `t_log`
-- ----------------------------
DROP TABLE IF EXISTS `t_log`;
CREATE TABLE `t_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '操作者IP地址',
  `node` char(200) NOT NULL DEFAULT '' COMMENT '当前操作节点',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '操作人用户名',
  `action` varchar(200) NOT NULL DEFAULT '' COMMENT '操作行为',
  `content` text NOT NULL COMMENT '操作内容描述',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统操作日志表';

-- ----------------------------
-- Records of t_log
-- ----------------------------

-- ----------------------------
-- Table structure for `t_menu`
-- ----------------------------
DROP TABLE IF EXISTS `t_menu`;
CREATE TABLE `t_menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `node` varchar(200) NOT NULL DEFAULT '' COMMENT '节点代码',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `url` varchar(400) NOT NULL DEFAULT '' COMMENT '链接',
  `params` varchar(500) DEFAULT '' COMMENT '链接参数',
  `target` varchar(20) NOT NULL DEFAULT '_self' COMMENT '链接打开方式',
  `sort` int(11) unsigned DEFAULT '0' COMMENT '菜单排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `create_by` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_system_menu_node` (`node`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8 COMMENT='系统菜单表';

-- ----------------------------
-- Records of t_menu
-- ----------------------------
INSERT INTO `t_menu` VALUES ('2', '0', '系统管理', '', '', '#', '', '_self', '1000', '1', '0', '2015-11-16 19:15:38');
INSERT INTO `t_menu` VALUES ('4', '2', '系统配置', '', '', '#', '', '_self', '100', '1', '0', '2016-03-14 18:12:55');
INSERT INTO `t_menu` VALUES ('5', '4', '网站参数', '', 'fa fa-apple', 'admin/config/index', '', '_self', '20', '1', '0', '2016-05-06 14:36:49');
INSERT INTO `t_menu` VALUES ('6', '4', '文件存储', '', 'fa fa-save', 'admin/config/file', '', '_self', '30', '1', '0', '2016-05-06 14:39:43');
INSERT INTO `t_menu` VALUES ('9', '20', '操作日志', '', 'glyphicon glyphicon-console', 'admin/log/index', '', '_self', '50', '1', '0', '2017-03-24 15:49:31');
INSERT INTO `t_menu` VALUES ('19', '20', '权限管理', '', 'fa fa-user-secret', 'admin/auth/index', '', '_self', '10', '1', '0', '2015-11-17 13:18:12');
INSERT INTO `t_menu` VALUES ('20', '2', '系统权限', '', '', '#', '', '_self', '200', '1', '0', '2016-03-14 18:11:41');
INSERT INTO `t_menu` VALUES ('21', '20', '系统菜单', '', 'glyphicon glyphicon-menu-hamburger', 'admin/menu/index', '', '_self', '30', '1', '0', '2015-11-16 19:16:16');
INSERT INTO `t_menu` VALUES ('22', '20', '节点管理', '', 'fa fa-ellipsis-v', 'admin/node/index', '', '_self', '20', '1', '0', '2015-11-16 19:16:16');
INSERT INTO `t_menu` VALUES ('29', '20', '系统用户', '', 'fa fa-users', 'admin/user/index', '', '_self', '40', '1', '0', '2016-10-31 14:31:40');
INSERT INTO `t_menu` VALUES ('61', '0', '微信管理', '', '', '#', '', '_self', '2000', '1', '0', '2017-03-29 11:00:21');
INSERT INTO `t_menu` VALUES ('62', '61', '微信对接配置', '', '', '#', '', '_self', '100', '1', '0', '2017-03-29 11:03:38');
INSERT INTO `t_menu` VALUES ('63', '62', '微信接口配置\r\n', '', 'fa fa-usb', 'wechat/config/index', '', '_self', '10', '1', '0', '2017-03-29 11:04:44');
INSERT INTO `t_menu` VALUES ('65', '61', '微信粉丝管理', '', '', '#', '', '_self', '300', '1', '0', '2017-03-29 11:08:32');
INSERT INTO `t_menu` VALUES ('66', '65', '粉丝标签', '', 'fa fa-tags', 'wechat/tags/index', '', '_self', '10', '1', '0', '2017-03-29 11:09:41');
INSERT INTO `t_menu` VALUES ('67', '65', '已关注粉丝', '', 'fa fa-wechat', 'wechat/fans/index', '', '_self', '20', '1', '0', '2017-03-29 11:10:07');
INSERT INTO `t_menu` VALUES ('68', '61', '微信订制', '', '', '#', '', '_self', '200', '1', '0', '2017-03-29 11:10:39');
INSERT INTO `t_menu` VALUES ('69', '68', '微信菜单定制', '', 'glyphicon glyphicon-phone', 'wechat/menu/index', '', '_self', '40', '1', '0', '2017-03-29 11:11:08');
INSERT INTO `t_menu` VALUES ('70', '68', '关键字管理', '', 'fa fa-paw', 'wechat/keys/index', '', '_self', '10', '1', '0', '2017-03-29 11:11:49');
INSERT INTO `t_menu` VALUES ('71', '68', '关注自动回复', '', 'fa fa-commenting-o', 'wechat/keys/subscribe', '', '_self', '20', '1', '0', '2017-03-29 11:12:32');
INSERT INTO `t_menu` VALUES ('81', '68', '无配置默认回复', '', 'fa fa-commenting-o', 'wechat/keys/defaults', '', '_self', '30', '1', '0', '2017-04-21 14:48:25');
INSERT INTO `t_menu` VALUES ('82', '61', '素材资源管理', '', '', '#', '', '_self', '300', '1', '0', '2017-04-24 11:23:18');
INSERT INTO `t_menu` VALUES ('83', '82', '添加图文', '', 'fa fa-folder-open-o', 'wechat/news/add?id=1', '', '_self', '20', '1', '0', '2017-04-24 11:23:40');
INSERT INTO `t_menu` VALUES ('85', '82', '图文列表', '', 'fa fa-file-pdf-o', 'wechat/news/index', '', '_self', '10', '1', '0', '2017-04-24 11:25:45');
INSERT INTO `t_menu` VALUES ('86', '65', '粉丝黑名单', '', 'fa fa-reddit-alien', 'wechat/fans/back', '', '_self', '30', '1', '0', '2017-05-05 16:17:03');
INSERT INTO `t_menu` VALUES ('87', '0', '插件案例', '', '', '#', '', '_self', '3000', '1', '0', '2017-07-10 15:10:16');
INSERT INTO `t_menu` VALUES ('88', '87', '第三方插件', '', '', '#', '', '_self', '0', '1', '0', '2017-07-10 15:10:37');
INSERT INTO `t_menu` VALUES ('90', '88', 'PCAS 省市区', '', '', 'demo/plugs/region', '', '_self', '0', '1', '0', '2017-07-10 18:45:47');
INSERT INTO `t_menu` VALUES ('91', '87', '内置插件', '', '', '#', '', '_self', '0', '1', '0', '2017-07-10 18:56:59');
INSERT INTO `t_menu` VALUES ('92', '91', '文件上传', '', '', 'demo/plugs/file', '', '_self', '0', '1', '0', '2017-07-10 18:57:22');
INSERT INTO `t_menu` VALUES ('93', '88', '富文本编辑器', '', '', 'demo/plugs/editor', '', '_self', '0', '1', '0', '2017-07-28 15:19:44');
INSERT INTO `t_menu` VALUES ('94', '0', '后台首页', '', '', 'admin/index/main', '', '_self', '0', '1', '0', '2017-08-08 11:28:43');

-- ----------------------------
-- Table structure for `t_node`
-- ----------------------------
DROP TABLE IF EXISTS `t_node`;
CREATE TABLE `t_node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(100) DEFAULT NULL COMMENT '节点代码',
  `title` varchar(500) DEFAULT NULL COMMENT '节点标题',
  `is_menu` tinyint(1) unsigned DEFAULT '0' COMMENT '是否可设置为菜单',
  `is_auth` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启动RBAC权限控制',
  `is_login` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启动登录控制',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_system_node_node` (`node`)
) ENGINE=InnoDB AUTO_INCREMENT=209 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统节点表';

-- ----------------------------
-- Records of t_node
-- ----------------------------
INSERT INTO `t_node` VALUES ('131', 'admin/auth/index', '权限列表', '1', '1', '1', '2017-08-23 15:45:42');
INSERT INTO `t_node` VALUES ('132', 'admin', '后台管理', '0', '1', '1', '2017-08-23 15:45:44');
INSERT INTO `t_node` VALUES ('133', 'admin/auth/apply', '节点授权', '0', '1', '1', '2017-08-23 16:05:18');
INSERT INTO `t_node` VALUES ('134', 'admin/auth/add', '添加授权', '0', '1', '1', '2017-08-23 16:05:19');
INSERT INTO `t_node` VALUES ('135', 'admin/auth/edit', '编辑权限', '0', '1', '1', '2017-08-23 16:05:19');
INSERT INTO `t_node` VALUES ('136', 'admin/auth/forbid', '禁用权限', '0', '1', '1', '2017-08-23 16:05:20');
INSERT INTO `t_node` VALUES ('137', 'admin/auth/resume', '启用权限', '0', '1', '1', '2017-08-23 16:05:20');
INSERT INTO `t_node` VALUES ('138', 'admin/auth/del', '删除权限', '0', '1', '1', '2017-08-23 16:05:21');
INSERT INTO `t_node` VALUES ('139', 'admin/config/index', '参数配置', '1', '1', '1', '2017-08-23 16:05:22');
INSERT INTO `t_node` VALUES ('140', 'admin/config/file', '文件配置', '1', '1', '1', '2017-08-23 16:05:22');
INSERT INTO `t_node` VALUES ('141', 'admin/log/index', '日志列表', '1', '1', '1', '2017-08-23 16:05:23');
INSERT INTO `t_node` VALUES ('142', 'admin/log/del', '删除日志', '0', '1', '1', '2017-08-23 16:05:24');
INSERT INTO `t_node` VALUES ('143', 'admin/menu/index', '菜单列表', '1', '1', '1', '2017-08-23 16:05:25');
INSERT INTO `t_node` VALUES ('144', 'admin/menu/add', '添加菜单', '0', '1', '1', '2017-08-23 16:05:25');
INSERT INTO `t_node` VALUES ('145', 'admin/menu/edit', '编辑菜单', '0', '1', '1', '2017-08-23 16:05:26');
INSERT INTO `t_node` VALUES ('146', 'admin/menu/del', '删除菜单', '0', '1', '1', '2017-08-23 16:05:26');
INSERT INTO `t_node` VALUES ('147', 'admin/menu/forbid', '禁用菜单', '0', '1', '1', '2017-08-23 16:05:27');
INSERT INTO `t_node` VALUES ('148', 'admin/menu/resume', '启用菜单', '0', '1', '1', '2017-08-23 16:05:28');
INSERT INTO `t_node` VALUES ('149', 'admin/node/index', '节点列表', '1', '1', '1', '2017-08-23 16:05:29');
INSERT INTO `t_node` VALUES ('150', 'admin/node/save', '节点更新', '0', '1', '1', '2017-08-23 16:05:30');
INSERT INTO `t_node` VALUES ('151', 'admin/user/index', '用户管理', '1', '1', '1', '2017-08-23 16:05:31');
INSERT INTO `t_node` VALUES ('152', 'admin/user/auth', '用户授权', '0', '1', '1', '2017-08-23 16:05:32');
INSERT INTO `t_node` VALUES ('153', 'admin/user/add', '添加用户', '0', '1', '1', '2017-08-23 16:05:33');
INSERT INTO `t_node` VALUES ('154', 'admin/user/edit', '编辑用户', '0', '1', '1', '2017-08-23 16:05:33');
INSERT INTO `t_node` VALUES ('155', 'admin/user/pass', '用户密码', '0', '1', '1', '2017-08-23 16:05:34');
INSERT INTO `t_node` VALUES ('156', 'admin/user/del', '删除用户', '0', '1', '1', '2017-08-23 16:05:34');
INSERT INTO `t_node` VALUES ('157', 'admin/user/forbid', '禁用用户', '0', '1', '1', '2017-08-23 16:05:34');
INSERT INTO `t_node` VALUES ('158', 'admin/user/resume', '启用用户', '0', '1', '1', '2017-08-23 16:05:35');
INSERT INTO `t_node` VALUES ('159', 'demo/plugs/file', '文件上传', '1', '0', '0', '2017-08-23 16:05:36');
INSERT INTO `t_node` VALUES ('160', 'demo/plugs/region', '区域选择', '1', '0', '0', '2017-08-23 16:05:36');
INSERT INTO `t_node` VALUES ('161', 'demo/plugs/editor', '富文本器', '1', '0', '0', '2017-08-23 16:05:37');
INSERT INTO `t_node` VALUES ('162', 'wechat/config/index', '微信参数', '1', '1', '1', '2017-08-23 16:05:37');
INSERT INTO `t_node` VALUES ('163', 'wechat/config/pay', '微信支付', '0', '1', '1', '2017-08-23 16:05:38');
INSERT INTO `t_node` VALUES ('164', 'wechat/fans/index', '粉丝列表', '1', '1', '1', '2017-08-23 16:05:39');
INSERT INTO `t_node` VALUES ('165', 'wechat/fans/back', '粉丝黑名单', '1', '1', '1', '2017-08-23 16:05:39');
INSERT INTO `t_node` VALUES ('166', 'wechat/fans/backadd', '移入黑名单', '0', '1', '1', '2017-08-23 16:05:40');
INSERT INTO `t_node` VALUES ('167', 'wechat/fans/tagset', '设置标签', '0', '1', '1', '2017-08-23 16:05:41');
INSERT INTO `t_node` VALUES ('168', 'wechat/fans/backdel', '移出黑名单', '0', '1', '1', '2017-08-23 16:05:41');
INSERT INTO `t_node` VALUES ('169', 'wechat/fans/tagadd', '添加标签', '0', '1', '1', '2017-08-23 16:05:41');
INSERT INTO `t_node` VALUES ('170', 'wechat/fans/tagdel', '删除标签', '0', '1', '1', '2017-08-23 16:05:42');
INSERT INTO `t_node` VALUES ('171', 'wechat/fans/sync', '同步粉丝', '0', '1', '1', '2017-08-23 16:05:43');
INSERT INTO `t_node` VALUES ('172', 'wechat/keys/index', '关键字列表', '1', '1', '1', '2017-08-23 16:05:44');
INSERT INTO `t_node` VALUES ('173', 'wechat/keys/add', '添加关键字', '0', '1', '1', '2017-08-23 16:05:44');
INSERT INTO `t_node` VALUES ('174', 'wechat/keys/edit', '编辑关键字', '0', '1', '1', '2017-08-23 16:05:45');
INSERT INTO `t_node` VALUES ('175', 'wechat/keys/del', '删除关键字', '0', '1', '1', '2017-08-23 16:05:45');
INSERT INTO `t_node` VALUES ('176', 'wechat/keys/forbid', '禁用关键字', '0', '1', '1', '2017-08-23 16:05:46');
INSERT INTO `t_node` VALUES ('177', 'wechat/keys/resume', '启用关键字', '0', '1', '1', '2017-08-23 16:05:46');
INSERT INTO `t_node` VALUES ('178', 'wechat/keys/subscribe', '关注默认回复', '0', '1', '1', '2017-08-23 16:05:48');
INSERT INTO `t_node` VALUES ('179', 'wechat/keys/defaults', '默认响应回复', '0', '1', '1', '2017-08-23 16:05:49');
INSERT INTO `t_node` VALUES ('180', 'wechat/menu/index', '微信菜单', '1', '1', '1', '2017-08-23 16:05:51');
INSERT INTO `t_node` VALUES ('181', 'wechat/menu/edit', '发布微信菜单', '0', '1', '1', '2017-08-23 16:05:51');
INSERT INTO `t_node` VALUES ('182', 'wechat/menu/cancel', '取消微信菜单', '0', '1', '1', '2017-08-23 16:05:52');
INSERT INTO `t_node` VALUES ('183', 'wechat/news/index', '微信图文列表', '1', '1', '1', '2017-08-23 16:05:52');
INSERT INTO `t_node` VALUES ('184', 'wechat/news/select', '微信图文选择', '0', '1', '1', '2017-08-23 16:05:53');
INSERT INTO `t_node` VALUES ('185', 'wechat/news/image', '微信图片选择', '0', '1', '1', '2017-08-23 16:05:53');
INSERT INTO `t_node` VALUES ('186', 'wechat/news/add', '添加图文', '0', '1', '1', '2017-08-23 16:05:54');
INSERT INTO `t_node` VALUES ('187', 'wechat/news/edit', '编辑图文', '0', '1', '1', '2017-08-23 16:05:56');
INSERT INTO `t_node` VALUES ('188', 'wechat/news/del', '删除图文', '0', '1', '1', '2017-08-23 16:05:56');
INSERT INTO `t_node` VALUES ('189', 'wechat/news/push', '推送图文', '0', '1', '1', '2017-08-23 16:05:56');
INSERT INTO `t_node` VALUES ('190', 'wechat/tags/index', '微信标签列表', '1', '1', '1', '2017-08-23 16:05:58');
INSERT INTO `t_node` VALUES ('191', 'wechat/tags/add', '添加微信标签', '0', '1', '1', '2017-08-23 16:05:58');
INSERT INTO `t_node` VALUES ('192', 'wechat/tags/edit', '编辑微信标签', '0', '1', '1', '2017-08-23 16:05:58');
INSERT INTO `t_node` VALUES ('193', 'wechat/tags/sync', '同步微信标签', '0', '1', '1', '2017-08-23 16:05:59');
INSERT INTO `t_node` VALUES ('194', 'admin/auth', '权限管理', '0', '1', '1', '2017-08-23 16:06:58');
INSERT INTO `t_node` VALUES ('195', 'admin/config', '系统配置', '0', '1', '1', '2017-08-23 16:07:34');
INSERT INTO `t_node` VALUES ('196', 'admin/log', '系统日志', '0', '1', '1', '2017-08-23 16:07:46');
INSERT INTO `t_node` VALUES ('197', 'admin/menu', '系统菜单', '0', '1', '1', '2017-08-23 16:08:02');
INSERT INTO `t_node` VALUES ('198', 'admin/node', '系统节点', '0', '1', '1', '2017-08-23 16:08:44');
INSERT INTO `t_node` VALUES ('199', 'admin/user', '系统用户', '0', '1', '1', '2017-08-23 16:09:43');
INSERT INTO `t_node` VALUES ('200', 'demo', '插件案例', '0', '1', '1', '2017-08-23 16:10:43');
INSERT INTO `t_node` VALUES ('201', 'demo/plugs', '插件案例', '0', '1', '1', '2017-08-23 16:10:51');
INSERT INTO `t_node` VALUES ('202', 'wechat', '微信管理', '0', '1', '1', '2017-08-23 16:11:13');
INSERT INTO `t_node` VALUES ('203', 'wechat/config', '微信配置', '0', '1', '1', '2017-08-23 16:11:19');
INSERT INTO `t_node` VALUES ('204', 'wechat/fans', '粉丝管理', '0', '1', '1', '2017-08-23 16:11:36');
INSERT INTO `t_node` VALUES ('205', 'wechat/keys', '关键字管理', '0', '1', '1', '2017-08-23 16:13:00');
INSERT INTO `t_node` VALUES ('206', 'wechat/menu', '微信菜单管理', '0', '1', '1', '2017-08-23 16:14:11');
INSERT INTO `t_node` VALUES ('207', 'wechat/news', '微信图文管理', '0', '1', '1', '2017-08-23 16:14:40');
INSERT INTO `t_node` VALUES ('208', 'wechat/tags', '微信标签管理', '0', '1', '1', '2017-08-23 16:15:25');

-- ----------------------------
-- Table structure for `t_sequence`
-- ----------------------------
DROP TABLE IF EXISTS `t_sequence`;
CREATE TABLE `t_sequence` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) DEFAULT NULL COMMENT '序号类型',
  `sequence` char(50) NOT NULL COMMENT '序号值',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_system_sequence_unique` (`type`,`sequence`) USING BTREE,
  KEY `index_system_sequence_type` (`type`),
  KEY `index_system_sequence_number` (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统序号表';

-- ----------------------------
-- Records of t_sequence
-- ----------------------------

-- ----------------------------
-- Table structure for `t_user`
-- ----------------------------
DROP TABLE IF EXISTS `t_user`;
CREATE TABLE `t_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户登录名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户登录密码',
  `qq` varchar(16) DEFAULT NULL COMMENT '联系QQ',
  `mail` varchar(32) DEFAULT NULL COMMENT '联系邮箱',
  `phone` varchar(16) DEFAULT NULL COMMENT '联系手机号',
  `desc` varchar(255) DEFAULT '' COMMENT '备注说明',
  `login_num` bigint(20) unsigned DEFAULT '0' COMMENT '登录次数',
  `login_at` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `authorize` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除状态(1:删除,0:未删)',
  `create_by` bigint(20) unsigned DEFAULT NULL COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_system_user_username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='系统用户表';

-- ----------------------------
-- Records of t_user
-- ----------------------------
INSERT INTO `t_user` VALUES ('10000', 'admin', '21232f297a57a5a743894a0e4a801fc3', '22222222', '123@qq.com', '13888888855', 'dfgsdfgsfd', '27039', '2017-08-23 16:15:57', '1', '301,302,303,304', '0', null, '2015-11-13 15:14:22');
