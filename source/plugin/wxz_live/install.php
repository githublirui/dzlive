<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

CREATE TABLE IF NOT EXISTS `pre_wxz_live_room` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '创建者id',
  `title` varchar(255) DEFAULT '' COMMENT '直播间标题',
  `img` varchar(255) DEFAULT '' COMMENT '直播间封面',
  `activity_title` varchar(255) DEFAULT '' COMMENT '活动标题',
  `activity_img` varchar(255) DEFAULT '' COMMENT '活动封面',
  `activity_desc` varchar(255) DEFAULT '' COMMENT '活动描述',
  `avatar` varchar(255) DEFAULT '' COMMENT '默认游客头像',
  `follow_qrcode` varchar(255) DEFAULT '' COMMENT '引导关注二维码',
  `follow_type` TINYINT(1) DEFAULT 1 COMMENT '关注模式 1. 强制关注 2.用户点击弹出二维码 3.自动弹出可关闭二维码',
  `is_show` tinyint(1) DEFAULT '2' COMMENT '1不展示，2展示',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '直播间信息基础表';
 
CREATE TABLE IF NOT EXISTS `pre_wxz_live_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '创建者id',
  `activity_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动id',
  `name` varchar(255) DEFAULT '' COMMENT '分类名称',
  `icon` varchar(255) DEFAULT '' COMMENT '分类icon',
  `desc` varchar(1000) DEFAULT '' COMMENT '分类说明',
  `is_show` tinyint(1) DEFAULT '2' COMMENT '1不展示，2展示',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pre_wxz_live_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动id',
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `sub_openid` varchar(255) DEFAULT NULL COMMENT 'openid',
  `openid` varchar(255) DEFAULT NULL COMMENT '订阅号openid',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_id` (`activity_id`) USING BTREE,
  KEY `sub_openid` (`sub_openid`) USING BTREE,
  KEY `openid` (`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pre_wxz_live_setting` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(50) NOT NULL COMMENT '页面类型',
  `title` VARCHAR(250) NOT NULL COMMENT '标题',
  `desc` VARCHAR(2000) NOT NULL COMMENT '描述',
  `img` VARCHAR(255) NOT NULL COMMENT '图片',
  `link` VARCHAR(255) NOT NULL COMMENT '链接地址',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;    

CREATE TABLE `pre_wxz_live_banner` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `img` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOF;

runquery($sql);

$finish = TRUE;
?>