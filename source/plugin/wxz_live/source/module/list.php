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

$video = new wxz_live();
$videoconfig = $video->config;


$cat = $video->get_cat_tree();
$catpid = $input['cat_id'] ? $video->get_pidbycat_id($input['cat_id']) : '0';
$catid = $input['cat_id'] ? $input['cat_id'] : '';
$catson =  $video->get_cat_tree($catpid ? $catpid : $input['cat_id']);

$videoBaseUrl = 'plugin.php?id=wxz_live:video';
$pdata = $input;
unset($pdata['page']);
$mpurl = 'plugin.php?'.urldecode(http_build_query($pdata));
$cdata = $input;
unset($cdata['cat_id']);
unset($cdata['page']);

$caturl = 'plugin.php?'.urldecode(http_build_query($cdata));
$ddata = $input;
unset($ddata['diff']);
$durl = 'plugin.php?'.urldecode(http_build_query($ddata));
$odata = $input;
unset($odata['order']);
$ourl = 'plugin.php?'.urldecode(http_build_query($odata));
$gdata = $input;
unset($gdata['groupselect']);
unset($gdata['page']);
$gurl = 'plugin.php?'.urldecode(http_build_query($gdata));

$perpage=12;
$curpage = ($input['page'] + 0) > 0 ? ($input['page'] + 0) : 1;
$pages= ceil($num / $perpage);
$start = $num - ($num - $perpage*$curpage+$perpage);
$field = array();
$field['isdel'] = '0';
if ($input['cat_id']) {
	$field['cat_id'] = $input['cat_id'] + 0;
	$catinfo = $video->get_cat_by_cat_id($input['cat_id'] + 0);
}
if ($input['diff']) {
	$field['diff'] = $input['diff'];
}
if ($input['groupselect']) {
	$field['course_group'] = array('value'=>'#'.$input['groupselect'].'#','type'=>'like');
}

$num = $video->get_type_course_num($field); 
$perpage=12;
$curpage = ($input['page'] + 0) ? ($input['page'] + 0) : 1;
$pages= ceil($num / $perpage);
$start = $num - ($num - $perpage*$curpage+$perpage);

if ($input['order'] == 'new') {
	$sort = array('course_weight'=>'desc','dateline'=>'desc');
}else if($input['order'] == 'hot'){
	$sort = array('course_weight'=>'desc','views'=>'desc');
}else {
	$sort = array('course_weight'=>'desc','cid'=>'desc');
}


if (defined('IN_MOBILE')) {
	$cate = array_chunk($video->get_cat_by_level('1',array('cat_touchorder'=>'asc')),8);
	$swiper = $video-> get_touch_swiper();
	$best = $video->get_touch_best();
	$list = $video->get_type_course('0','10',$sort,'',$field);
}else{
	$multi = multi($num, $perpage, $curpage, $mpurl, '0');
	$list = $video->get_type_course($start,$perpage,$sort,'',$field);
}
$groupicons = $video->get_group_icons();


if ($_GET['dtype']) {
	
	$swiper = $video-> get_touch_swiper();
	$best = $video->get_touch_best();
	$outapi = array(
		'msg'=>'success',
		'code'=>'0',
		'data'=>array(),
	);

	$outapi['data']['swiper'] = $swiper;
	$outapi['data']['best'] = $best;
	$outapi['data']['list'] = $list;
	$outapi['data']['catinfo'] = $catinfo;
	$outapi['data']['groupicons'] = $groupicons;
	$outapi = zms_diconv($outapi,CHARSET,'utf-8');
	echo json_encode($outapi);
	exit;
}



include template('wxz_live:'.$mod);


?>