<?php
/*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS pre_wxz_live_course (
  `cid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cat_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `diff` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ProfileID` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `yourproductid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `progress` tinyint(1) NOT NULL DEFAULT '1',
  `issell` tinyint(1) NOT NULL DEFAULT '1',
  `isdel` tinyint(1) NOT NULL DEFAULT '0',
  `course_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `selltimes` smallint(3) NOT NULL DEFAULT '0',
  `course_weight` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `views` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `learns` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `course_name` varchar(156) NOT NULL DEFAULT '',
  `course_img` varchar(100) NOT NULL DEFAULT '',
  `course_intro` varchar(255) NOT NULL DEFAULT '',
  `course_group` varchar(255) NOT NULL DEFAULT '',
  `live_url` varchar(1000) NOT NULL DEFAULT '',
  `course_price` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `course_length` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `site_sign_img1` varchar(100) NOT NULL DEFAULT '',
  `site_sign_img2` varchar(100) NOT NULL DEFAULT '',
  `fileurl` varchar(1000) NOT NULL DEFAULT '',
  `baiduurl` varchar(1000) NOT NULL DEFAULT '',
  `baiduurlpwd` varchar(20) NOT NULL DEFAULT '',
  `360url` varchar(100) NOT NULL DEFAULT '',
  `360urlpwd` varchar(100) NOT NULL DEFAULT '',
  `rarpwd` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (cid),
  KEY dateline (dateline),
  KEY uid (uid),
  KEY views (views),
  KEY selltimes (selltimes),
  KEY cat_id (cat_id),
  KEY learns (learns),
  KEY course_name (course_name),
  KEY course_group (course_group),
  KEY ProfileID (ProfileID),
  KEY course_price (course_price)
);
CREATE TABLE IF NOT EXISTS pre_wxz_live (
  `vid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cid` mediumint(8) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cat_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `video_length` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `isfree` tinyint(1) NOT NULL DEFAULT '1',
  `isdel` tinyint(1) NOT NULL DEFAULT '0',
  `islive` tinyint(1) NOT NULL DEFAULT '0',
  `selltimes` smallint(3) NOT NULL DEFAULT '0',
  `video_name` varchar(156) NOT NULL DEFAULT '',
  `video_img` varchar(100) NOT NULL DEFAULT '',
  `video_url`  varchar(1000) NOT NULL DEFAULT '',
  `video_urltype` tinyint(1) NOT NULL DEFAULT '0',
  `video_intro` varchar(255) NOT NULL DEFAULT '',
  `video_price` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (vid),
  KEY dateline (dateline),
  KEY uid (uid),
  KEY cid (cid),
  KEY video_name (video_name),
  KEY video_price (video_price)
);

CREATE TABLE IF NOT EXISTS pre_wxz_live_order (
  `oid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `vid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ispayed` tinyint(1) NOT NULL DEFAULT '0',
  `isselled` tinyint(1) NOT NULL DEFAULT '0',
  `order_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checknum` smallint(3) NOT NULL DEFAULT '0',
  `course_name` varchar(156) NOT NULL DEFAULT '',
  `course_img` varchar(100) NOT NULL DEFAULT '',
  `course_intro` varchar(255) NOT NULL DEFAULT '',
  `course_price` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `total_fee` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `video_name` varchar(156) NOT NULL DEFAULT '',
  `video_img` varchar(100) NOT NULL DEFAULT '',
  `video_intro` varchar(255) NOT NULL DEFAULT '',
  `video_price` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `buyer_email` varchar(20) NOT NULL DEFAULT '',
  `trade_no` varchar(40) NOT NULL DEFAULT '',
  `out_trade_no` varchar(40) NOT NULL DEFAULT '',
  `buyer_mobile` varchar(12) NOT NULL DEFAULT '',
  `buyer_uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `playcount` smallint(3) unsigned NOT NULL DEFAULT '0',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0',
  `sign_time` int(10) unsigned NOT NULL DEFAULT '0',
  `confirm_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `issign` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isconfirm` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ismail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `issuccess` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isclosed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sign_img1` varchar(100) NOT NULL DEFAULT '',
  `sign_img2` varchar(100) NOT NULL DEFAULT '',
  `sign_img3` varchar(100) NOT NULL DEFAULT '',
  `device` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (oid),
  KEY cid (cid),
  KEY uid (uid),
  KEY vid (vid),
  KEY buyer_uid (buyer_uid),
  KEY total_fee (total_fee),
  KEY out_trade_no (out_trade_no),
  KEY status (status),
  KEY ismail (ismail),
  KEY trade_no (trade_no)
);
CREATE TABLE IF NOT EXISTS pre_wxz_live_cat (
  `cat_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sort` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(1) NOT NULL DEFAULT '0',
  `cat_icon` varchar(1000) NOT NULL DEFAULT '',
  `cat_touchorder`  smallint(3) unsigned NOT NULL DEFAULT '0',
  `isdel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cat_name` varchar(156) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cat_id),
  KEY dateline (dateline),
  KEY cat_touchorder (cat_touchorder),
  KEY sortselect (sort),
  KEY cat_name (cat_name)

);
CREATE TABLE IF NOT EXISTS pre_wxz_live_autho (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `oid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `vid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `device` varchar(80) NOT NULL DEFAULT '',
  `isautho` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `nums` varchar(156) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `ip1` smallint(3) NOT NULL default '0',
  `ip2` smallint(3) NOT NULL default '0',
  `ip3` smallint(3) NOT NULL default '0',
  `ip4` smallint(3) NOT NULL default '0',
  PRIMARY KEY (aid),
  KEY dateline (dateline),
  KEY cid (cid),
  KEY device (device)
);

EOF;

runquery($sql);

$finish = TRUE;
?>