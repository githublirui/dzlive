<?php
/*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.comm
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
include_once DISCUZ_ROOT.'./source/plugin/zhanmishu_video/source/Autoloader.php';
include_once DISCUZ_ROOT.'./source/plugin/zhanmishu_video/source/function/common_function.php';
$cate = new zhanmishu_video();
$mpurl=ADMINSCRIPT.'?action=plugins&operation=config&identifier=zhanmishu_video&pmod=cateadmin';
$formurl = 'plugins&operation=config&identifier=zhanmishu_video&pmod=cateadmin';

$catetree = $cate->get_cat_tree('0','0',false);
$cate->update_catecache();


$input = daddslashes($_GET);


if (submitcheck('cateedit')) {


	//add cate
	$cateinput = array();
	foreach ($input['cat_id'] as $key => $value) {
		if ($input['cat_name'][$key]) {
			$cateinput[$key]['cat_name'] = $input['cat_name'][$key];
			$cateinput[$key]['dateline'] = TIMESTAMP;
		}else{
			continue;
		}

		if ($value) {
			$cateinput[$key]['cat_id'] = $value;
		}
		$cateinput[$key]['parent_id'] = $input['parent_id'][$key] ? $input['parent_id'][$key] : '0';
		$cateinput[$key]['sort'] = $input['catesort'][$key] ? $input['catesort'][$key] : '0';
		$cateinput[$key]['level'] = $input['parent_id'][$key] ? '1' : '0';

	}

	//add son
	foreach ($input['newcat_name'] as $key => $value) {
		if (!$value) {
			continue;
		}
		$tmp = array();
		$tmp['cat_name'] = $value;
		$tmp['sort'] = $input['newcatesort'][$key];
		$tmp['parent_id'] = $input['newparent_id'][$key];
		$tmp['level'] = '1';
		$tmp['dateline'] = TIMESTAMP;
		$cateinput[] = $tmp;
	}

	foreach ($cateinput as $key => $value) {
		C::t("#zhanmishu_video#zhanmishu_video_cat")->insert($value,true,true);
	}	


	// foreach ($input['delete'] as $key => $value) {
	// 	C::t("#zhanmishu_video#zhanmishu_video_cat")->update($value,array('isdel'=>'1'));
	// }

	//del cate
	if (is_array($input['delete']) && !empty($input['delete'])) {
		foreach ($input['delete'] as $key => $value) {
			C::t("#zhanmishu_video#zhanmishu_video_cat")->delete($value);
		}
	}
	$cate->update_catecache();
	cpmsg(lang('plugin/zhanmishu_video', 'update_cate_success'),dreferer(),'success');
}else if (submitcheck('sb_editcat') && $_GET['cat_id']) {
	$images = zms_uploadimg('zhanmishu_video/',false);
	$catadd = array();

	$catadd['cat_icon'] = $images['cat_icon'] ? $images['cat_icon'] : $input['cat_icon'];
	$catadd['cat_name'] = $input['cat_name'];
	$catadd['cat_touchorder'] = $input['cat_touchorder'];

	C::t("#zhanmishu_video#zhanmishu_video_cat")->update($_GET['cat_id']+ 0,$catadd);
	cpmsg(lang('plugin/zhanmishu_video', 'update_cate_success'),dreferer(),'success');

	exit;
}

if ($_GET['act'] =='editcat') {
	$cat = $cate->get_cat_by_cat_id($input['cat_id']);
	showformheader($formurl.'&act=editcat','enctype="multipart/form-data"');
	showtableheader();
	showsetting(lang('plugin/zhanmishu_video', 'cat_id'), 'cat_id', $cat['cat_id'], 'text','','','','size="10" readonly="readonly"');
	showsetting(lang('plugin/zhanmishu_video', 'cat_id'), 'cat_name', $cat['cat_name'], 'text','','','','size="10"');
	showsetting(lang('plugin/zhanmishu_video', 'cat_icon'), 'cat_icon', $cat['cat_icon'], 'filetext','','','<img src="'.$cat['cat_icon'].'" maxwidth="98px" maxheight="98px" >&nbsp;&nbsp; '.lang('plugin/zhanmishu_video','cat_icon_desc'),'size="10"');
	showsetting(lang('plugin/zhanmishu_video', 'cat_touchorder'), 'cat_touchorder', $cat['cat_touchorder'], 'text','','',lang('plugin/zhanmishu_video','cat_touchorder_desc'),'size="10"');

	showsubmit('sb_editcat',lang('plugin/zhanmishu_video', 'submit'));

	showtablefooter();
	showformfooter();	
}else{

showtips(lang('plugin/zhanmishu_video', 'cateedittips'),'',true,lang('plugin/zhanmishu_video', 'cateedittips_title'));
showformheader('plugins&operation=config&do=59&identifier=zhanmishu_video&pmod=cateadmin');
showtableheader(lang('plugin/zhanmishu_video', 'cateadmin'));
	showsubtitle(array(
		lang('plugin/zhanmishu_video', 'delete'),
		lang('plugin/zhanmishu_video', 'sort'),
		lang('plugin/zhanmishu_video', 'catename')
	));




foreach ($catetree as $key => $value) {
		$catearr = array(
			'<input type="checkbox" class="txt" name="delete['.$value['cat_id'].']" value="'.$value['cat_id'].'" '.$protected.' />',
			'<input type="text" class="txt" name="catesort['.$value['cat_id'].']" value="'.$value['sort'].'" />',
			'<input type="text" class="txt" name="cat_name['.$value['cat_id'].']" value="'.$value['cat_name'].'" />',
			'<input type="hidden"  name="cat_id['.$value['cat_id'].']" value="'.$value['cat_id'].'" />',
			'<input type="hidden"  name="parent_id['.$value['cat_id'].']" value="0" />'
		);
		showtablerow('',array('class="td25"', 'class="td25"'), $catearr);
		if (is_array($value['son']) && !empty($value['son'])) {
			foreach ($value['son']  as $v) {
				showtablerow('', array('class="td25"', 'class="td25"', 'class=" "', '', 'class="td30"',  'class="td25"', 'class="td25"', 'class="td25"'), array(
					'<input type="checkbox" class="txt" name="delete['.$v['cat_id'].']" value="'.$v['cat_id'].'" '.$protected.' />',
					'<input type="text" class="txt" name="catesort['.$v['cat_id'].']" value="'.$v['sort'].'" />',
					'<div class="board"><input type="text" class="txt" name="cat_name['.$v['cat_id'].']" value="'.$v['cat_name'].'" /></div>',
					'<a href="'.$mpurl.'&act=editcat&cat_id='.$v['cat_id'].'">'.lang('plugin/zhanmishu_video','edit').'</a>',
					'<input type="hidden" class="txt" name="cat_id['.$v['cat_id'].']" value="'.$v['cat_id'].'" />',
					'<input type="hidden" class="txt" name="parent_id['.$v['cat_id'].']" value="'.$value['cat_id'].'" />',
				));
			}
		}
		showtablerow('',array(),array(
			'',
			'',
			'<div class="lastboard"><a href="###" onclick="addrow(this, 1,'.$value['cat_id'].');" class=" addtr">'.lang('plugin/zhanmishu_video', 'addnewcateson').'</a></div>'

		));
}

	
		echo '<tr><td colspan="2"><div class="lastboard"><a href="###" onclick="addrow(this, 0);" class=" addtr">'.lang('plugin/zhanmishu_video', 'addnewcate').'</a></div></tr>';


echo <<<EOT
	<script type="text/JavaScript">
		var rowtypedata = [
			[
				[1,'<input type="checkbox" class="txt" name="deldete[]" value="">', 'td25'],
				[1,'<input type="text" class="txt" name="catesort[]" value="0">', 'td25'],
				[1,'<input type="text" class="txt" name="cat_name[]" value="">', ' '],
				[1,'<input type="hidden" class="txt" name="cat_id[]" value="">', ' '],
				[1,'<input type="hidden" class="txt" name="parent_id[]" value="0">', ' '],

			],
			[
			[1,'<input type="checkbox" class="txt"  value="">', 'td25'],
				[1,'<input type="text" class="txt" name="newcatesort[]" value="0">', 'td25'],
				[1,'<div class="board"><input type="text" class="txt" name="newcat_name[]" value=""></div>',''],
				[1,'<input type="hidden" class="txt" name="newcat_id[]" value="">',''],
				[1,'<input type="hidden" class="txt" name="newparent_id[]" value="{1}">',''],

			]
		];
	</script>
EOT;
	showsubmit('cateedit',lang('plugin/zhanmishu_video', 'submit'));
showtablefooter();
showformfooter();	
}



?>