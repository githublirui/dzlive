<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

CREATE TABLE IF NOT EXISTS `pre_wxz_live_player` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL DEFAULT '0' COMMENT '房间id',
  `video_type` tinyint(2) DEFAULT NULL,
  `settings` text,
  `player_weight` int(10) DEFAULT '1280',
  `player_height` int(10) DEFAULT '720',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `pre_wxz_live_room_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL DEFAULT '0' COMMENT '房间id',
  `activity_title` varchar(255) DEFAULT '' COMMENT '活动标题',
  `activity_img` varchar(255) DEFAULT '' COMMENT '活动封面',
  `default_avatar` varchar(255) DEFAULT '' COMMENT '默认游客头像',
  `activity_desc` varchar(255) DEFAULT '' COMMENT '活动描述',
  `avatar` varchar(255) DEFAULT '' COMMENT '默认游客头像',
  `follow_qrcode` varchar(255) DEFAULT '' COMMENT '引导关注二维码',
  `follow_url` varchar(255) DEFAULT '' COMMENT '引导关注链接',
  `follow_type` TINYINT(1) DEFAULT 1 COMMENT '关注模式 1. 强制关注 2.用户点击弹出二维码 3.自动弹出可关闭二维码',
  `follow_cache_day` int(3) DEFAULT 1 COMMENT '关注模式三缓存时间',
  `share_img` varchar(255) NOT NULL COMMENT '分享图片',
  `share_title` varchar(255) NOT NULL COMMENT '分享标题',
  `share_desc` varchar(255) NOT NULL COMMENT '分享描述',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '直播间配置表';

CREATE TABLE IF NOT EXISTS `pre_wxz_live_room` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `room_no` varchar(255) DEFAULT '' COMMENT '房间号',
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '分类id',
  `title` varchar(255) DEFAULT '' COMMENT '直播间标题',      
  `start_time` datetime NOT NULL COMMENT '直播开始时间',
  `end_time` datetime NOT NULL COMMENT '直播结束时间',
  `live_status` tinyint(1) DEFAULT 1 COMMENT '直播状态 1. 即将直播 2.直播中 3. 回播',
  `countdown_style` varchar(255) DEFAULT '' COMMENT '直播间倒计时颜色',      
  `style` tinyint(1) DEFAULT 1 COMMENT '风格',
  `limit` tinyint(1) DEFAULT 1 COMMENT '直播访问限制',
  `limit_data` varchar(255) DEFAULT '' COMMENT '直播访问限制配置',
  `style_extend` varchar(255) DEFAULT '' COMMENT '风格额外数据',        
  `station_caption` varchar(255) DEFAULT '' COMMENT '台标，风格2使用',        
  `theme_pic` varchar(255) DEFAULT '' COMMENT '直播间主题图片',      
  `cover_pic` varchar(255) DEFAULT '' COMMENT '封面图片',        
  `is_show_follow_btn` tinyint(1) DEFAULT 1 COMMENT '关注按钮', 
  `publisher` varchar(255) DEFAULT '' COMMENT '图文直播-直播员',    
  `publisher_avatar` varchar(255) DEFAULT '' COMMENT '图文直播-直播员头像',  
  `online_user_config` varchar(255) DEFAULT '' COMMENT '在线人数设置',  
  `check_comment` tinyint(1) DEFAULT 1 COMMENT '评论是否需要审核 1.需要审核 2.不需要审核',
  `enable_redpacket` tinyint(1) DEFAULT 1 COMMENT '是否启用红包 1不启用,2启用',
  `is_show_copyright` tinyint(1) DEFAULT 1 COMMENT '是否显示版权', 
  `copyright` varchar(255) DEFAULT '' COMMENT '显示版权信息',
  `rule` text COMMENT '直播规则',
  `linkurl` varchar(255) DEFAULT '' COMMENT '外链url',
  `islinkurl` tinyint(1) DEFAULT '2' COMMENT '是否外链 1是，2否',
  `is_show` tinyint(1) DEFAULT '1' COMMENT '1展示，2不展示',
  `is_show_list` tinyint(1) DEFAULT 1 COMMENT '是否在列表页显示 1.是 2.否',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `base_num int(11) NOT NULL DEFAULT '0' COMMENT '基础人数',
  `total_num` int(11) NOT NULL DEFAULT '0' COMMENT '总人数',
  `view_num` int(11) NOT NULL DEFAULT '0' COMMENT '浏览数',    
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_no` (`room_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '直播间表';

        
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

CREATE TABLE IF NOT EXISTS `pre_wxz_live_user` (
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
  `is_vip` tinyint(1) DEFAULT '1' COMMENT '1非会员 2会员',
  `vip_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '会员开始日期',
  `vip_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '会员到期日期',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_id` (`activity_id`) USING BTREE,
  KEY `sub_openid` (`sub_openid`) USING BTREE,
  KEY `openid` (`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_viewer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `room_id` int(10) DEFAULT NULL,
  `share` tinyint(1) DEFAULT '0',
  `amount` int(10) DEFAULT '0',
  `ispay` tinyint(1) DEFAULT '0',
  `rlog` varchar(255) DEFAULT NULL,
  `deposit` int(10) DEFAULT '0',
  `password` varchar(255) DEFAULT NULL,
  `isshutup` tinyint(1) DEFAULT '0',
  `role` tinyint(1) DEFAULT '0' COMMENT '0普通1主播2管理员',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE IF NOT EXISTS `pre_wxz_live_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(10) DEFAULT '0',
  `sort_order` int(11) NOT NULL,
  `is_show` tinyint(1) NOT NULL,
  `type` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `settings` text NOT NULL,
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`) USING BTREE,
  KEY `is_show` (`is_show`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pre_wxz_live_comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `content` text,
  `is_auth` tinyint(1) DEFAULT '0',
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  `lid` int(10) DEFAULT '0',
  `touid` int(10) DEFAULT '0',
  `tonickname` varchar(255) DEFAULT NULL,
  `toheadimgurl` varchar(255) DEFAULT NULL,
  `toid` int(10) DEFAULT '0',
  `isadmin` tinyint(1) DEFAULT '0',
  `ispacket` tinyint(1) DEFAULT '0',
  `amount` int(10) DEFAULT '0',
  `num` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `send_num` int(10) DEFAULT '0',
  `yifa_amount` int(10) DEFAULT '0',
  `samount` text,
  `syifa` text,
  `dsid` int(10) DEFAULT '0',
  `dsstatus` int(1) DEFAULT '0',
  `dsamount` int(10) DEFAULT '0',
  `giftid` int(10) DEFAULT NULL,
  `giftnum` int(10) DEFAULT NULL,
  `giftpic` varchar(255) DEFAULT NULL,
  `giftstatus` tinyint(1) DEFAULT '0',
  `gid` int(10) DEFAULT '0',
  `groupid` int(10) DEFAULT '0',
  `groupamount` int(10) DEFAULT '0',
  `ispic` tinyint(1) DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,    
  PRIMARY KEY (`id`),
  KEY `is_auth` (`is_auth`) USING BTREE,
  KEY `rid` (`rid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `wxz_live_polling` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0' COMMENT '1评论2个人禁言3全体禁言4红包5礼物6个人打赏7主播打赏',
  `comment_id` int(10) DEFAULT '0',
  `pic_id` int(10) DEFAULT '0',
  `black_id` int(10) DEFAULT NULL,
  `live_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `type` (`type`) USING BTREE
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

CREATE TABLE IF NOT EXISTS `pre_wxz_live_banner` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `img` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT NULL DEFAULT '1' COMMENT '1显示 2不显示',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOF;

runquery($sql);

$finish = TRUE;
?>