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
$input = daddslashes($_GET);

if (!$_GET['vid'] && !$_GET['type']) {
	$_GET['type'] = 'index';
}

$video = new wxz_live();
$videoconfig = $video->config;

if ($input['sign'] || $input['ispay']) {
	$out_trade_no = $input['out_trade_no'];
	$video->check_ispay_byout_trade_no($out_trade_no);
}
$video->issueorderbyuid(); 
$cid = $input['cid'];

if (!$cid) {
	showmessage(lang('plugin/wxz_live', 'data_error'));
}

$course = $video->get_course_bycid($cid,true,true);
$user = getuserbyuid($course['uid']);
$course['username'] = $user['username'];
$course['video'] = $video->get_video_bycid($cid);

$course['coursetype'] = $video->check_liveorvideo($course['live_url']);
$course['recommend'] = $video->get_recommend_course($cid);

$course['ispay'] = $video->checkuser_ispay_course($cid,$_G['uid']);
$course['cat'] = $video->get_cat_by_cat_id($course['cat_id']);

$extgroups = explode("\t", $_G['member']['extgroupids']);
$course_group = array_filter(explode('#', trim($course['course_group'])));
$extgroups[] = $_G['groupid'];
$vgroup = array_intersect($extgroups, array_keys($videoconfig['vipgroup']));

$course_groupin = array_intersect($extgroups, $course_group);

if (!empty($course_groupin)) {
	$course['ispay'] = '1';
}else{
	foreach ($vgroup as $value) {
		if (($course['course_price'] / 100) <= $videoconfig['vipgroup'][$value]) {
			$course['ispay'] = '1';
			break;
		}
	}

}

$groupicons = $video->get_group_icons();

if (!empty($course['video'])) {
	$showvideo = current($course['video']);
	foreach ($course['video'] as $key => $value) {
		$course['video'][$key]['video_img'] = $value['video_img'] ?  $value['video_img'] : $course['course_img'];

		if ($value['isdel'] == '1') {
			unset($course['video'][$key]);
		}
	}
	
	$vid = $input['vid'] ? $input['vid'] :  $showvideo['vid'];

	$course['video'][$vid]['video_url'] = $video->get_private_videourl($vid);
	$video->update_video_length($vid);
}

if ($_GET['dtype'] && ($course['ispay'] == '1' || $course['course_price'] == '0')) {
	$outapi = array(
		'msg'=>'success',
		'code'=>'0',
		'data'=>array(),
	);
	$outapi['data']['course'] = $course;
	$outapi['data']['groupicons'] = $groupicons;

	$outapi = zms_diconv($outapi,CHARSET,'utf-8');
	echo json_encode($outapi);
	exit;
}

 include template('wxz_live:'.$mod);
?>