<?php
/*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
include_once DISCUZ_ROOT.'./source/plugin/zhanmishu_video/source/Autoloader.php';
include_once DISCUZ_ROOT.'./source/plugin/zhanmishu_video/source/function/common_function.php';

$input = daddslashes($_GET);
$input['act'] = $input['act'] ? $input['act'] : 'admin';
$url = 'plugins&operation=config&identifier=zhanmishu_video&pmod=input';
$video = new zhanmishu_video();

$perpage=20;
$curpage = ($input['page'] + 0) > 0 ? ($input['page'] + 0) : 1;
$pages= ceil($num / $perpage);
$start = $num - ($num - $perpage*$curpage+$perpage);


if ($_GET['act'] == 'adminvideo' && $_GET['cid']) {
	$addarray = array(lang('plugin/zhanmishu_video', 'add_video'),$url.'&act=adminvideo&m=add&cid='.$_GET['cid'],$status = $input['act'] =='add'?'1':'0');
}else{
	$addarray = array(lang('plugin/zhanmishu_video', 'add_course'),$url.'&act=add',$status = $input['act'] =='add'?'1':'0');
}


zms_showtitle(lang('plugin/zhanmishu_video', 'course_admin'),array(
	$addarray,
	array(lang('plugin/zhanmishu_video', 'course_admin'),$url.'&act=admin',$status = $input['act'] =='admin'?'1':'0'),
	array(lang('plugin/zhanmishu_video', 'order_admin'),$url.'&act=order',$status = $input['act'] =='order'?'1':'0'),
	array(lang('plugin/zhanmishu_video', 'setvipbyhand'),$url.'&act=setvipbyhand',$status = $input['act'] =='setvipbyhand'?'1':'0')
));
if ($input['act'] =='add') {

	if (submitcheck('course_addsubmit')) {
		$images = zms_uploadimg();

		$course = array();
		$course['course_img'] = $images['course_img'] ? $images['course_img'] : $input['course_img'];
		$course['site_sign_img1'] = $images['site_sign_img1'] ? $images['site_sign_img1'] : $input['site_sign_img1'];
		$course['site_sign_img2'] = $images['site_sign_img2'] ? $images['site_sign_img2'] : $input['site_sign_img2'];
		// $course['num'] = $input['num'] + 0;

		if ($input['course_teacher']) {
			if (is_numeric($course['course_teacher']) && $course['course_teacher']) {
				$course = $input['course_teacher'] + 0;
			}else{
				$course_teacher = $input['course_teacher'];
				loaducenter();
				if($data = uc_get_user($course_teacher)) {
					list($uid, $username, $email) = $data;
					$course['uid'] = $uid;
				} else {
					cpmsg(lang('plugin/zhanmishu_video', 'course_teacher_isnot_exists'),'','error');

				}
			}
			
		}else{
			$course['uid'] = $_G['uid'];
		}
		$course['course_name'] = $input['course_name'];
		$course['live_url'] = $input['live_url'];
		$course['course_type'] = $input['course_type'];
		$course['course_group'] = $input['course_group'];
	
		$course['course_group'] = '#'.implode('#', $input['course_group']).'#';		

		//trim($course['course_group'],'#')

		$course['course_weight'] = $input['course_weight'];
		$course['course_intro'] = $input['course_intro'];
		$course['course_price'] = strval(intval($input['course_price'] * 100));
		$course['dateline'] = TIMESTAMP;
		$course['diff'] = $input['diff'];
		$course['ProfileID'] = $input['ProfileID']; 
		$course['baiduurl'] = $input['baiduurl']; 
		$course['fileurl'] = $input['fileurl']; 
		$course['baiduurlpwd'] = $input['baiduurlpwd']; 
		// $course['360url'] = $input['360url']; 
		// $course['360urlpwd'] = $input['360urlpwd']; 
		$course['rarpwd'] = $input['rarpwd']; 
		// $course['yourproductid'] = $input['yourproductid'];
		$course['progress'] = $input['progress'];
		if (!$course['course_name'] || !$course['course_intro']) {
			cpmsg(lang('plugin/zhanmishu_video', 'must_finish_info'),'','error');
		}
		if ($input['cid']) {
			$course['cid'] = $input['cid'] + 0;
		}
		if ($input['cat_id']) {
			$course['cat_id'] = $input['cat_id'] + 0;
		}
		$isreplace = $course['cid'] ? true : false;
		$cid = C::t("#zhanmishu_video#zhanmishu_video_course")->insert($course,true,$isreplace);

		if ($isreplace) {
			C::t("#zhanmishu_video#zhanmishu_video_order")->update_ordertype_bycid($course['cid'],$course['course_type']);
		}

		cpmsg(lang('plugin/zhanmishu_video', 'add_course_success_and_add_video'),'action=plugins&operation=config&identifier=zhanmishu_video&pmod=input&act=admin','success');

		
	}else{

		$groupselect = array();
		$query = C::t('common_usergroup')->range();
		foreach($query as $group) {
			if (in_array($group['groupid'], array(4,5,6,7))) {
				continue;
			}
			$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
			if($group['type'] == 'member' && $group['creditshigher'] == 0) {
				$groupselect[$group['type']] .= "<option value=\"$group[groupid]\">$group[grouptitle]</option>\n";
			} else {
				$groupselect[$group['type']] .= "<option value=\"$group[groupid]\">$group[grouptitle]</option>\n";
			}
		}
		$groupselect = '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
			($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
			($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
			'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup>';


		showformheader($url.'&act=add','enctype="multipart/form-data"');
		showtableheader();
		showsetting(lang('plugin/zhanmishu_video', 'course_name'), 'course_name', '', 'text','','',lang('plugin/zhanmishu_video', 'course_name_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_weight'), 'course_weight', $course['course_weight'], 'text','','',lang('plugin/zhanmishu_video', 'course_weight_desc'),'size="10"');

		showsetting(lang('plugin/zhanmishu_video', 'course_price'), 'course_price', '', 'text','','',lang('plugin/zhanmishu_video', 'course_price_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_teacher'), 'course_teacher', '', 'text','','',lang('plugin/zhanmishu_video', 'course_teacher_desc'),'size="10"');
		// showsetting(lang('plugin/zhanmishu_video', 'live_url'), 'live_url', '', 'textarea','','',lang('plugin/zhanmishu_video', 'live_url_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_type'), array('course_type',array(array('0',$zhanmishu_videoconf['course_type']['0']),array('1',$zhanmishu_videoconf['course_type']['1']))), $v['course_type'], 'mradio','','',lang('plugin/zhanmishu_video', 'course_type_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_group'), 'course_group', '', '<select name="course_group[]"  multiple="multiple" size="10">'.$groupselect.'</select><td class="vtop tips2" s="1">'.lang('plugin/zhanmishu_video','course_group_desc').'</td>');

		$diff_sellect = array();
		foreach ($zhanmishu_videoconf['diff'] as $key => $value) {
			$diff_sellect[] = array($key,$value);
		}
		showsetting(lang('plugin/zhanmishu_video', 'diff'), array('diff',$diff_sellect), $v['diff'], 'mradio','','',lang('plugin/zhanmishu_video', 'isfree_desc'),'size="10"');
		foreach ($zhanmishu_videoconf['progress'] as $key => $value) {
			$progress_sellect[] = array($key,$value);
		}
		showsetting(lang('plugin/zhanmishu_video', 'progress'), array('progress',$progress_sellect), $v['progress'], 'mradio','','',lang('plugin/zhanmishu_video', 'isfree_desc'),'size="10"');
		$video_cat = $video->get_cat_select();
		showsetting(lang('plugin/zhanmishu_video', 'cat_id'), array('cat_id',$video_cat), $v['cat_id'], 'mradio','','',lang('plugin/zhanmishu_video', 'isfree_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_img'), 'course_img', '', 'filetext','','',lang('plugin/zhanmishu_video', 'course_img_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'site_sign_img1'), 'site_sign_img1', '', 'filetext','','',lang('plugin/zhanmishu_video', 'site_sign_img1_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'site_sign_img2'), 'site_sign_img2', '', 'filetext','','',lang('plugin/zhanmishu_video', 'site_sign_img2_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_intro'), 'course_intro', '', 'textarea','','',lang('plugin/zhanmishu_video', 'course_intro_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'ProfileID'), 'ProfileID', '', 'text','','',lang('plugin/zhanmishu_video', 'ProfileID_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'fileurl'), 'fileurl', '', 'text','','',lang('plugin/zhanmishu_video', 'fileurl_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'baiduurl'), 'baiduurl', '', 'text','','',lang('plugin/zhanmishu_video', 'baiduurl_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'baiduurlpwd'), 'baiduurlpwd', '', 'text','','',lang('plugin/zhanmishu_video', 'baiduurlpwd_desc'),'size="10"');
		// showsetting(lang('plugin/zhanmishu_video', '360url'), '360url', '', 'text','','',lang('plugin/zhanmishu_video', '360url_desc'),'size="10"');
		// showsetting(lang('plugin/zhanmishu_video', '360urlpwd'), '360urlpwd', '', 'text','','',lang('plugin/zhanmishu_video', '360urlpwd_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'rarpwd'), 'rarpwd', '', 'text','','',lang('plugin/zhanmishu_video', 'rarpwd_desc'),'size="10"');
		// showsetting(lang('plugin/zhanmishu_video', 'yourproductid'), 'yourproductid', '', 'text','','',lang('plugin/zhanmishu_video', 'yourproductid_desc'),'size="10"');
		showsubmit('course_addsubmit');
		showtablefooter();
		showformfooter();
	}	
}else if ($input['act'] =='editk' && $input['cid']) {
		$course = $video->get_course_bycid($input['cid']);
		$user = getuserbyuid($course['uid']);
		$course['course_teacher'] = $user['username'];

		$course['course_group'] = array_filter(explode('#', trim($course['course_group'],'#')));
 
	$groupselect = array();
	$query = C::t('common_usergroup')->range();
	foreach($query as $group) {
		if (in_array($group['groupid'], array(4,5,6,7))) {
			continue;
		}
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		if(in_array($group['groupid'], $course['course_group'])) {
			$groupselect[$group['type']] .= "<option value=\"$group[groupid]\" selected>$group[grouptitle]</option>\n";
		} else {
			$groupselect[$group['type']] .= "<option value=\"$group[groupid]\">$group[grouptitle]</option>\n";
		}
	}
	$groupselect = '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
		($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
		($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
		'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup>';


		showformheader($url.'&act=add','enctype="multipart/form-data"');
		showtableheader();
		showsetting(lang('plugin/zhanmishu_video', 'cid'), 'cid', $course['cid'], 'text','','',lang('plugin/zhanmishu_video', ''),'size="10" readonly="readonly"');
		showsetting(lang('plugin/zhanmishu_video', 'course_name'), 'course_name', $course['course_name'], 'text','','',lang('plugin/zhanmishu_video', 'course_name_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_weight'), 'course_weight', $course['course_weight'], 'text','','',lang('plugin/zhanmishu_video', 'course_weight_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_price'), 'course_price', $course['course_price'] / 100, 'text','','',lang('plugin/zhanmishu_video', 'course_price_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_teacher'), 'course_teacher', $course['course_teacher'], 'text','','',lang('plugin/zhanmishu_video', 'course_teacher_desc'),'size="10"');
		// showsetting(lang('plugin/zhanmishu_video', 'live_url'), 'live_url', stripcslashes($course['live_url']), 'textarea','','',lang('plugin/zhanmishu_video', 'live_url_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_type'), array('course_type',array(array('0',$zhanmishu_videoconf['course_type']['0']),array('1',$zhanmishu_videoconf['course_type']['1']))), $course['course_type'], 'mradio','','',lang('plugin/zhanmishu_video', 'course_type_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_group'), 'course_group', '', '<select name="course_group[]"  multiple="multiple" size="10">'.$groupselect.'</select><td class="vtop tips2" s="1">'.lang('plugin/zhanmishu_video','course_group_desc').'</td>');

		$diff_sellect = array();
		foreach ($zhanmishu_videoconf['diff'] as $key => $value) {
			$diff_sellect[] = array($key,$value);
		}
		showsetting(lang('plugin/zhanmishu_video', 'diff'), array('diff',$diff_sellect), $course['diff'] ? $course['diff'] : '0', 'mradio','','',lang('plugin/zhanmishu_video', 'isfree_desc'),'size="10"');
		foreach ($zhanmishu_videoconf['progress'] as $key => $value) {
			$progress_sellect[] = array($key,$value);
		}
		showsetting(lang('plugin/zhanmishu_video', 'progress'), array('progress',$progress_sellect), $course['progress']? $course['progress'] : '0', 'mradio','','',lang('plugin/zhanmishu_video', 'isfree_desc'),'size="10"');
		$video_cat = $video->get_cat_select();
		showsetting(lang('plugin/zhanmishu_video', 'cat_id'), array('cat_id',$video_cat), $course['cat_id'], 'mradio','','',lang('plugin/zhanmishu_video', 'isfree_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_img'), 'course_img', $course['course_img'], 'filetext','','',lang('plugin/zhanmishu_video', 'course_img_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'site_sign_img1'), 'site_sign_img1', $course['site_sign_img1'], 'filetext','','',lang('plugin/zhanmishu_video', 'course_img_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'site_sign_img2'), 'site_sign_img2', $course['site_sign_img2'], 'filetext','','',lang('plugin/zhanmishu_video', 'course_img_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'course_intro'), 'course_intro', $course['course_intro'], 'textarea','','',lang('plugin/zhanmishu_video', 'course_intro_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'ProfileID'), 'ProfileID', $course['ProfileID'], 'text','','',lang('plugin/zhanmishu_video', 'ProfileID_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'fileurl'), 'fileurl', $course['fileurl'], 'text','','',lang('plugin/zhanmishu_video', 'fileurl_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'baiduurl'), 'baiduurl', $course['baiduurl'], 'text','','',lang('plugin/zhanmishu_video', 'baiduurl_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'baiduurlpwd'), 'baiduurlpwd', $course['baiduurlpwd'], 'text','','',lang('plugin/zhanmishu_video', 'baiduurlpwd_desc'),'size="10"');
		// showsetting(lang('plugin/zhanmishu_video', '360url'), '360url', $course['360url'], 'text','','',lang('plugin/zhanmishu_video', '360url_desc'),'size="10"');
		// showsetting(lang('plugin/zhanmishu_video', '360urlpwd'), '360urlpwd', $course['360urlpwd'], 'text','','',lang('plugin/zhanmishu_video', '360urlpwd_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'rarpwd'), 'rarpwd', $course['rarpwd'], 'text','','',lang('plugin/zhanmishu_video', 'rarpwd_desc'),'size="10"');
		// showsetting(lang('plugin/zhanmishu_video', 'yourproductid'), 'yourproductid', $course['yourproductid'], 'text','','',lang('plugin/zhanmishu_video', 'yourproductid_desc'),'size="10"');
		showsubmit('course_addsubmit');
		showtablefooter();
		showformfooter();


}else if ($input['act'] =='delk' &&  FORMHASH == $input['formhash'] && $input['cid']) {

	//is course_exists
	$video->delete_k($input['cid']);
	cpmsg(lang('plugin/zhanmishu_video', 'delte_success'),'action=plugins&operation=config&identifier=zhanmishu_video&pmod=input&act=admin','success');
}else if ($input['act'] =='outsellk' &&  FORMHASH == $input['formhash'] && $input['cid']) {

	//is course_exists
	$rs = $video->set_course_upatesale($input['cid']);

	cpmsg(lang('plugin/zhanmishu_video', 'update_sell_success'),'action=plugins&operation=config&identifier=zhanmishu_video&pmod=input&act=admin','success');
}else if ($input['act'] =='admin') {

	$mpurl=ADMINSCRIPT.'?action='.$url;
	$courses = $video->get_type_course_fmt($start,$perpage,'desc','',array('isdel'=>'0'));
	$num = $video->get_type_course_num(array('isdel'=>'0'));

	showtableheader();
		showsubtitle(array(lang('plugin/zhanmishu_video', 'cid'),lang('plugin/zhanmishu_video', 'sellner'),lang('plugin/zhanmishu_video', 'title'),lang('plugin/zhanmishu_video', 'issell'),lang('plugin/zhanmishu_video', 'sellnum'),lang('plugin/zhanmishu_video', 'price'),lang('plugin/zhanmishu_video', 'diff'),lang('plugin/zhanmishu_video', 'progress'),lang('plugin/zhanmishu_video', 'img'),lang('plugin/zhanmishu_video', 'desc'),lang('plugin/zhanmishu_video', 'datetime'),lang('plugin/zhanmishu_video', 'act')));
		foreach ($courses as $key => $value) {
			showtablerow('class="partition"',array('class="td15"', 'class="td28"'),$value);
		}
	showtablefooter();
	$multi = multi($num, $perpage, $curpage, $mpurl, '0', '10');
	echo $multi;
}else if ($input['act'] =='adminvideo' && (($input['m'] =='add'  && $input['cid']) || ($input['m'] =='edit' && $input['vid']))) {
	if (submitcheck('video_addsubmit')) {
		$images = zms_uploadimg();

		$video_data = array();
		$video_data['video_img'] = $images['video_img'] ? $images['video_img'] : $input['video_img'];
		// $course['num'] = $input['num'] + 0;
		$video_data['uid'] = $_G['uid'];

		$video_data['video_name'] = $input['video_name'];
		$video_data['video_intro'] = $input['video_intro'];
		$video_data['video_url'] = stripslashes($input['video_url']);
		$video_data['video_urltype'] = $input['video_urltype'];
		$video_data['video_length'] = $input['video_length'];
		$video_data['isfree'] = $input['isfree'];
		$video_data['video_price'] = strval(intval($input['video_price'] * 100));
		$video_data['dateline'] = TIMESTAMP;
		$video_data['cid'] = $input['cid'] + 0;

		if (!$video_data['video_name']) {
			cpmsg(lang('plugin/zhanmishu_video', 'must_finish_videoname'),'','error');
		}
		if ($input['vid']) {
			$video_data['vid'] = $input['vid'] + 0;
		}
		$isreplace = $video_data['vid'] ? true : false;

		C::t("#zhanmishu_video#zhanmishu_video")->insert($video_data,false,$isreplace);


		cpmsg(lang('plugin/zhanmishu_video', 'add_video_success'),'action=plugins&operation=config&identifier=zhanmishu_video&pmod=input&act=adminvideo&m=admin&cid='.$input['cid'],'success');

		
	}else{

		$v = $video->get_video_by_vid($input['vid']);
		$cid = $v['cid'] ? $v['cid'] : $input['cid'];
		showformheader($url.'&act=adminvideo&m=add&cid='.$cid,'enctype="multipart/form-data"');
		showtableheader();
		if ($v['vid']) {
			showsetting(lang('plugin/zhanmishu_video', 'vid'), 'vid', $v['vid'], 'text','','',lang('plugin/zhanmishu_video', ''),'size="10" readonly="readonly"');
		}

		showsetting(lang('plugin/zhanmishu_video', 'video_name'), 'video_name', $v['video_name'], 'text','','',lang('plugin/zhanmishu_video', ''),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'video_url'), 'video_url', stripslashes($v['video_url']), 'textarea','','',lang('plugin/zhanmishu_video', 'video_url_desc'),'size="10"');

		$urltype_sellect = array();
		foreach ($zhanmishu_videoconf['video_url_type'] as $key => $value) {
			$urltype_sellect[] = array($key,$value);
		}
		showsetting(lang('plugin/zhanmishu_video', 'video_urltype'), array('video_urltype',$urltype_sellect), $v['video_urltype']? $v['video_urltype'] : '0', 'mradio','','',lang('plugin/zhanmishu_video', 'isfree_desc'),'size="10"');

		//showsetting(lang('plugin/zhanmishu_video', 'video_urltype'), array('video_urltype',array(array('0',$urltype_sellect['video_urltype']['0']),array('1',$urltype_sellect['video_urltype']['1']))), $v['video_urltype'], 'mradio','','',lang('plugin/zhanmishu_video', 'course_urltype_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'isfree'), array('isfree',array(array('0',$zhanmishu_videoconf['isfree']['0']),array('1',$zhanmishu_videoconf['isfree']['1']))), $v['isfree'], 'mradio','','',lang('plugin/zhanmishu_video', 'isfree_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'video_price'), 'video_price', $v['video_price'] / 100, 'text','','',lang('plugin/zhanmishu_video', 'course_price_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'video_length'), 'video_length', $v['video_length'], 'text','','',lang('plugin/zhanmishu_video', 'video_length_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'video_img'), 'video_img', $v['video_img'], 'filetext','','',lang('plugin/zhanmishu_video', 'course_img_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'video_intro'), 'video_intro', $v['video_intro'], 'textarea','','',lang('plugin/zhanmishu_video', 'course_intro_desc'),'size="10"');
		showsubmit('video_addsubmit');
		showtablefooter();
		showformfooter();	}

}else if ($input['act'] =='adminvideo' && $input['m'] =='admin' && $input['cid']) {

	$mpurl=ADMINSCRIPT.'?action='.$url.'&act=adminvideo&m=admin&cid='.$input['cid'];
	$num = $video->get_type_video_num(array('cid'=>$input['cid']));
	$videoes = $video->get_type_video_fmt($start,$perpage,'desc','',array('cid'=>$input['cid'],'isdel'=>'0'));
	showtableheader();
		showsubtitle(array(lang('plugin/zhanmishu_video', 'vid'),lang('plugin/zhanmishu_video', 'video_name'),lang('plugin/zhanmishu_video', 'video_price'),lang('plugin/zhanmishu_video', 'isfree'),lang('plugin/zhanmishu_video', 'video_url'),lang('plugin/zhanmishu_video', 'video_urltype'),lang('plugin/zhanmishu_video', 'video_length'),lang('plugin/zhanmishu_video', 'selltimes'),lang('plugin/zhanmishu_video', 'video_img'),lang('plugin/zhanmishu_video', 'dateline'),lang('plugin/zhanmishu_video', 'act')));
		foreach ($videoes as $key => $value) {
			$value['video_url'] = '<a href="'.$value['video_url'].'" target="_blank">'.lang('plugin/zhanmishu_video','click_check').'</a>';
			showtablerow('',array('class="td15"', 'class="td32"', 'class="td28"', 'class="td28"', 'class="td31"'),array_values($value));
		}
	showtablefooter();
	$multi = multi($num, $perpage, $curpage, $mpurl, '0', '10');
	echo $multi;
}else if ($input['act'] =='adminvideo' && $input['m'] =='delete' &&  FORMHASH == $input['formhash'] && $input['vid']) {
	
	$video->delete_video($input['vid']);
	cpmsg(lang('plugin/zhanmishu_video', 'delete_video_success'),dreferer(),'success');
}else if ($input['act'] =='order' &&  $input['m'] =='setpay' &&  FORMHASH == $input['formhash'] && $input['oid']) {
	$r = $video->order_setpay($input['oid'],$input['paystatus']+0);
	$video->update_order_status_byoid($input['oid']);
	if ($r['code'] > 0) {
		cpmsg(lang('plugin/zhanmishu_video', $r['msg']),dreferer(),'success');
	}
	cpmsg(lang('plugin/zhanmishu_video', $r['msg']),dreferer(),'error');

}else if ($input['act'] =='order' &&  $input['m'] =='setcontract' &&  FORMHASH == $input['formhash'] && $input['oid']) {
	$r = $video->order_setcontract($input['oid'],$input['contractstatus']+0);
	if ($r['code'] > 0) {
		cpmsg(lang('plugin/zhanmishu_video', $r['msg']),dreferer(),'success');
	}
	cpmsg(lang('plugin/zhanmishu_video', $r['msg']),dreferer(),'error');

}else if ($input['act'] =='order' &&  $input['m'] =='setmail' &&  FORMHASH == $input['formhash'] && $input['oid']) {
	$r = $video->order_setmail($input['oid'],$input['mailstatus']+0);
	if ($r['code'] > 0) {
		cpmsg(lang('plugin/zhanmishu_video', $r['msg']),dreferer(),'success');
	}
	cpmsg(lang('plugin/zhanmishu_video', $r['msg']),dreferer(),'error');

}else if ($input['act'] =='order' &&  $input['m'] =='cleanplaycount' &&  FORMHASH == $input['formhash'] && $input['oid']) {
	$r = $video->cleanplaycount($input['oid']);
	if ($r['code'] > 0) {
		cpmsg(lang('plugin/zhanmishu_video', $r['msg']),dreferer(),'success');
	}
	cpmsg(lang('plugin/zhanmishu_video', $r['msg']),dreferer(),'error');

}else if ($input['act'] =='order' &&  $input['m'] =='checkorder' &&  FORMHASH == $input['formhash'] && $input['oid']) {
	$o = $video->get_order_byoid_fmt($input['oid'] + 0);
	if (empty($o)) {
		cpmsg(lang('plugin/zhanmishu_video', 'order_isnot_exists'),dreferer(),'error');
	}
	include template('zhanmishu_video:admin/order_info');

}else if ($input['act'] =='order') {
	$mpurl=ADMINSCRIPT.'?action='.$url.'&act=order';
	$num = $video->get_orders_num();
	$orders = $video->get_orders_fmt($start,$perpage,'desc');

	showtableheader(); 
		showsubtitle(array(lang('plugin/zhanmishu_video', 'oid'),lang('plugin/zhanmishu_video', 'cid'),lang('plugin/zhanmishu_video', 'cname'),lang('plugin/zhanmishu_video', 'vid'),lang('plugin/zhanmishu_video', 'ispayed'),lang('plugin/zhanmishu_video', 'course_price'),lang('plugin/zhanmishu_video', 'buyer_uid'),lang('plugin/zhanmishu_video', 'out_trade_no'),lang('plugin/zhanmishu_video', 'orderdateline'),lang('plugin/zhanmishu_video', 'pay_time'),lang('plugin/zhanmishu_video', 'orderstatus'),lang('plugin/zhanmishu_video', 'act')));
		foreach ($orders as $key => $value) {
			showtablerow('class="partition"',array('class="td15"', 'class="td28"'),$value);
		}
	showtablefooter();

	$multi = multi($num, $perpage, $curpage, $mpurl, '0', '10');
	echo $multi;
}else if ($input['act'] =='setvipbyhand') {
	if ($_GET['setvipbyhand_addsubmit'] &&  FORMHASH == $input['formhash']) {
	$input = daddslashes($_GET);
	$cid = $input['cid'];
	$video = new zhanmishu_video();
	$course = $video->get_course_bycid($cid,false,true);
	
	if (empty($course)) {
		cpmsg(lang('plugin/zhanmishu_video', 'course_isnot_exists'),dreferer(),'error');

	}

	$out_trade_no = $video->get_rand_trade_no();
	$o = array();
	$o['uid'] = $course['uid'];
	$o['course_name'] = $course['course_name'];
	$o['total_fee'] = $course['course_price'];
	$o['course_intro'] = $course['course_intro'];
	$o['course_img'] = $course['course_img'];
	$o['course_price'] = $course['course_price'];
	$o['order_type'] = $course['course_type'];
	$o['cid'] = $course['cid'];
	$o['buyer_uid'] = $_G['uid'];
	$o['out_trade_no'] = $out_trade_no;
	$o['dateline'] = TIMESTAMP;

		exit;
	}

	$mpurl=ADMINSCRIPT.'?action='.$url.'&act=setvipbyhand';

		showformheader($url.'&act=setvipbyhand','enctype="multipart/form-data"');
		showtableheader();
		showsetting(lang('plugin/zhanmishu_video', 'username'), 'username', '', 'text','','',lang('plugin/zhanmishu_video', 'username_desc'),'size="10"');
		showsetting(lang('plugin/zhanmishu_video', 'cid'), 'cid', '', 'text','','',lang('plugin/zhanmishu_video', 'cid_desc'),'size="10"');
		showsubmit('setvipbyhand_addsubmit');
		showtablefooter();
		showformfooter();

}


?>