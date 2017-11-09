<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$sql = <<<EOF

CREATE TABLE IF NOT EXISTS `pre_wxz_live_player` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL DEFAULT '0',
  `video_type` tinyint(2) NOT NULL DEFAULT '0',
  `settings` text NOT NULL DEFAULT '',
  `player_weight` int(10) NOT NULL DEFAULT '1280',
  `player_height` int(10) NOT NULL DEFAULT '720',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE
);

CREATE TABLE IF NOT EXISTS `pre_wxz_live_room_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL DEFAULT '0',
  `activity_title` varchar(255) NOT NULL DEFAULT '',
  `activity_img` varchar(255) NOT NULL DEFAULT '',
  `default_avatar` varchar(255) NOT NULL DEFAULT '',
  `activity_desc` varchar(255) NOT NULL DEFAULT '',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `follow_qrcode` varchar(255) NOT NULL DEFAULT '',
  `follow_url` varchar(255) NOT NULL DEFAULT '',
  `follow_type` TINYINT(1) NOT NULL DEFAULT 1,
  `follow_cache_day` int(3) NOT NULL DEFAULT 1,
  `share_img` varchar(255) NOT NULL DEFAULT '',
  `share_title` varchar(255) NOT NULL DEFAULT '',
  `share_desc` varchar(255) NOT NULL DEFAULT '',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
);

CREATE TABLE IF NOT EXISTS `pre_wxz_live_room` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `room_no` varchar(255) NOT NULL DEFAULT '',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',      
  `start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_time` datetime NOT NULL  DEFAULT '0000-00-00 00:00:00',
  `live_status` tinyint(1) NOT NULL DEFAULT 1,
  `countdown_style` varchar(255) NOT NULL DEFAULT '',      
  `style` tinyint(1) NOT NULL DEFAULT 1,
  `limit` tinyint(1) NOT NULL DEFAULT 1,
  `limit_data` varchar(255) NOT NULL DEFAULT '',
  `style_extend` varchar(255) NOT NULL DEFAULT '',        
  `station_caption` varchar(255) NOT NULL DEFAULT '',        
  `theme_pic` varchar(255) NOT NULL DEFAULT '',      
  `cover_pic` varchar(255) NOT NULL DEFAULT '',        
  `is_show_follow_btn` tinyint(1) NOT NULL DEFAULT 1, 
  `publisher` varchar(255) NOT NULL DEFAULT '',    
  `publisher_avatar` varchar(255) NOT NULL DEFAULT '',  
  `online_user_config` varchar(255) NOT NULL DEFAULT '',  
  `check_comment` tinyint(1) NOT NULL DEFAULT 1,
  `enable_redpacket` tinyint(1) NOT NULL DEFAULT 1,
  `is_show_copyright` tinyint(1) NOT NULL DEFAULT 1, 
  `copyright` varchar(255) NOT NULL DEFAULT '',
  `rule` text NOT NULL DEFAULT '',
  `linkurl` varchar(255) DEFAULT '',
  `islinkurl` tinyint(1) DEFAULT '2',
  `is_show` tinyint(1) DEFAULT '1',
  `is_show_list` tinyint(1) DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `base_num` int(11) NOT NULL DEFAULT '0',
  `total_num` int(11) NOT NULL DEFAULT '0',
  `view_num` int(11) NOT NULL DEFAULT '0',    
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_no` (`room_no`)
);

        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `activity_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `icon` varchar(255) DEFAULT '',
  `desc` varchar(1000) DEFAULT '',
  `is_show` tinyint(1) DEFAULT '2',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_id` (`activity_id`)
);

CREATE TABLE IF NOT EXISTS `pre_wxz_live_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL DEFAULT '0',
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `headimgurl` varchar(255) NOT NULL DEFAULT '',
  `province` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `sex` varchar(255) NOT NULL DEFAULT '',
  `sub_openid` varchar(255) NOT NULL DEFAULT '',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `is_vip` tinyint(1) NOT NULL DEFAULT '1',
  `vip_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vip_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_id` (`activity_id`) USING BTREE,
  KEY `sub_openid` (`sub_openid`) USING BTREE,
  KEY `openid` (`openid`) USING BTREE
);
        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_viewer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `share` tinyint(1) DEFAULT '0',
  `amount` int(10) DEFAULT '0',
  `ispay` tinyint(1) DEFAULT '0',
  `rlog` varchar(255) DEFAULT NULL,
  `deposit` int(10) DEFAULT '0',
  `password` varchar(255) DEFAULT NULL,
  `isshutup` tinyint(1) DEFAULT '0',
  `role` tinyint(1) DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
);

 CREATE TABLE IF NOT EXISTS `pre_wxz_live_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(10) DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `is_show` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `settings` text NOT NULL DEFAULT '',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `is_show` (`is_show`) USING BTREE
);

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
  `samount` text NOT NULL DEFAULT '',
  `syifa` text NOT NULL DEFAULT '',
  `dsid` int(10) NOT NULL DEFAULT '0',
  `dsstatus` int(1) NOT NULL DEFAULT '0',
  `dsamount` int(10) NOT NULL DEFAULT '0',
  `giftid` int(10) NOT NULL DEFAULT '0',
  `giftnum` int(10) NOT NULL DEFAULT '0',
  `giftpic` varchar(255) NOT NULL DEFAULT '',
  `giftstatus` tinyint(1) NOT NULL DEFAULT '0',
  `gid` int(10) NOT NULL DEFAULT '0',
  `groupid` int(10) NOT NULL DEFAULT '0',
  `groupamount` int(10) NOT NULL DEFAULT '0',
  `ispic` tinyint(1) NOT NULL DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,    
  PRIMARY KEY (`id`),
  KEY `is_auth` (`is_auth`) USING BTREE,
  KEY `rid` (`rid`) USING BTREE
);

CREATE TABLE IF NOT EXISTS `pre_wxz_live_polling` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(10) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `comment_id` int(10) NOT NULL DEFAULT '0',
  `pic_id` int(10) NOT NULL DEFAULT '0',
  `black_id` int(10) NOT NULL DEFAULT '0',
  `live_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `type` (`type`) USING BTREE
);
        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_setting` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(50) NOT NULL DEFAULT '',
  `title` VARCHAR(250) NOT NULL DEFAULT '',
  `desc` text NOT NULL DEFAULT '',
  `img` VARCHAR(255) NOT NULL DEFAULT '',
  `link` VARCHAR(255) NOT NULL DEFAULT '',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `pre_wxz_live_banner` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `img` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `is_show` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL DEFAULT '0',
  `rid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `order_type` int(11) NOT NULL DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '1',
  `money` int(11) NOT NULL DEFAULT '0',
  `pay_money` int(11) NOT NULL DEFAULT '0',
  `fail_reason` varchar(255) NOT NULL DEFAULT '',
  `trade_no` varchar(50) NOT NULL DEFAULT '',
  `ext` varchar(1000) NOT NULL DEFAULT '',
  `success_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`),
  KEY `uid` (`uid`),
  KEY `order_no` (`order_no`)
);

CREATE TABLE IF NOT EXISTS `pre_wxz_live_zanpic` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `pic` varchar(255) NOT NULL DEFAULT '',
    `rid` int(10) unsigned NOT NULL DEFAULT '0',
    `is_show` tinyint(1) DEFAULT NULL DEFAULT '1',
    `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `rid` (`rid`) USING BTREE,
    KEY `is_show` (`is_show`) USING BTREE
);
        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_zannum` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `rid` int(10) unsigned NOT NULL DEFAULT '0',
    `uid` int(10) unsigned NOT NULL DEFAULT '0',
    `num` int(10) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `rid` (`rid`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE
);

CREATE TABLE IF NOT EXISTS `pre_wxz_live_reward` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fee` varchar(20) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT '0',
  `rid` int(10) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `touid` int(10) NOT NULL DEFAULT '0',
  `tonickname` varchar(255) NOT NULL DEFAULT '',
  `toheadurl` varchar(255) NOT NULL DEFAULT '',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `order_id` (`order_id`),
  KEY `status` (`status`)
);

CREATE TABLE IF NOT EXISTS `pre_wxz_live_gift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `pic` varchar(255) NOT NULL DEFAULT '',
  `amount` int(10) NOT NULL DEFAULT 0,
  `rid` int(10) NOT NULL DEFAULT 0,
  `is_show` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) NOT NULL DEFAULT 0,
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE
);

CREATE TABLE IF NOT EXISTS  `pre_wxz_live_grouppacket` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT 0,
  `order_id` int(10) unsigned NOT NULL DEFAULT 0,
  `rid` int(10) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `amount` int(10) NOT NULL DEFAULT 0,
  `num` int(10) NOT NULL DEFAULT 0,
  `json` text NOT NULL DEFAULT '',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE
);

CREATE TABLE IF NOT EXISTS `pre_wxz_live_giftlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT 0,
  `order_id` int(10) unsigned NOT NULL DEFAULT 0,
  `giftid` int(10) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT '0',
  `rid` int(10) NOT NULL DEFAULT '0',
  `num` int(10) NOT NULL DEFAULT 0,
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `order_id` (`order_id`),
  KEY `giftid` (`giftid`) USING BTREE
);
        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_money_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(10) NOT NULL DEFAULT 0,
  `uid` int(10) NOT NULL DEFAULT 0,
  `amount` int(10) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `fromuid` int(10) NOT NULL DEFAULT 0,
  `fromnickname` varchar(255) NOT NULL DEFAULT '',
  `fromheadimgurl` varchar(255) NOT NULL DEFAULT '',
  `fromid` int(10) NOT NULL DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
);

CREATE TABLE IF NOT EXISTS `pre_wxz_live_grouppacket_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `hid` int(10) NOT NULL DEFAULT 0,
  `rid` int(10) NOT NULL DEFAULT 0,
  `uid` int(10) NOT NULL DEFAULT 0,
  `comment_id` int(10) NOT NULL DEFAULT 0,
  `amount` int(10) NOT NULL DEFAULT 0,
  `headimgurl` varchar(255) NOT NULL DEFAULT '',
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
        
        
EOF;

runquery($sql);

$finish = TRUE;