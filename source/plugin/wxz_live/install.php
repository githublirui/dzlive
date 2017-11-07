<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$sql = <<<EOF

CREATE TABLE IF NOT EXISTS `pre_wxz_live_player` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `video_type` tinyint(2) DEFAULT NULL,
  `settings` text,
  `player_weight` int(10) DEFAULT '1280',
  `player_height` int(10) DEFAULT '720',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `pre_wxz_live_room_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `activity_title` varchar(255) DEFAULT '' COMMENT '',
  `activity_img` varchar(255) DEFAULT '' COMMENT '',
  `default_avatar` varchar(255) DEFAULT '' COMMENT '',
  `activity_desc` varchar(255) DEFAULT '' COMMENT '',
  `avatar` varchar(255) DEFAULT '' COMMENT '',
  `follow_qrcode` varchar(255) DEFAULT '' COMMENT '',
  `follow_url` varchar(255) DEFAULT '' COMMENT '',
  `follow_type` TINYINT(1) DEFAULT 1 COMMENT '',
  `follow_cache_day` int(3) DEFAULT 1 COMMENT '',
  `share_img` varchar(255) NOT NULL COMMENT '',
  `share_title` varchar(255) NOT NULL COMMENT '',
  `share_desc` varchar(255) NOT NULL COMMENT '',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '';

CREATE TABLE IF NOT EXISTS `pre_wxz_live_room` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `room_no` varchar(255) DEFAULT '' COMMENT '',
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `title` varchar(255) DEFAULT '' COMMENT '',      
  `start_time` datetime NOT NULL COMMENT '',
  `end_time` datetime NOT NULL COMMENT '',
  `live_status` tinyint(1) DEFAULT 1 COMMENT '',
  `countdown_style` varchar(255) DEFAULT '' COMMENT '',      
  `style` tinyint(1) DEFAULT 1 COMMENT '',
  `limit` tinyint(1) DEFAULT 1 COMMENT '',
  `limit_data` varchar(255) DEFAULT '' COMMENT '',
  `style_extend` varchar(255) DEFAULT '' COMMENT '',        
  `station_caption` varchar(255) DEFAULT '' COMMENT '',        
  `theme_pic` varchar(255) DEFAULT '' COMMENT '',      
  `cover_pic` varchar(255) DEFAULT '' COMMENT '',        
  `is_show_follow_btn` tinyint(1) DEFAULT 1 COMMENT '', 
  `publisher` varchar(255) DEFAULT '' COMMENT '',    
  `publisher_avatar` varchar(255) DEFAULT '' COMMENT '',  
  `online_user_config` varchar(255) DEFAULT '' COMMENT '',  
  `check_comment` tinyint(1) DEFAULT 1 COMMENT '',
  `enable_redpacket` tinyint(1) DEFAULT 1 COMMENT '',
  `is_show_copyright` tinyint(1) DEFAULT 1 COMMENT '', 
  `copyright` varchar(255) DEFAULT '' COMMENT '',
  `rule` text COMMENT '直播规则',
  `linkurl` varchar(255) DEFAULT '' COMMENT '',
  `islinkurl` tinyint(1) DEFAULT '2' COMMENT '',
  `is_show` tinyint(1) DEFAULT '1' COMMENT '',
  `is_show_list` tinyint(1) DEFAULT 1 COMMENT '',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `base_num int(11) NOT NULL DEFAULT '0' COMMENT '',
  `total_num` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `view_num` int(11) NOT NULL DEFAULT '0' COMMENT '',    
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_no` (`room_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '';

        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `activity_id` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `name` varchar(255) DEFAULT '' COMMENT '',
  `icon` varchar(255) DEFAULT '' COMMENT '',
  `desc` varchar(1000) DEFAULT '' COMMENT '',
  `is_show` tinyint(1) DEFAULT '2' COMMENT '',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pre_wxz_live_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `sub_openid` varchar(255) DEFAULT NULL COMMENT '',
  `openid` varchar(255) DEFAULT NULL COMMENT '',
  `is_vip` tinyint(1) DEFAULT '1' COMMENT '',
  `vip_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '',
  `vip_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '',
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
  `rid` int(10) DEFAULT NULL,
  `share` tinyint(1) DEFAULT '0',
  `amount` int(10) DEFAULT '0',
  `ispay` tinyint(1) DEFAULT '0',
  `rlog` varchar(255) DEFAULT NULL,
  `deposit` int(10) DEFAULT '0',
  `password` varchar(255) DEFAULT NULL,
  `isshutup` tinyint(1) DEFAULT '0',
  `role` tinyint(1) DEFAULT '0' COMMENT '',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE IF NOT EXISTS `pre_wxz_live_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(10) DEFAULT '0',
  `sort_order` int(11) NOT NULL,
  `is_show` tinyint(1) NOT NULL,
  `type` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `settings` text NOT NULL,
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
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

CREATE TABLE IF NOT EXISTS `pre_wxz_live_polling` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0' COMMENT '',
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
  `type` VARCHAR(50) NOT NULL COMMENT '',
  `title` VARCHAR(250) NOT NULL COMMENT '',
  `desc` text COMMENT '',
  `img` VARCHAR(255) NOT NULL COMMENT '',
  `link` VARCHAR(255) NOT NULL COMMENT '',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;    

CREATE TABLE IF NOT EXISTS `pre_wxz_live_banner` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `img` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT NULL DEFAULT '1' COMMENT '',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL DEFAULT '0' COMMENT '',
  `rid` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `order_type` int(11) NOT NULL DEFAULT '1' COMMENT '',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '',
  `money` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `pay_money` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `fail_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '',
  `trade_no` varchar(50) NOT NULL COMMENT '',
  `ext` varchar(1000) DEFAULT '' COMMENT '',
  `success_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`),
  KEY `uid` (`uid`),
  KEY `order_no` (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pre_wxz_live_zanpic` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `pic` varchar(255) DEFAULT NULL,
    `rid` int(10) unsigned NOT NULL DEFAULT '0',
    `is_show` tinyint(1) DEFAULT NULL DEFAULT '1' COMMENT '',
    `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `rid` (`rid`) USING BTREE,
    KEY `is_show` (`is_show`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_zannum` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `rid` int(10) unsigned DEFAULT '0',
    `uid` int(10) unsigned DEFAULT '0',
    `num` int(10) unsigned DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `rid` (`rid`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;        

CREATE TABLE IF NOT EXISTS `pre_wxz_live_reward` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT '0',
  `rid` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '1' COMMENT '',
  `touid` int(10) DEFAULT '0',
  `tonickname` varchar(255) DEFAULT NULL,
  `toheadurl` varchar(255) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `order_id` (`order_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `pre_wxz_live_gift` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `amount` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT NULL,
  `sort_order` int(10) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `pre_wxz_live_grouppacket` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `rid` int(10) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `amount` int(10) DEFAULT NULL,
  `num` int(10) DEFAULT NULL,
  `json` text,
  `remark` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pre_wxz_live_giftlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `giftid` int(10) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT '0',
  `rid` int(10) DEFAULT '0',
  `num` int(10) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `order_id` (`order_id`),
  KEY `giftid` (`giftid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
        
CREATE TABLE IF NOT EXISTS `pre_wxz_live_money_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(10) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `amount` int(10) DEFAULT NULL,
  `type` tinyint(1) DEFAULT '0' COMMENT '',
  `fromuid` int(10) DEFAULT NULL,
  `fromnickname` varchar(255) DEFAULT NULL,
  `fromheadimgurl` varchar(255) DEFAULT NULL,
  `fromid` int(10) DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pre_wxz_live_grouppacket_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `hid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `comment_id` int(10) DEFAULT NULL,
  `amount` int(10) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;   
EOF;

runquery($sql);

$finish = TRUE;
?>