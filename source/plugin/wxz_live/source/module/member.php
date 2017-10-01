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


$wxz_api = new wxz_api();

$member = $wxz_api->getuserbyuid();

if ($_GET['dtype']) {
	$outapi = array(
		'msg'=>'success',
		'code'=>'0',
		'data'=>array(),
	);
	$outapi['data']['member'] = $member;

	$outapi = zms_diconv($outapi,CHARSET,'utf-8');
	echo json_encode($outapi);
	exit;
}

?>