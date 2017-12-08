/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : ceb_orders

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-12-08 17:30:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ceb_admin`
-- ----------------------------
DROP TABLE IF EXISTS `ceb_admin`;
CREATE TABLE `ceb_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `ip` varchar(32) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ceb_admin
-- ----------------------------
INSERT INTO `ceb_admin` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '0.0.0.0', '9', '2017-12-08 09:48:11');
INSERT INTO `ceb_admin` VALUES ('40', 'tom', '21232f297a57a5a743894a0e4a801fc3', '127.0.0.1', '1', '2017-12-08 10:39:56');
INSERT INTO `ceb_admin` VALUES ('41', 'jack', '21232f297a57a5a743894a0e4a801fc3', '0.0.0.0', '1', '2017-11-08 15:40:40');
INSERT INTO `ceb_admin` VALUES ('42', 'root', '21232f297a57a5a743894a0e4a801fc3', null, '1', null);

-- ----------------------------
-- Table structure for `ceb_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `ceb_auth_group`;
CREATE TABLE `ceb_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '用户组所属模块',
  `type` tinyint(4) NOT NULL COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`module`,`title`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ceb_auth_group
-- ----------------------------
INSERT INTO `ceb_auth_group` VALUES ('1', 'admin', '1', '默认用户组', 'admin', '1', '12,32,20,21,49,50,52,54,23,30,55,24,25,35,36,26,38,39,40,41,27,31,33,56,57,63,64,66,68,62,67');
INSERT INTO `ceb_auth_group` VALUES ('27', 'admin', '1', '超级管理员', '', '1', '12,32,20,21,49,50,51,52,23,30,24,25,35,36,37,26,38,39,40,41,42,27,31,33,34');
INSERT INTO `ceb_auth_group` VALUES ('19', 'admin', '1', 'root', '', '1', '12,32,20,21,49,50,52,23,30');
INSERT INTO `ceb_auth_group` VALUES ('7', 'admin', '1', 'admin', 'ffff', '1', '24,25,35,36,37,26,38,39,40,41,42,27,31,33,34');

-- ----------------------------
-- Table structure for `ceb_auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `ceb_auth_group_access`;
CREATE TABLE `ceb_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `group_id` varchar(64) NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ceb_auth_group_access
-- ----------------------------
INSERT INTO `ceb_auth_group_access` VALUES ('1', '1,27,7');
INSERT INTO `ceb_auth_group_access` VALUES ('40', '1');
INSERT INTO `ceb_auth_group_access` VALUES ('41', '1');
INSERT INTO `ceb_auth_group_access` VALUES ('42', '1,27');

-- ----------------------------
-- Table structure for `ceb_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `ceb_auth_rule`;
CREATE TABLE `ceb_auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `module` varchar(20) NOT NULL COMMENT '规则所在module',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of ceb_auth_rule
-- ----------------------------
INSERT INTO `ceb_auth_rule` VALUES ('50', '21', 'admin', 'Admin/Order/Edit', '修改', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('12', '0', 'admin', 'Admin/Index/Index', '首页', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('27', '24', 'admin', 'Admin/Admin/Permission', '权限管理', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('20', '0', 'admin', 'Admin/Order', '订单管理', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('21', '20', 'admin', 'Admin/Order/Index', '订单列表', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('23', '20', 'admin', 'Admin/Order/Export', '订单导出', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('24', '0', 'admin', 'Admin/Admin', '管理员管理', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('25', '24', 'admin', 'Admin/Admin/Index', '管理员列表', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('26', '24', 'admin', 'Admin/Admin/Groups', '用户组管理', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('30', '23', 'admin', 'Admin/Order/ExportData', '导出', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('31', '27', 'admin', 'Admin/Admin/Permissionadd', '添加权限', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('32', '12', 'admin', 'Admin/Index/Welcome', '欢迎', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('49', '21', 'admin', 'Admin/Order/Details', '详情', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('33', '27', 'admin', 'Admin/Admin/Permissionedit', '修改', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('34', '27', 'admin', 'Admin/Admin/Delpermission', '删除', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('35', '25', 'admin', 'Admin/Admin/Add', '添加', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('36', '25', 'admin', 'Admin/Admin/Edit', '修改', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('37', '25', 'admin', 'Admin/Admin/Delusers', '删除', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('38', '26', 'admin', 'Admin/Admin/Groupsadd', '添加', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('39', '26', 'admin', 'Admin/Admin/Groupsedit', '修改', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('40', '26', 'admin', 'Admin/Admin/Groupsauthorize', '授权', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('41', '26', 'admin', 'Admin/Admin/Groupsmember', '组员管理', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('42', '26', 'admin', 'Admin/Admin/Delgroups', '删除', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('51', '21', 'admin', 'Admin/Order/DelOrder', '删除', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('52', '21', 'admin', 'Admin/Order/ImportData', '导入', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('54', '21', 'admin', 'Admin/Order/Pay', '支付', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('55', '23', 'admin', 'Admin/Order/Declaration', '报关', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('56', '0', 'admin', 'Admin/Client', '客户管理', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('57', '56', 'admin', 'Admin/Client/Index', '客户列表', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('65', '57', 'admin', 'Admin/Client/DelClients', '删除', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('64', '57', 'admin', 'Admin/Client/Edit', '修改', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('63', '57', 'admin', 'Admin/Client/Add', '添加', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('62', '56', 'admin', 'Admin/Client/DelList', '删除的客户', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('66', '57', 'admin', 'Admin/Client/OrderLists', '查看订单列表', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('67', '62', 'admin', 'Admin/Client/Recover', '恢复客户', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('68', '57', 'admin', 'Admin/Client/Export', '查看批次列表', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('71', '57', 'admin', 'Admin/Client/DelBatch', '删除批次', '1', '1', '');
INSERT INTO `ceb_auth_rule` VALUES ('72', '50', 'admin', 'Admin/Order/Index/Test', 'test', '1', '1', '');

-- ----------------------------
-- Table structure for `ceb_client`
-- ----------------------------
DROP TABLE IF EXISTS `ceb_client`;
CREATE TABLE `ceb_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(100) NOT NULL COMMENT '需要报关的客户名称',
  `client_telephone` varchar(50) NOT NULL COMMENT '客户电话',
  `client_email` varchar(60) DEFAULT NULL COMMENT '客户邮箱',
  `client_note` varchar(1000) DEFAULT NULL COMMENT '客户备注',
  `create_time` datetime DEFAULT NULL COMMENT '客户创建时间',
  `delete_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识字段',
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_name` (`client_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ceb_client
-- ----------------------------
INSERT INTO `ceb_client` VALUES ('1', '就手国际', '1367890543', 'dsfsd@qqf', '123w', '2017-12-05 10:37:59', '0');
INSERT INTO `ceb_client` VALUES ('2', '阿里巴巴', '123', '123@2323', '1321323', '2017-12-05 10:55:50', '0');
INSERT INTO `ceb_client` VALUES ('4', '百度', '134', '123213@qq', 'ssss', '2017-12-05 11:00:52', '0');
INSERT INTO `ceb_client` VALUES ('5', '腾讯', '656345345', 'sfsfsdfds@fdsff', 'sdasdg', '2017-12-05 11:01:19', '0');
INSERT INTO `ceb_client` VALUES ('6', '京东', '32434', 'dsffffd@qq', '', '2017-12-05 15:37:30', '0');
INSERT INTO `ceb_client` VALUES ('7', '美团', '87567', 'fhjj@dfe', '', '2017-12-05 15:37:51', '0');

-- ----------------------------
-- Table structure for `ceb_order_batch`
-- ----------------------------
DROP TABLE IF EXISTS `ceb_order_batch`;
CREATE TABLE `ceb_order_batch` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `com_id` int(11) DEFAULT NULL COMMENT '该批次所属的公司id',
  `batch_time` varchar(60) NOT NULL COMMENT '批次号',
  `batch_note` varchar(1000) DEFAULT NULL COMMENT '批次注释',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ceb_order_batch
-- ----------------------------
INSERT INTO `ceb_order_batch` VALUES ('7', '6', '201712071201314474', '');
INSERT INTO `ceb_order_batch` VALUES ('10', '7', '201712071616423177', '');
INSERT INTO `ceb_order_batch` VALUES ('14', '1', '201712081032431133', '');
INSERT INTO `ceb_order_batch` VALUES ('15', '1', '201712081704245024', '234');

-- ----------------------------
-- Table structure for `ceb_order_goods`
-- ----------------------------
DROP TABLE IF EXISTS `ceb_order_goods`;
CREATE TABLE `ceb_order_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_no` varchar(60) NOT NULL COMMENT '交易平台的订单编号，同一交易平台的订单编号。订单编号长度不能超过60位。',
  `gnum` decimal(4,0) NOT NULL COMMENT '从1开始的递增序号。',
  `item_no` varchar(30) DEFAULT NULL COMMENT '电商企业自定义的商品货号（SKU）。',
  `item_name` varchar(250) NOT NULL COMMENT '交易平台销售商品的中文名称。',
  `item_describe` varchar(1000) DEFAULT NULL COMMENT '交易平台销售商品的描述信息。',
  `bar_code` varchar(50) DEFAULT NULL COMMENT '国际通用的商品条形码，一般由前缀部分、制造厂商代码、商品代码和校验码组成。',
  `qty` decimal(19,2) NOT NULL COMMENT '按成交计量单位的实际数量',
  `qty1` decimal(19,2) NOT NULL COMMENT '按照商品编码规则对应的法定计量单位的实际数量填写。',
  `qty2` decimal(19,2) DEFAULT NULL COMMENT '第二数量',
  `unit` char(3) NOT NULL COMMENT '海关标准的参数代码  《JGS-20 海关业务代码集》- 计量单位代码(计量单位)',
  `unit1` char(3) NOT NULL COMMENT '海关标准的参数代码 《JGS-20 海关业务代码集》- 计量单位代码(法定计量单位)',
  `unit2` char(3) DEFAULT NULL COMMENT '海关标准的参数代码 《JGS-20 海关业务代码集》- 计量单位代码(第二计量单位)',
  `country` char(3) NOT NULL COMMENT '填写海关标准的参数代码，参照《JGS-20 海关业务代码集》-国家（地区）代码表。',
  `hscode` varchar(60) NOT NULL COMMENT '海关商品编码(Hscode)',
  `gjcode` varchar(60) DEFAULT NULL COMMENT '国检商品编号',
  `gtype` varchar(100) NOT NULL COMMENT '商品规格型号',
  `price` decimal(19,2) NOT NULL COMMENT '商品单价。赠品单价填写为“0”。',
  `total_price` decimal(19,2) NOT NULL COMMENT '商品总价，等于单价乘以数量。',
  `currency` char(3) NOT NULL COMMENT '限定为人民币，填写“142”。',
  `goods_note` varchar(1000) DEFAULT NULL COMMENT '促销活动，商品单价偏离市场价格的，可以在此说明。',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=355 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of ceb_order_goods
-- ----------------------------
INSERT INTO `ceb_order_goods` VALUES ('216', '2017120750548150', '1', null, 'COW&amp;GATE牛栏婴儿奶粉3段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('231', '2017120702989517', '1', null, 'COW&amp;GATE牛栏婴儿奶粉2段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '76.00', '456.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('292', '2017120800986197', '1', null, 'COW&GATE牛栏婴儿奶粉3段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('293', '2017120800984076', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('294', '2017120800984538', '1', null, 'COW&GATE牛栏婴儿奶粉2段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '76.00', '456.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('295', '2017120800986355', '1', null, 'COW&GATE牛栏婴儿奶粉3段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('296', '2017120800988843', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('297', '2017120899514722', '1', null, 'NESTLE雀巢成人奶粉', null, null, '2.00', '1.80', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '100.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('298', '2017120899514722', '2', null, 'APTAMIL爱他美婴儿奶粉4段', null, null, '4.00', '3.20', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '78.00', '312.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('299', '2017120899515513', '1', null, 'HIPP喜宝2段婴儿奶粉', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('300', '2017120899519414', '1', null, 'HIPP喜宝2段婴儿奶粉', null, null, '2.00', '1.60', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '144.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('301', '2017120899519414', '2', null, 'HIPP喜宝1段婴儿奶粉', null, null, '4.00', '3.20', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '288.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('302', '2017120899515896', '1', null, 'HIPP喜宝1段婴儿奶粉', null, null, '2.00', '1.60', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '144.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('303', '2017120899515896', '2', null, 'HIPP喜宝2段婴儿奶粉', null, null, '4.00', '3.20', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '288.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('304', '2017120899512800', '1', null, 'COW&GATE牛栏婴儿奶粉4段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('305', '2017120899518875', '1', null, 'HIPP喜宝2段婴儿奶粉', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('306', '2017120899515035', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('307', '2017120899516340', '1', null, 'COW&GATE牛栏婴儿奶粉4段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('308', '2017120899514956', '1', null, 'COW&GATE牛栏婴儿奶粉3段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('309', '2017120899516341', '1', null, 'HIPP喜宝1段婴儿奶粉', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('310', '2017120899516459', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('311', '2017120897986353', '1', null, 'SMA惠氏婴儿奶粉3段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '75.00', '450.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('312', '2017120897981283', '1', null, 'SMA惠氏婴儿奶粉3段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '75.00', '450.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('313', '2017120897982178', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('314', '2017120898569242', '1', null, 'COW&GATE牛栏婴儿奶粉3段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('315', '2017120898564366', '1', null, 'COW&GATE牛栏婴儿奶粉4段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('316', '2017120898563369', '1', null, 'COW&GATE牛栏婴儿奶粉2段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '76.00', '456.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('317', '2017120898567358', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('318', '2017120898567888', '1', null, 'HIPP喜宝1段婴儿奶粉', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('319', '2017120898568596', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('320', '2017120898568666', '1', null, 'APTAMIL爱他美婴儿奶粉4段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '78.00', '468.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('321', '2017120898569073', '1', null, 'COW&GATE牛栏婴儿奶粉4段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('322', '2017120898568086', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('323', '2017120898563045', '1', null, 'COW&GATE牛栏婴儿奶粉4段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('324', '2017120898564234', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('325', '2017120897482212', '1', null, 'HIPP喜宝2段婴儿奶粉', null, null, '1.00', '0.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '72.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('326', '2017120897482212', '2', null, 'HIPP喜宝1段婴儿奶粉', null, null, '5.00', '4.00', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '360.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('327', '2017120897483741', '1', null, 'HIPP喜宝1段婴儿奶粉', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('328', '2017120897482045', '1', null, 'HIPP喜宝2段婴儿奶粉', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('329', '2017120897483780', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('330', '2017120897485450', '1', null, 'APTAMIL爱他美婴儿奶粉1段', null, null, '1.00', '0.90', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '82.00', '82.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('331', '2017120897485450', '2', null, 'COW&GATE牛栏婴儿奶粉3段', null, null, '2.00', '1.80', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '72.00', '144.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('332', '2017120897485450', '3', null, 'NESTLE雀巢成人奶粉', null, null, '3.00', '2.70', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '150.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('333', '2017120897488146', '1', null, 'HIPP喜宝2段婴儿奶粉', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('334', '2017120897483692', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('335', '2017120897483917', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('336', '2017120897488238', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('337', '2017120897489506', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('338', '2017120897487444', '1', null, 'COW&GATE牛栏婴儿奶粉1段', null, null, '1.00', '0.90', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '76.00', '76.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('339', '2017120897487444', '2', null, 'APTAMIL爱他美婴儿奶粉4段', null, null, '5.00', '4.00', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '78.00', '390.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('340', '2017120897489831', '1', null, 'COW&GATE牛栏婴儿奶粉4段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('341', '2017120897489889', '1', null, 'COW&GATE牛栏婴儿奶粉4段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('342', '2017120897485364', '1', null, 'COW&GATE牛栏婴儿奶粉4段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('343', '2017120897484294', '1', null, 'COW&GATE牛栏婴儿奶粉3段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('344', '2017120897481801', '1', null, 'COW&GATE牛栏婴儿奶粉3段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('345', '2017120897486403', '1', null, 'COW&GATE牛栏婴儿奶粉3段', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('346', '2017120897488926', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('347', '2017120856566806', '1', null, 'APTAMIL爱他美婴儿奶粉3段', null, null, '2.00', '1.80', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '78.00', '156.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('348', '2017120856566806', '2', null, 'APTAMIL爱他美婴儿奶粉1段', null, null, '4.00', '3.60', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '82.00', '328.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('349', '2017120856561672', '1', null, 'SMA惠氏婴儿奶粉3段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '75.00', '450.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('350', '2017120856563388', '1', null, 'HIPP喜宝1段婴儿奶粉', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('351', '2017120856561698', '1', null, 'NESTLE雀巢成人奶粉', null, null, '6.00', '5.40', null, '122', '035', null, '303', '1901101000', null, '900g/罐', '50.00', '300.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('352', '2017120856563381', '1', null, 'APTAMIL爱他美婴儿奶粉4段', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '78.00', '468.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('353', '2017120856567488', '1', null, 'HIPP喜宝1段婴儿奶粉', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);
INSERT INTO `ceb_order_goods` VALUES ('354', '2017120856562182', '1', null, 'HIPP喜宝1段婴儿奶粉', null, null, '6.00', '4.80', null, '122', '035', null, '303', '1901101000', null, '800g/罐', '72.00', '432.00', '142', null);

-- ----------------------------
-- Table structure for `ceb_order_head`
-- ----------------------------
DROP TABLE IF EXISTS `ceb_order_head`;
CREATE TABLE `ceb_order_head` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_no` varchar(60) NOT NULL COMMENT '交易平台的订单编号，同一交易平台的订单编号应唯一。订单编号长度不能超过60位。',
  `ebp_code` varchar(18) NOT NULL COMMENT '电商平台的海关注册登记编号；电商平台未在海关注册登记，由电商企业发送订单的，以中国电子口岸发布的电商平台标识编号为准。',
  `ebp_name` varchar(100) NOT NULL COMMENT '电商平台的海关注册登记名称；电商平台未在海关注册登记，由电商企业发送订单的，以中国电子口岸发布的电商平台名称为准。',
  `ebc_code` varchar(18) NOT NULL DEFAULT '' COMMENT '电商企业的海关注册登记编号。',
  `ebc_name` varchar(100) NOT NULL COMMENT '电商企业的海关注册登记名称。',
  `goods_value` decimal(19,2) NOT NULL COMMENT '商品实际成交价，含非现金抵扣金额。',
  `freight` decimal(19,2) NOT NULL COMMENT '不包含在商品价格中的运杂费，无则填写"0"。',
  `discount` decimal(19,2) NOT NULL COMMENT '使用积分、虚拟货币、代金券等非现金支付金额，无则填写"0"。',
  `tax_total` decimal(19,2) NOT NULL COMMENT '企业预先代扣的税款金额，无则填写“0”',
  `actural_paid` decimal(19,2) NOT NULL COMMENT '商品价格+运杂费+代扣税款-非现金抵扣金额，与支付凭证的支付金额一致。',
  `currency` char(3) NOT NULL COMMENT '限定为人民币，填写“142”。',
  `buyer_regno` varchar(60) DEFAULT NULL COMMENT '订购人注册号',
  `buyer_name` varchar(60) NOT NULL COMMENT '订购人的真实姓名。',
  `buyer_telephone` varchar(50) NOT NULL COMMENT '订购人联系电话，必须与电子运单的收货人电话一致。',
  `buyer_id_type` char(1) NOT NULL COMMENT '1-身份证,2-其它。限定为身份证，填写“1”。',
  `buyer_id_number` varchar(60) NOT NULL COMMENT '订购人的身份证件号码。',
  `pay_code` varchar(18) DEFAULT NULL COMMENT '支付企业的海关注册登记编号。',
  `pay_name` varchar(100) DEFAULT NULL COMMENT '支付企业在海关注册登记的企业名称。',
  `pay_transaction_id` varchar(60) DEFAULT NULL COMMENT '支付企业唯一的支付流水号。',
  `consignee` varchar(100) NOT NULL COMMENT '收货人姓名，必须与电子运单的收货人姓名一致。',
  `consignee_telephone` varchar(50) NOT NULL COMMENT '收货人联系电话，必须与电子运单的收货人电话一致。',
  `consignee_address` varchar(200) NOT NULL COMMENT '收货地址，必须与电子运单的收货地址一致。',
  `logistics_no` varchar(60) NOT NULL COMMENT '物流企业的运单包裹面单号。同一物流企业的运单编号在6个月内不重复。运单编号长度不能超过60位。',
  `trade_mode` char(4) NOT NULL COMMENT '直购进口填写“9610”，保税进口填写“1210”。',
  `books_no` varchar(60) DEFAULT NULL COMMENT '保税模式必填，填写区内仓储企业在海关备案的账册编号，用于保税进口业务在特殊区域辅助系统记账（二线出区核减）。',
  `license_no` varchar(60) DEFAULT NULL COMMENT '商务主管部门及其授权发证机关签发的进出口货物许可证件的编号',
  `a_country` char(3) NOT NULL COMMENT '直购进口填写起始发出国家（地区）代码，参照《JGS-20 海关业务代码集》的国家（地区）代码表；保税进口填写代码“142”。',
  `freight2` decimal(19,2) NOT NULL COMMENT '物流企业实际收取的运输费用。',
  `insured_fee` decimal(19,2) NOT NULL COMMENT '物流企业实际收取的商品保价费用。',
  `wrap_type` char(1) NOT NULL COMMENT '海关对进出口货物实际采用的外部包装方式的标识代码，采用1 位数字表示，如：木箱、纸箱、桶装、散装、托盘、包、油罐车等',
  `gross_weight` decimal(19,2) NOT NULL COMMENT '毛重',
  `net_weight` decimal(19,2) NOT NULL COMMENT '净重',
  `bill_no` varchar(60) NOT NULL COMMENT '提单号',
  `voyage_no` varchar(60) NOT NULL COMMENT '航班航次号',
  `pack_no` decimal(19,0) NOT NULL COMMENT '件数',
  `oh_note` varchar(1000) DEFAULT NULL COMMENT '备注',
  `declare_status` char(1) DEFAULT '0' COMMENT '报关状态',
  `pay_status` char(1) DEFAULT '0' COMMENT '支付状态',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `batch_time` varchar(60) NOT NULL COMMENT '导入的批次号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  UNIQUE KEY `logistics_no` (`logistics_no`)
) ENGINE=InnoDB AUTO_INCREMENT=1729 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ceb_order_head
-- ----------------------------
INSERT INTO `ceb_order_head` VALUES ('1612', '2017120750548150', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '卓天明', '13500101311', '1', '350182197610255716', '', '', '', '卓天明', '15059419329', '福建省长乐市闽鹏村下厅125号', '997792677335', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-07 12:01:31', '201712071201314474');
INSERT INTO `ceb_order_head` VALUES ('1624', '2017120702989517', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '456.00', '0.00', '0.00', '0.00', '456.00', '142', '', '徐俊瑞', '18711310016', '1', '150121192912205415', '', '', '', '徐俊瑞', '18711310016', '湖南省湘潭市湘潭大学机械工程学院', '99779268773362', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-07 16:16:42', '201712071616423177');
INSERT INTO `ceb_order_head` VALUES ('1674', '2017120800986197', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '卓天明', '13500101311', '1', '350182197610255716', '', '', '', '卓天明', '15059419329', '福建省长乐市闽鹏村下厅125号', '9977926773356', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1675', '2017120800984076', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '唐喜枚', '13873239357', '1', '430321198201137842', '', '', '', '徐俊瑞', '18711310016', '湖南省湘潭市湘潭大学机械工程学院', '9977926773361', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1676', '2017120800984538', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '456.00', '0.00', '0.00', '0.00', '456.00', '142', '', '徐俊瑞', '18711310016', '1', '150121192912205415', '', '', '', '徐俊瑞', '18711310016', '湖南省湘潭市湘潭大学机械工程学院', '9977926773362', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1677', '2017120800986355', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '刘建军', '15211080034', '1', '430922198104102014', '', '', '', '刘建军', '15211080034', '湖南省长沙市雨花区芙蓉中路三段569号第六都18B402', '9977926773363', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1678', '2017120800988843', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '王成', '13956638718', '1', '342601197708080036', '', '', '', '王成', '13956638718', '安徽省巢湖市北门转盘湖畔名俊2售楼部', '9977926773366', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1679', '2017120899514722', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '412.00', '0.00', '0.00', '0.00', '412.00', '142', '', '张丽', '13668065871', '1', '500382199111156420', '', '', '', '张丽', '13668065871', '重庆市合川区钱塘镇交通街137号', '9977926773375', '9610', '', '', '303', '0.00', '0.00', '5', '6.30', '5.00', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1680', '2017120899515513', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '王晖', '15152115923', '1', '320322198412162811', '', '', '', '郑丽华', '15021995387', '上海市浦东新区昌里路340弄42号602', '9977926773376', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1681', '2017120899519414', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '郑丽华', '15021995387', '1', '350425198502030740', '', '', '', '郑丽华', '15021995387', '上海市浦东新区昌里路340弄42号603', '9977926773377', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1682', '2017120899515896', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '乌仁高娃', '18686032210', '1', '152526199112250000', '', '', '', '乌仁高娃', '18686032210', '内蒙古自治区呼和浩特市赛罕区丰州路巨海城9区五号楼-1', '9977926773395', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1683', '2017120899512800', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '贺佳', '13638412552', '1', '430481198202060041', '', '', '', '贺佳', '13638412552', '湖南省永州市道县红星街科委家属院内', '9977926773405', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1684', '2017120899518875', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '黄艳琴', '13824085996', '1', '440781199010242345', '', '', '', '黄艳琴', '13824085996', '广东省开平市光明路27号爱琴海', '9977926773489', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1685', '2017120899515035', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '王若芬', '13489177760', '1', '350181197903186981', '', '', '', '张谊', '18978192811', '广西壮族自治区崇左市龙州县疾病预防控制中心隔壁', '9977926773492', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1686', '2017120899516340', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '张谊', '18978192811', '1', '452133197311100317', '', '', '', '张谊', '18978192811', '广西壮族自治区崇左市龙州县疾病预防控制中心', '9977926773493', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1687', '2017120899514956', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '李炳泉', '13906218010', '1', '32052419471228391X', '', '', '', '李炳泉', '13906218010', '江苏省苏州市相城区元和街道花南家园2幢501', '9977926773499', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1688', '2017120899516341', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '陈春妹', '13502555585', '1', '44132319510628204X', '', '', '', '陈春妹', '13422903896', '广东省惠州市惠东县稔山镇大墩村1号', '9977926773501', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1689', '2017120899516459', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '孙曼华', '13567111053', '1', '330125195502280025', '', '', '', '孙曼华', '13567111053', '浙江省杭州市余杭顺平邱山大街607号名门天第1幢1102', '9977926773504', '9610', '', '', '303', '0.00', '0.00', '5', '6.90', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1690', '2017120897986353', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '450.00', '0.00', '0.00', '0.00', '450.00', '142', '', '周盐娟', '15980542699', '1', '35012619770124726X', '', '', '', '周盐娟', '15980542699', '福建省长乐市文岭镇山边刘村新街7', '9977926773506', '9610', '', '', '303', '0.00', '0.00', '5', '6.00', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1691', '2017120897981283', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '450.00', '0.00', '0.00', '0.00', '450.00', '142', '', '王晓乐', '13804268430', '1', '210282198712061727', '', '', '', '周盐娟', '15980542699', '福建省长乐市文岭镇山边刘村新街9', '9977926773509', '9610', '', '', '303', '0.00', '0.00', '5', '6.00', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1692', '2017120897982178', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '严米费', '18684985254', '1', '430203199007017528', '', '', '', '严米费', '18684985254', '湖南省株洲市石峰区化工厂生活区一区34栋', '9977926773512', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 10:32:43', '201712081032431133');
INSERT INTO `ceb_order_head` VALUES ('1693', '2017120898569242', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '陈宝珠', '15059419329', '1', '35012619780907652X', '', '', '', '陈宝珠', '15059419329', '福建省长乐市闽鹏村下厅124号', '9977926773357', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1694', '2017120898564366', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '谭小丽', '13652801181', '1', '44530219881207004X', '', '', '', '刘金刚', '15080758961', '湖南省湘潭市湘潭大学机械工程学院办公室', '9977926773358', '9610', '', '', '303', '0.00', '0.00', '5', '6.00', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1695', '2017120898563369', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '456.00', '0.00', '0.00', '0.00', '456.00', '142', '', '刘金刚', '15080758961', '1', '420111197903087616', '', '', '', '刘金刚', '15080758961', '湖南省湘潭市湘潭大学机械工程门卫', '9977926773359', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1696', '2017120898567358', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '唐慧', '15007325797', '1', '430302198204280063', '', '', '', '徐俊瑞', '18711310016', '湖南省湘潭市湘潭大学机械工程学院', '9977926773360', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1697', '2017120898567888', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '郑建', '13637211113', '1', '34252919900529007X', '', '', '', '郑建', '13637211113', '安徽省宣城市泾川镇财富大道贺村门面2-3号', '9977926773364', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1698', '2017120898568596', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '康元元', '13292850318', '1', '130184198501240028', '', '', '', '康元元', '13292850318', '河北省石家庄市谈固东街129号银通小区41号楼1单元303', '9977926773365', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1699', '2017120898568666', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '468.00', '0.00', '0.00', '0.00', '468.00', '142', '', '刘文兵', '15375088808', '1', '342601198801163017', '', '', '', '刘文兵', '15375088808', '安徽省合肥市卧牛路巢湖市规划局', '9977926773367', '9610', '', '', '303', '0.00', '0.00', '5', '6.00', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1700', '2017120898569073', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '汪静', '18638057988', '1', '412728198710267869', '', '', '', '陈宇', '15674576246', '湖南省怀化市城东广电花园5305室', '9977926773368', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1701', '2017120898568086', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '王超英', '13960708386', '1', '350181199404191662', '', '', '', '陈宇', '15674576246', '湖南省怀化市城东广电花园5303室', '9977926773369', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1702', '2017120898563045', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '陈宇', '15674576246', '1', '433024197508180010', '', '', '', '陈宇', '15674576246', '湖南省怀化市城东广电花园5304室', '9977926773370', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1703', '2017120898564234', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '彭叶辉', '15273278567', '1', '430522197605126376', '', '', '', '彭叶辉', '15273278567', '湖南省湘潭市雨湖区桃园路湖南科技大学', '9977926773371', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1704', '2017120897482212', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '马庆奇', '13512561519', '1', '320826197903030001', '', '', '', '马庆奇', '13512561519', '江苏省徐州市天能家园北1号楼2单元202', '9977926773372', '9610', '', '', '303', '0.00', '0.00', '5', '5.75', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1705', '2017120897483741', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '刘曙娥', '17701580058', '1', '220104197710151529', '', '', '', '刘曙娥', '17701580058', '江苏省南京市将军大道33号挪威森林小区11栋405室', '9977926773373', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1706', '2017120897482045', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '王春花', '15883664020', '1', '51060219810114134x', '', '', '', '马庆奇', '13512561519', '江苏省徐州市天能家园北1号楼2单元203', '9977926773374', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1707', '2017120897483780', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '闫昱男', '18712133343', '1', '342224198709230021', '', '', '', '闫昱男', '18712133343', '安徽省宿州市灵璧县灵城镇凤山豪庭20栋401室', '9977926773378', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1708', '2017120897485450', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '376.00', '0.00', '0.00', '0.00', '376.00', '142', '', '漆一萱', '13507825743', '1', '452227199412070265', '', '', '', '漆一萱', '13507825743', '广西壮族自治区柳州市融安县铁路派出所隔壁', '9977926773379', '9610', '', '', '303', '0.00', '0.00', '5', '6.75', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1709', '2017120897488146', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '王金福', '15060065888', '1', '350181198402221771', '', '', '', '乌仁高娃', '18686032210', '内蒙古自治区呼和浩特市赛罕区丰州路巨海城9区五号楼', '9977926773396', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1710', '2017120897483692', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '温静娴', '18273165452', '1', '430602198708136029', '', '', '', '温静娴', '18273165452', '湖南省株洲市石峰区田心街道田心立交桥亿都国际', '9977926773397', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1711', '2017120897483917', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '王玲', '18770591778', '1', '36220419891116656X', '', '', '', '姜欢', '13638302223', '重庆市长寿区凤城街道黄桷逸景高层8_2', '9977926773398', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1712', '2017120897488238', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '姜欢', '13638302223', '1', '500221199308313620', '', '', '', '姜欢', '13638302223', '重庆市长寿区凤城街道黄桷逸景高层8_1', '9977926773399', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1713', '2017120897489506', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '刘夏莲', '13551050600', '1', '510722198406034925', '', '', '', '刘夏莲', '13551050600', '四川省成都市高新区锦城公园西门c栋文化运动中心一楼', '9977926773488', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1714', '2017120897487444', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '466.00', '0.00', '0.00', '0.00', '466.00', '142', '', '都艳', '15942882786', '1', '210212198912040542', '', '', '', '都艳', '15942882786', '辽宁省大连市旅顺口区向阳街道西二巷39-202', '9977926773490', '9610', '', '', '303', '0.00', '0.00', '5', '6.10', '4.90', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1715', '2017120897489831', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '王容辉', '15980151908', '1', '352227199002284523', '', '', '', '黄慧斯', '13978100235', '广西壮族自治区崇左市龙州县龙庭一品十一栋商铺18-19号唯美口腔隔壁', '9977926773491', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1716', '2017120897489889', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '高弘', '15677150886', '1', '450205194707280021', '', '', '', '高弘', '15677150886', '广西壮族自治区南宁市西乡塘大学东路170号25栋2单元701', '9977926773494', '9610', '', '', '303', '0.00', '0.00', '5', '6.00', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1717', '2017120897485364', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '黄慧斯', '13978100235', '1', '45260119861130032X', '', '', '', '黄慧斯', '13978100235', '广西壮族自治区崇左市龙州县龙庭一品十一栋商铺18-19号唯美口腔', '9977926773495', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1718', '2017120897484294', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '吴佩芳', '13812789335', '1', '320586198106253945', '', '', '', '吴佩芳', '13812789335', '江苏省苏州市相城区开发区徐庄一村36', '9977926773497', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1719', '2017120897481801', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '李介明', '13901540925', '1', '320524196808133915', '', '', '', '李介明', '13901540925', '江苏省苏州市相城区黄桥镇苏埭路2', '9977926773498', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1720', '2017120897486403', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '吴彩珍', '15851462988', '1', '320524196804013940', '', '', '', '吴彩珍', '15851462988', '江苏省苏州市相城区元和街道香城花园43幢102', '9977926773500', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1721', '2017120897488926', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '林朝英', '13422903896', '1', '441323197611132025', '', '', '', '林朝英', '13422903896', '广东省惠州市惠东县稔山镇大墩村', '9977926773502', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1722', '2017120856566806', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '484.00', '0.00', '0.00', '0.00', '484.00', '142', '', '郭本瑞', '15960081380', '1', '350181198405021935', '', '', '', '郭本瑞', '15960081380', '福建省福清市阳下镇北林村208', '9977926773503', '9610', '', '', '303', '0.00', '0.00', '5', '2.20', '1.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1723', '2017120856561672', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '450.00', '0.00', '0.00', '0.00', '450.00', '142', '', '王珊珊', '13726929366', '1', '420682199012133027', '', '', '', '周盐娟', '15980542699', '福建省长乐市文岭镇山边刘村新街8', '9977926773505', '9610', '', '', '303', '0.00', '0.00', '5', '6.00', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1724', '2017120856563388', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '142', '', '杨艳', '13702058122', '1', '120113198309153226', '', '', '', '杨艳', '13702058122', '天津市武清区五一阳光锦园24号楼602', '9977926773508', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1725', '2017120856561698', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '300.00', '0.00', '0.00', '0.00', '300.00', '142', '', '刘文龙', '15802961697', '1', '610102198901143112', '', '', '', '刘文龙', '15802961697', '陕西省西安市新城区幸福中路107号幸福制药', '9977926773516', '9610', '', '', '303', '0.00', '0.00', '5', '6.60', '5.40', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1726', '2017120856563381', '44079610EA', '广东就手电子商务有限公司', '44079610EA', '广东就手电子商务有限公司', '468.00', '0.00', '0.00', '0.00', '468.00', '142', '', '吴碧芳', '15859141717', '1', '35018119880801190X', '', '', '', '吴碧芳', '15859141717', '福建省福清市音西街道融侨城26号401', '9977926773517', '9610', '', '', '303', '0.00', '0.00', '5', '6.00', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1727', '2017120856567488', '44079611EA', '广东就手电子商务有限公司', '44079611EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '143', '', '王燕', '15953708788', '1', '370911198306136428', '', '', '', '左林芳', '17707433556', '湖南省吉首市乾州建设工程质量检测中心隔壁', '9977926773519', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');
INSERT INTO `ceb_order_head` VALUES ('1728', '2017120856562182', '44079612EA', '广东就手电子商务有限公司', '44079612EA', '广东就手电子商务有限公司', '432.00', '0.00', '0.00', '0.00', '432.00', '144', '', '左林芳', '17707433556', '1', '433101198810160024', '', '', '', '左林芳', '17707433556', '湖南省吉首市乾州建设工程质量检测中心', '9977926773520', '9610', '', '', '303', '0.00', '0.00', '5', '5.70', '4.80', '15732773053', 'QR028', '6', null, '0', '0', '2017-12-08 17:04:24', '201712081704245024');

-- ----------------------------
-- Table structure for `ceb_order_preview`
-- ----------------------------
DROP TABLE IF EXISTS `ceb_order_preview`;
CREATE TABLE `ceb_order_preview` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_no` varchar(60) NOT NULL COMMENT '交易平台的订单编号，同一交易平台的订单编号应唯一。订单编号长度不能超过60位。',
  `ebp_code` varchar(18) NOT NULL COMMENT '电商平台的海关注册登记编号；电商平台未在海关注册登记，由电商企业发送订单的，以中国电子口岸发布的电商平台标识编号为准。',
  `ebp_name` varchar(100) NOT NULL COMMENT '电商平台的海关注册登记名称；电商平台未在海关注册登记，由电商企业发送订单的，以中国电子口岸发布的电商平台名称为准。',
  `ebc_code` varchar(18) NOT NULL DEFAULT '' COMMENT '电商企业的海关注册登记编号。',
  `ebc_name` varchar(100) NOT NULL COMMENT '电商企业的海关注册登记名称。',
  `goods_value` decimal(19,2) NOT NULL COMMENT '商品实际成交价，含非现金抵扣金额。',
  `freight` decimal(19,2) NOT NULL COMMENT '不包含在商品价格中的运杂费，无则填写"0"。',
  `discount` decimal(19,2) NOT NULL COMMENT '使用积分、虚拟货币、代金券等非现金支付金额，无则填写"0"。',
  `tax_total` decimal(19,2) NOT NULL COMMENT '企业预先代扣的税款金额，无则填写“0”',
  `actural_paid` decimal(19,2) NOT NULL COMMENT '商品价格+运杂费+代扣税款-非现金抵扣金额，与支付凭证的支付金额一致。',
  `currency` char(3) NOT NULL COMMENT '限定为人民币，填写“142”。',
  `buyer_regno` varchar(60) DEFAULT NULL COMMENT '订购人注册号',
  `buyer_name` varchar(60) NOT NULL COMMENT '订购人的真实姓名。',
  `buyer_telephone` varchar(50) NOT NULL COMMENT '收货人联系电话，必须与电子运单的收货人电话一致。',
  `buyer_id_type` char(1) NOT NULL COMMENT '1-身份证,2-其它。限定为身份证，填写“1”。',
  `buyer_id_number` varchar(60) NOT NULL COMMENT '订购人的身份证件号码。',
  `pay_code` varchar(18) DEFAULT NULL COMMENT '支付企业的海关注册登记编号。',
  `pay_name` varchar(100) DEFAULT NULL COMMENT '支付企业在海关注册登记的企业名称。',
  `pay_transaction_id` varchar(60) DEFAULT NULL COMMENT '支付企业唯一的支付流水号。',
  `consignee` varchar(100) NOT NULL COMMENT '收货人姓名，必须与电子运单的收货人姓名一致。',
  `consignee_telephone` varchar(50) NOT NULL COMMENT '收货人联系电话，必须与电子运单的收货人电话一致。',
  `consignee_address` varchar(200) NOT NULL COMMENT '收货地址，必须与电子运单的收货地址一致。',
  `logistics_no` varchar(60) NOT NULL COMMENT '物流企业的运单包裹面单号。同一物流企业的运单编号在6个月内不重复。运单编号长度不能超过60位。',
  `trade_mode` char(4) NOT NULL COMMENT '直购进口填写“9610”，保税进口填写“1210”。',
  `books_no` varchar(60) DEFAULT NULL COMMENT '保税模式必填，填写区内仓储企业在海关备案的账册编号，用于保税进口业务在特殊区域辅助系统记账（二线出区核减）。',
  `license_no` varchar(60) DEFAULT NULL COMMENT '商务主管部门及其授权发证机关签发的进出口货物许可证件的编号',
  `a_country` char(3) NOT NULL COMMENT '直购进口填写起始发出国家（地区）代码，参照《JGS-20 海关业务代码集》的国家（地区）代码表；保税进口填写代码“142”。',
  `freight2` decimal(19,2) NOT NULL COMMENT '物流企业实际收取的运输费用。',
  `insured_fee` decimal(19,2) NOT NULL COMMENT '物流企业实际收取的商品保价费用。',
  `wrap_type` char(1) NOT NULL COMMENT '海关对进出口货物实际采用的外部包装方式的标识代码，采用1 位数字表示，如：木箱、纸箱、桶装、散装、托盘、包、油罐车等',
  `gross_weight` decimal(19,2) NOT NULL COMMENT '毛重',
  `net_weight` decimal(19,2) NOT NULL COMMENT '净重',
  `bill_no` varchar(60) NOT NULL COMMENT '提单号',
  `voyage_no` varchar(60) NOT NULL COMMENT '航班航次号',
  `pack_no` decimal(19,0) NOT NULL COMMENT '件数',
  `oh_note` varchar(1000) DEFAULT NULL COMMENT '备注',
  `gnum` decimal(4,0) NOT NULL COMMENT '从1开始的递增序号。',
  `item_no` varchar(30) DEFAULT NULL COMMENT '电商企业自定义的商品货号（SKU）。',
  `item_name` varchar(250) NOT NULL COMMENT '交易平台销售商品的中文名称。',
  `item_describe` varchar(1000) DEFAULT NULL COMMENT '交易平台销售商品的描述信息。',
  `bar_code` varchar(50) DEFAULT NULL COMMENT '国际通用的商品条形码，一般由前缀部分、制造厂商代码、商品代码和校验码组成。',
  `qty` decimal(19,2) NOT NULL COMMENT '按成交计量单位的实际数量',
  `qty1` decimal(19,2) NOT NULL COMMENT '按照商品编码规则对应的法定计量单位的实际数量填写。',
  `qty2` decimal(19,2) DEFAULT NULL COMMENT '第二数量',
  `unit` char(3) NOT NULL COMMENT '海关标准的参数代码  《JGS-20 海关业务代码集》- 计量单位代码(计量单位)',
  `unit1` char(3) NOT NULL COMMENT '海关标准的参数代码 《JGS-20 海关业务代码集》- 计量单位代码(法定计量单位)',
  `unit2` char(3) DEFAULT NULL COMMENT '海关标准的参数代码 《JGS-20 海关业务代码集》- 计量单位代码(第二计量单位)',
  `country` char(3) NOT NULL COMMENT '填写海关标准的参数代码，参照《JGS-20 海关业务代码集》-国家（地区）代码表。',
  `hscode` varchar(60) NOT NULL COMMENT '海关商品编码(Hscode)',
  `gjcode` varchar(60) DEFAULT NULL COMMENT '国检商品编号',
  `gtype` varchar(100) NOT NULL COMMENT '商品规格型号',
  `price` decimal(19,2) NOT NULL COMMENT '商品单价。赠品单价填写为“0”。',
  `total_price` decimal(19,2) NOT NULL COMMENT '商品总价，等于单价乘以数量。',
  `item_currency` char(3) NOT NULL COMMENT '限定为人民币，填写“142”。',
  `goods_note` varchar(1000) DEFAULT NULL COMMENT '促销活动，商品单价偏离市场价格的，可以在此说明。',
  `batch_no` varchar(60) NOT NULL COMMENT '批次号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=288 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ceb_order_preview
-- ----------------------------
