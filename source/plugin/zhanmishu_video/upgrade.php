<?php
/*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$field = C::t('#zhanmishu_video#zhanmishu_video_cat')->fetch_all_field();
$table = DB::table('zhanmishu_video_cat');
$sql = '';
if (!$field['cat_icon']) {
  $sql .= "ALTER TABLE $table ADD  `cat_icon` varchar(1000) NOT NULL DEFAULT '';\n";
  // $sql .= "ALTER TABLE $table ADD INDEX orderId (`orderId`);\n";
}
if (!$field['cat_touchorder']) {
  $sql .= "ALTER TABLE $table ADD  `cat_touchorder`  smallint(3) unsigned NOT NULL DEFAULT '0';\n";
  $sql .= "ALTER TABLE $table ADD INDEX cat_touchorder (`cat_touchorder`);\n";
}

$fieldcourse = C::t('#zhanmishu_video#zhanmishu_video_course')->fetch_all_field();
$tablecourse = DB::table('zhanmishu_video_course');
$sql = '';
if (!$fieldcourse['course_group']) {
  $sql .= "ALTER TABLE $tablecourse ADD  `course_group` varchar(255) NOT NULL DEFAULT '';\n";
  $sql .= "ALTER TABLE $tablecourse ADD INDEX course_group (`course_group`);\n";
}
if (!$fieldcourse['fileurl']) {
  $sql .= "ALTER TABLE $tablecourse ADD  `fileurl` varchar(2000) NOT NULL DEFAULT '';\n";
}
if (!$fieldcourse['course_weight']) {
  $sql .= "ALTER TABLE $tablecourse ADD  `course_weight` mediumint(8) unsigned NOT NULL DEFAULT '0';\n";
}


$fieldorder = C::t('#zhanmishu_video#zhanmishu_video_order')->fetch_all_field();
$tableorder = DB::table('zhanmishu_video_order');
if (!$fieldorder['device']) {
  $sql .= "ALTER TABLE $tableorder ADD  `device`  varchar(1000) not null default '';\n";
}
if (!$fieldorder['pay_type']) {
  $sql .= "ALTER TABLE $tableorder ADD  `pay_type` tinyint(1) unsigned NOT NULL DEFAULT '0';\n";
}

$fieldvideo = C::t('#zhanmishu_video#zhanmishu_video')->fetch_all_field();
$tablevideo = DB::table('zhanmishu_video');
if (!$fieldvideo['islive']) {
  $sql .= "ALTER TABLE $tablevideo ADD  `islive` tinyint(1) NOT NULL DEFAULT '0';\n";
}

$sql .= "alter table $tablevideo modify `video_url` varchar(1000) not null default '';\n";

$tableautho = DB::table('zhanmishu_video_autho');
$countautho = DB::fetch_first('show tables like \''.$tableautho.'\'');
if (empty($countautho)) {

  $sql .= "CREATE TABLE IF NOT EXISTS pre_zhanmishu_video_autho (
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
);\n";

}


if ($sql) {
	runquery($sql);
}


$finish = true;