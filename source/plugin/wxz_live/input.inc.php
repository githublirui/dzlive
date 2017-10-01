<?php
/*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
include_once DISCUZ_ROOT.'./source/plugin/wxz_live/source/Autoloader.php';
include_once DISCUZ_ROOT.'./source/plugin/wxz_live/source/function/common_function.php';

$input = daddslashes($_GET);
$input['act'] = $input['act'] ? $input['act'] : 'admin';
$url = 'plugins&operation=config&identifier=wxz_live&pmod=input';
$video = new wxz_live();

$perpage=20;
$curpage = ($input['page'] + 0) > 0 ? ($input['page'] + 0) : 1;
$pages= ceil($num / $perpage);
$start = $num - ($num - $perpage*$curpage+$perpage);


if ($_GET['act'] == 'adminvideo' && $_GET['cid']) {
	$addarray = array(lang('plugin/wxz_live', 'add_video'),$url.'&act=adminvideo&m=add&cid='.$_GET['cid'],$status = $input['act'] =='add'?'1':'0');
}else{
	$addarray = array(lang('plugin/wxz_live', 'add_course'),$url.'&act=add',$status = $input['act'] =='add'?'1':'0');
}


zms_showtitle(lang('plugin/wxz_live', 'course_admin'),array(
	$addarray,
	array(lang('plugin/wxz_live', 'course_admin'),$url.'&act=admin',$status = $input['act'] =='admin'?'1':'0'),
	array(lang('plugin/wxz_live', 'order_admin'),$url.'&act=order',$status = $input['act'] =='order'?'1':'0'),
	array(lang('plugin/wxz_live', 'setvipbyhand'),$url.'&act=setvipbyhand',$status = $input['act'] =='setvipbyhand'?'1':'0')
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
					cpmsg(lang('plugin/wxz_live', 'course_teacher_isnot_exists'),'','error');

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
			cpmsg(lang('plugin/wxz_live', 'must_finish_info'),'','error');
		}
		if ($input['cid']) {
			$course['cid'] = $input['cid'] + 0;
		}
		if ($input['cat_id']) {
			$course['cat_id'] = $input['cat_id'] + 0;
		}
		$isreplace = $course['cid'] ? true : false;
		$cid = C::t("#wxz_live#wxz_live_course")->insert($course,true,$isreplace);

		if ($isreplace) {
			C::t("#wxz_live#wxz_live_order")->update_ordertype_bycid($course['cid'],$course['course_type']);
		}

		cpmsg(lang('plugin/wxz_live', 'add_course_success_and_add_video'),'action=plugins&operation=config&identifier=wxz_live&pmod=input&act=admin','success');

		
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
		showsetting(lang('plugin/wxz_live', 'course_name'), 'course_name', '', 'text','','',lang('plugin/wxz_live', 'course_name_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_weight'), 'course_weight', $course['course_weight'], 'text','','',lang('plugin/wxz_live', 'course_weight_desc'),'size="10"');

		showsetting(lang('plugin/wxz_live', 'course_price'), 'course_price', '', 'text','','',lang('plugin/wxz_live', 'course_price_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_teacher'), 'course_teacher', '', 'text','','',lang('plugin/wxz_live', 'course_teacher_desc'),'size="10"');
		// showsetting(lang('plugin/wxz_live', 'live_url'), 'live_url', '', 'textarea','','',lang('plugin/wxz_live', 'live_url_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_type'), array('course_type',array(array('0',$wxz_liveconf['course_type']['0']),array('1',$wxz_liveconf['course_type']['1']))), $v['course_type'], 'mradio','','',lang('plugin/wxz_live', 'course_type_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_group'), 'course_group', '', '<select name="course_group[]"  multiple="multiple" size="10">'.$groupselect.'</select><td class="vtop tips2" s="1">'.lang('plugin/wxz_live','course_group_desc').'</td>');

		$diff_sellect = array();
		foreach ($wxz_liveconf['diff'] as $key => $value) {
			$diff_sellect[] = array($key,$value);
		}
		showsetting(lang('plugin/wxz_live', 'diff'), array('diff',$diff_sellect), $v['diff'], 'mradio','','',lang('plugin/wxz_live', 'isfree_desc'),'size="10"');
		foreach ($wxz_liveconf['progress'] as $key => $value) {
			$progress_sellect[] = array($key,$value);
		}
		showsetting(lang('plugin/wxz_live', 'progress'), array('progress',$progress_sellect), $v['progress'], 'mradio','','',lang('plugin/wxz_live', 'isfree_desc'),'size="10"');
		$video_cat = $video->get_cat_select();
		showsetting(lang('plugin/wxz_live', 'cat_id'), array('cat_id',$video_cat), $v['cat_id'], 'mradio','','',lang('plugin/wxz_live', 'isfree_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_img'), 'course_img', '', 'filetext','','',lang('plugin/wxz_live', 'course_img_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'site_sign_img1'), 'site_sign_img1', '', 'filetext','','',lang('plugin/wxz_live', 'site_sign_img1_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'site_sign_img2'), 'site_sign_img2', '', 'filetext','','',lang('plugin/wxz_live', 'site_sign_img2_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_intro'), 'course_intro', '', 'textarea','','',lang('plugin/wxz_live', 'course_intro_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'ProfileID'), 'ProfileID', '', 'text','','',lang('plugin/wxz_live', 'ProfileID_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'fileurl'), 'fileurl', '', 'text','','',lang('plugin/wxz_live', 'fileurl_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'baiduurl'), 'baiduurl', '', 'text','','',lang('plugin/wxz_live', 'baiduurl_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'baiduurlpwd'), 'baiduurlpwd', '', 'text','','',lang('plugin/wxz_live', 'baiduurlpwd_desc'),'size="10"');
		// showsetting(lang('plugin/wxz_live', '360url'), '360url', '', 'text','','',lang('plugin/wxz_live', '360url_desc'),'size="10"');
		// showsetting(lang('plugin/wxz_live', '360urlpwd'), '360urlpwd', '', 'text','','',lang('plugin/wxz_live', '360urlpwd_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'rarpwd'), 'rarpwd', '', 'text','','',lang('plugin/wxz_live', 'rarpwd_desc'),'size="10"');
		// showsetting(lang('plugin/wxz_live', 'yourproductid'), 'yourproductid', '', 'text','','',lang('plugin/wxz_live', 'yourproductid_desc'),'size="10"');
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
		showsetting(lang('plugin/wxz_live', 'cid'), 'cid', $course['cid'], 'text','','',lang('plugin/wxz_live', ''),'size="10" readonly="readonly"');
		showsetting(lang('plugin/wxz_live', 'course_name'), 'course_name', $course['course_name'], 'text','','',lang('plugin/wxz_live', 'course_name_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_weight'), 'course_weight', $course['course_weight'], 'text','','',lang('plugin/wxz_live', 'course_weight_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_price'), 'course_price', $course['course_price'] / 100, 'text','','',lang('plugin/wxz_live', 'course_price_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_teacher'), 'course_teacher', $course['course_teacher'], 'text','','',lang('plugin/wxz_live', 'course_teacher_desc'),'size="10"');
		// showsetting(lang('plugin/wxz_live', 'live_url'), 'live_url', stripcslashes($course['live_url']), 'textarea','','',lang('plugin/wxz_live', 'live_url_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_type'), array('course_type',array(array('0',$wxz_liveconf['course_type']['0']),array('1',$wxz_liveconf['course_type']['1']))), $course['course_type'], 'mradio','','',lang('plugin/wxz_live', 'course_type_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_group'), 'course_group', '', '<select name="course_group[]"  multiple="multiple" size="10">'.$groupselect.'</select><td class="vtop tips2" s="1">'.lang('plugin/wxz_live','course_group_desc').'</td>');

		$diff_sellect = array();
		foreach ($wxz_liveconf['diff'] as $key => $value) {
			$diff_sellect[] = array($key,$value);
		}
		showsetting(lang('plugin/wxz_live', 'diff'), array('diff',$diff_sellect), $course['diff'] ? $course['diff'] : '0', 'mradio','','',lang('plugin/wxz_live', 'isfree_desc'),'size="10"');
		foreach ($wxz_liveconf['progress'] as $key => $value) {
			$progress_sellect[] = array($key,$value);
		}
		showsetting(lang('plugin/wxz_live', 'progress'), array('progress',$progress_sellect), $course['progress']? $course['progress'] : '0', 'mradio','','',lang('plugin/wxz_live', 'isfree_desc'),'size="10"');
		$video_cat = $video->get_cat_select();
		showsetting(lang('plugin/wxz_live', 'cat_id'), array('cat_id',$video_cat), $course['cat_id'], 'mradio','','',lang('plugin/wxz_live', 'isfree_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_img'), 'course_img', $course['course_img'], 'filetext','','',lang('plugin/wxz_live', 'course_img_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'site_sign_img1'), 'site_sign_img1', $course['site_sign_img1'], 'filetext','','',lang('plugin/wxz_live', 'course_img_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'site_sign_img2'), 'site_sign_img2', $course['site_sign_img2'], 'filetext','','',lang('plugin/wxz_live', 'course_img_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'course_intro'), 'course_intro', $course['course_intro'], 'textarea','','',lang('plugin/wxz_live', 'course_intro_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'ProfileID'), 'ProfileID', $course['ProfileID'], 'text','','',lang('plugin/wxz_live', 'ProfileID_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'fileurl'), 'fileurl', $course['fileurl'], 'text','','',lang('plugin/wxz_live', 'fileurl_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'baiduurl'), 'baiduurl', $course['baiduurl'], 'text','','',lang('plugin/wxz_live', 'baiduurl_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'baiduurlpwd'), 'baiduurlpwd', $course['baiduurlpwd'], 'text','','',lang('plugin/wxz_live', 'baiduurlpwd_desc'),'size="10"');
		// showsetting(lang('plugin/wxz_live', '360url'), '360url', $course['360url'], 'text','','',lang('plugin/wxz_live', '360url_desc'),'size="10"');
		// showsetting(lang('plugin/wxz_live', '360urlpwd'), '360urlpwd', $course['360urlpwd'], 'text','','',lang('plugin/wxz_live', '360urlpwd_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'rarpwd'), 'rarpwd', $course['rarpwd'], 'text','','',lang('plugin/wxz_live', 'rarpwd_desc'),'size="10"');
		// showsetting(lang('plugin/wxz_live', 'yourproductid'), 'yourproductid', $course['yourproductid'], 'text','','',lang('plugin/wxz_live', 'yourproductid_desc'),'size="10"');
		showsubmit('course_addsubmit');
		showtablefooter();
		showformfooter();


}else if ($input['act'] =='delk' &&  FORMHASH == $input['formhash'] && $input['cid']) {

	//is course_exists
	$video->delete_k($input['cid']);
	cpmsg(lang('plugin/wxz_live', 'delte_success'),'action=plugins&operation=config&identifier=wxz_live&pmod=input&act=admin','success');
}else if ($input['act'] =='outsellk' &&  FORMHASH == $input['formhash'] && $input['cid']) {

	//is course_exists
	$rs = $video->set_course_upatesale($input['cid']);

	cpmsg(lang('plugin/wxz_live', 'update_sell_success'),'action=plugins&operation=config&identifier=wxz_live&pmod=input&act=admin','success');
}else if ($input['act'] =='admin') {

	$mpurl=ADMINSCRIPT.'?action='.$url;
	$courses = $video->get_type_course_fmt($start,$perpage,'desc','',array('isdel'=>'0'));
	$num = $video->get_type_course_num(array('isdel'=>'0'));

	showtableheader();
		showsubtitle(array(lang('plugin/wxz_live', 'cid'),lang('plugin/wxz_live', 'sellner'),lang('plugin/wxz_live', 'title'),lang('plugin/wxz_live', 'issell'),lang('plugin/wxz_live', 'sellnum'),lang('plugin/wxz_live', 'price'),lang('plugin/wxz_live', 'diff'),lang('plugin/wxz_live', 'progress'),lang('plugin/wxz_live', 'img'),lang('plugin/wxz_live', 'desc'),lang('plugin/wxz_live', 'datetime'),lang('plugin/wxz_live', 'act')));
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
			cpmsg(lang('plugin/wxz_live', 'must_finish_videoname'),'','error');
		}
		if ($input['vid']) {
			$video_data['vid'] = $input['vid'] + 0;
		}
		$isreplace = $video_data['vid'] ? true : false;

		C::t("#wxz_live#wxz_live")->insert($video_data,false,$isreplace);


		cpmsg(lang('plugin/wxz_live', 'add_video_success'),'action=plugins&operation=config&identifier=wxz_live&pmod=input&act=adminvideo&m=admin&cid='.$input['cid'],'success');

		
	}else{

		$v = $video->get_video_by_vid($input['vid']);
		$cid = $v['cid'] ? $v['cid'] : $input['cid'];
		showformheader($url.'&act=adminvideo&m=add&cid='.$cid,'enctype="multipart/form-data"');
		showtableheader();
		if ($v['vid']) {
			showsetting(lang('plugin/wxz_live', 'vid'), 'vid', $v['vid'], 'text','','',lang('plugin/wxz_live', ''),'size="10" readonly="readonly"');
		}

		showsetting(lang('plugin/wxz_live', 'video_name'), 'video_name', $v['video_name'], 'text','','',lang('plugin/wxz_live', ''),'size="10"');
		showsetting(lang('plugin/wxz_live', 'video_url'), 'video_url', stripslashes($v['video_url']), 'textarea','','',lang('plugin/wxz_live', 'video_url_desc'),'size="10"');

		$urltype_sellect = array();
		foreach ($wxz_liveconf['video_url_type'] as $key => $value) {
			$urltype_sellect[] = array($key,$value);
		}
		showsetting(lang('plugin/wxz_live', 'video_urltype'), array('video_urltype',$urltype_sellect), $v['video_urltype']? $v['video_urltype'] : '0', 'mradio','','',lang('plugin/wxz_live', 'isfree_desc'),'size="10"');

		//showsetting(lang('plugin/wxz_live', 'video_urltype'), array('video_urltype',array(array('0',$urltype_sellect['video_urltype']['0']),array('1',$urltype_sellect['video_urltype']['1']))), $v['video_urltype'], 'mradio','','',lang('plugin/wxz_live', 'course_urltype_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'isfree'), array('isfree',array(array('0',$wxz_liveconf['isfree']['0']),array('1',$wxz_liveconf['isfree']['1']))), $v['isfree'], 'mradio','','',lang('plugin/wxz_live', 'isfree_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'video_price'), 'video_price', $v['video_price'] / 100, 'text','','',lang('plugin/wxz_live', 'course_price_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'video_length'), 'video_length', $v['video_length'], 'text','','',lang('plugin/wxz_live', 'video_length_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'video_img'), 'video_img', $v['video_img'], 'filetext','','',lang('plugin/wxz_live', 'course_img_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'video_intro'), 'video_intro', $v['video_intro'], 'textarea','','',lang('plugin/wxz_live', 'course_intro_desc'),'size="10"');
		showsubmit('video_addsubmit');
		showtablefooter();
		showformfooter();	}

}else if ($input['act'] =='adminvideo' && $input['m'] =='admin' && $input['cid']) {

	$mpurl=ADMINSCRIPT.'?action='.$url.'&act=adminvideo&m=admin&cid='.$input['cid'];
	$num = $video->get_type_video_num(array('cid'=>$input['cid']));
	$videoes = $video->get_type_video_fmt($start,$perpage,'desc','',array('cid'=>$input['cid'],'isdel'=>'0'));
	showtableheader();
		showsubtitle(array(lang('plugin/wxz_live', 'vid'),lang('plugin/wxz_live', 'video_name'),lang('plugin/wxz_live', 'video_price'),lang('plugin/wxz_live', 'isfree'),lang('plugin/wxz_live', 'video_url'),lang('plugin/wxz_live', 'video_urltype'),lang('plugin/wxz_live', 'video_length'),lang('plugin/wxz_live', 'selltimes'),lang('plugin/wxz_live', 'video_img'),lang('plugin/wxz_live', 'dateline'),lang('plugin/wxz_live', 'act')));
		foreach ($videoes as $key => $value) {
			$value['video_url'] = '<a href="'.$value['video_url'].'" target="_blank">'.lang('plugin/wxz_live','click_check').'</a>';
			showtablerow('',array('class="td15"', 'class="td32"', 'class="td28"', 'class="td28"', 'class="td31"'),array_values($value));
		}
	showtablefooter();
	$multi = multi($num, $perpage, $curpage, $mpurl, '0', '10');
	echo $multi;
}else if ($input['act'] =='adminvideo' && $input['m'] =='delete' &&  FORMHASH == $input['formhash'] && $input['vid']) {
	
	$video->delete_video($input['vid']);
	cpmsg(lang('plugin/wxz_live', 'delete_video_success'),dreferer(),'success');
}else if ($input['act'] =='order' &&  $input['m'] =='setpay' &&  FORMHASH == $input['formhash'] && $input['oid']) {
	$r = $video->order_setpay($input['oid'],$input['paystatus']+0);
	$video->update_order_status_byoid($input['oid']);
	if ($r['code'] > 0) {
		cpmsg(lang('plugin/wxz_live', $r['msg']),dreferer(),'success');
	}
	cpmsg(lang('plugin/wxz_live', $r['msg']),dreferer(),'error');

}else if ($input['act'] =='order' &&  $input['m'] =='setcontract' &&  FORMHASH == $input['formhash'] && $input['oid']) {
	$r = $video->order_setcontract($input['oid'],$input['contractstatus']+0);
	if ($r['code'] > 0) {
		cpmsg(lang('plugin/wxz_live', $r['msg']),dreferer(),'success');
	}
	cpmsg(lang('plugin/wxz_live', $r['msg']),dreferer(),'error');

}else if ($input['act'] =='order' &&  $input['m'] =='setmail' &&  FORMHASH == $input['formhash'] && $input['oid']) {
	$r = $video->order_setmail($input['oid'],$input['mailstatus']+0);
	if ($r['code'] > 0) {
		cpmsg(lang('plugin/wxz_live', $r['msg']),dreferer(),'success');
	}
	cpmsg(lang('plugin/wxz_live', $r['msg']),dreferer(),'error');

}else if ($input['act'] =='order' &&  $input['m'] =='cleanplaycount' &&  FORMHASH == $input['formhash'] && $input['oid']) {
	$r = $video->cleanplaycount($input['oid']);
	if ($r['code'] > 0) {
		cpmsg(lang('plugin/wxz_live', $r['msg']),dreferer(),'success');
	}
	cpmsg(lang('plugin/wxz_live', $r['msg']),dreferer(),'error');

}else if ($input['act'] =='order' &&  $input['m'] =='checkorder' &&  FORMHASH == $input['formhash'] && $input['oid']) {
	$o = $video->get_order_byoid_fmt($input['oid'] + 0);
	if (empty($o)) {
		cpmsg(lang('plugin/wxz_live', 'order_isnot_exists'),dreferer(),'error');
	}
	include template('wxz_live:admin/order_info');

}else if ($input['act'] =='order') {
	$mpurl=ADMINSCRIPT.'?action='.$url.'&act=order';
	$num = $video->get_orders_num();
	$orders = $video->get_orders_fmt($start,$perpage,'desc');

	showtableheader(); 
		showsubtitle(array(lang('plugin/wxz_live', 'oid'),lang('plugin/wxz_live', 'cid'),lang('plugin/wxz_live', 'cname'),lang('plugin/wxz_live', 'vid'),lang('plugin/wxz_live', 'ispayed'),lang('plugin/wxz_live', 'course_price'),lang('plugin/wxz_live', 'buyer_uid'),lang('plugin/wxz_live', 'out_trade_no'),lang('plugin/wxz_live', 'orderdateline'),lang('plugin/wxz_live', 'pay_time'),lang('plugin/wxz_live', 'orderstatus'),lang('plugin/wxz_live', 'act')));
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
	$video = new wxz_live();
	$course = $video->get_course_bycid($cid,false,true);
	
	if (empty($course)) {
		cpmsg(lang('plugin/wxz_live', 'course_isnot_exists'),dreferer(),'error');

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
		showsetting(lang('plugin/wxz_live', 'username'), 'username', '', 'text','','',lang('plugin/wxz_live', 'username_desc'),'size="10"');
		showsetting(lang('plugin/wxz_live', 'cid'), 'cid', '', 'text','','',lang('plugin/wxz_live', 'cid_desc'),'size="10"');
		showsubmit('setvipbyhand_addsubmit');
		showtablefooter();
		showformfooter();

}


?>