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
$cate = new wxz_live();

$mpurl=ADMINSCRIPT.'?action=plugins&operation=config&identifier=wxz_live&pmod=touchadmin';
$formurl = 'plugins&operation=config&identifier=wxz_live&pmod=touchadmin';

$_GET['act'] = $_GET['act'] ? $_GET['act'] : 'swiper';

zms_showtitle(lang('plugin/wxz_live', 'touchadmin'),array(
	array(lang('plugin/wxz_live', 'swiper'),$formurl.'&act=swiper',$_GET['act'] =='swiper'?'1':'0'),
	// array(lang('plugin/wxz_live', '推荐管理'),$formurl.'&act=recommend',$status = $input['act'] =='recommend'?'1':'0'),
	array(lang('plugin/wxz_live', 'best_recommend'),$formurl.'&act=best',$_GET['act'] =='best'?'1':'0')
));


if (submitcheck('sb_editswiper')) {

	$swiper = array();
	foreach ($_GET['image'] as $key => $value) {
		if (!$value || in_array($key, $_GET['delete'])) {
			continue;
		}
		$swiper[] = array(
			'name'=>$_GET['name'][$key],
			'image'=>$value,
			'url'=>$_GET['url'][$key]
		);
	}


	$cate->update_touch_swipercache_init($swiper);
	cpmsg(lang('plugin/wxz_live', 'update_swiper_success'),dreferer(),'success');

}else if (submitcheck('sb_editbest')) {
	$best = array();
	foreach ($_GET['image'] as $key => $value) {
		if (!$value || !$_GET['cid'][$key] || in_array($key, $_GET['delete'])) {
			continue;
		}

		$c = $cate->get_course_bycid($_GET['cid'][$key]);
		if (empty($c) || !$c) {
			cpmsg(lang('plugin/wxz_live', 'cid_isnot_exists'),dreferer(),'error');		
		}

		$best[] = array(
			'cid'=>$_GET['cid'][$key],
			'image'=>$value
		);
	}	
	$cate->update_touch_bestcache_init($best);

	cpmsg(lang('plugin/wxz_live', 'update_best_success'),dreferer(),'success');
}else{
	if ($_GET['act'] == 'swiper') {
		$swiper = $cate->get_touch_swiper();
		showformheader($formurl.'&act=editcat','enctype="multipart/form-data"');
		showtableheader(lang('plugin/wxz_live','swiper'));
		showsubtitle(array(
			lang('plugin/wxz_live', 'delete'),
			//lang('plugin/wxz_live', '编号'),
			lang('plugin/wxz_live', 'swiper_name'),
			lang('plugin/wxz_live', 'imgurl'),
			lang('plugin/wxz_live', 'url')
		));

		foreach ($swiper as $key => $value) {
			$value['id'] = $key;
			showformheader($formurl.'&act=editcat','enctype="multipart/form-data"');
				$catearr = array(
				'<input type="checkbox" class="txt" name="delete['.$value['id'].']" value="'.$value['id'].'" '.$protected.' />',
				'<input type="text" class="txt" name="name['.$value['id'].']" value="'.$value['name'].'" />',
				'<input type="text"  name="image['.$value['id'].']" value="'.$value['image'].'" />',
				'<input type="text"  name="url['.$value['id'].']" value="'.$value['url'].'" />'
			);
			showtablerow('',array('class="td25"', 'class="td25"', 'class="td22"', 'class="td22"', 'class="td22"'), $catearr);

		}
		echo '<tr><td colspan="2"><div class="lastboard"><a href="###" onclick="addrow(this, 0);" class=" addtr">'.lang('plugin/wxz_live', 'addswiper').'</a></div></tr>';

		showsubmit('sb_editswiper',lang('plugin/wxz_live', 'submit'));

		showtablefooter();
		showformfooter();	


	echo <<<EOT
	<script type="text/JavaScript">
		var rowtypedata = [
			[
				[1,'<input type="checkbox" class="txt" name="deldete[]" value="">', 'td25'],
				[1,'<input type="text" class="txt" name="name[]" value="">', 'td25'],
				[1,'<input type="text" class="" name="image[]" value="">', 'td22'],
				[1,'<input type="text" class="" name="url[]" value="">', 'td22'],

			]
			
		];
	</script>
EOT;

	}else if ($_GET['act'] =='best') {
		showformheader($formurl.'&act=editbest','enctype="multipart/form-data"');
		showtableheader(lang('plugin/wxz_live','edit_best_recommend'));
		showsubtitle(array(
			lang('plugin/wxz_live', 'delete'),
			lang('plugin/wxz_live', 'cid'),
			lang('plugin/wxz_live', 'bestimg'),
		));

		$best = $cate->get_touch_best();
		foreach ($best as $key => $value) {
			showformheader($formurl.'&act=editcat','enctype="multipart/form-data"');
				$catearr = array(
				'<input type="checkbox" class="txt" name="delete['.$key.']" value="'.$key.'" '.$protected.' />',
				'<input type="text" class="txt" name="cid['.$key.']" value="'.$value['cid'].'" />',
				'<input type="text"  name="image['.$key.']" value="'.$value['image'].'" />',
			);
			showtablerow('',array('class="td25"', 'class="td25"', 'class="td22"', 'class="td22"', 'class="td22"'), $catearr);
		}


		echo '<tr><td colspan="2"><div class="lastboard"><a href="###" onclick="addrow(this, 0);" class=" addtr">'.lang('plugin/wxz_live', 'addbestrecommend').'</a></div></tr>';


		showsubmit('sb_editbest',lang('plugin/wxz_live', 'submit'));

		showtablefooter();
		showformfooter();	
	echo <<<EOT
	<script type="text/JavaScript">
		var rowtypedata = [
			[
				[1,'<input type="checkbox" class="txt" name="deldete[]" value="">', 'td25'],
				[1,'<input type="text" class="txt" name="cid[]" value="">', 'td25'],
				[1,'<input type="text" class="" name="image[]" value="source/plugin/wxz_live/template/touch/static/img/xiaochengxu414*132.png">', 'td22'],

			]
			
		];
	</script>
EOT;


	}




}

?>