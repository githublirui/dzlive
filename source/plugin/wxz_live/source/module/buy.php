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

$cid = $input['cid'];


if (!$_G['uid']) {
	showmessage('to_login', '', array(), array('showmsg' => true, 'login' => 1));
}

$video = new wxz_live();
$course = $video->get_course_bycid($cid,false,true);
$videoconfig = $video->config;
if (!$cid || empty($course) || $course['isdel'] == '1' || $course['issell'] =='0') {
	showmessage(lang('plugin/wxz_live', 'data_error'));
}else if ($input['formhash']==formhash()) {

	if ($videoconfig['isverify']) {
		$isverify = $video->check_user_isverify();
		$isverifysubmit = $video->check_verify_issubmit();
		$isverifysubmitcheck = $isverifysubmit && $input['isverifysubmit'];

		$verifytips = $isverifysubmit ? lang('plugin/wxz_live','verifytips_check') : lang('plugin/wxz_live','verifytips') ;

		if (!$isverify && !$isverifysubmitcheck && $course['course_type'] =='1') {
			showmessage($verifytips, 'home.php?mod=spacecp&ac=profile&op=verify&vid='.$video->get_verify_id().'&return_url='.urlencode(dreferer()), array(), array('msgtype' => 1,'showmsg'=>true));
		}
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

	
	
	if ($input['oid']) {
		$checko['oid'] = $input['oid'] + 0;
		$checkorder = $video->get_order_byoid($checko['oid']);
	}else{
		//check order
		$checko = array();
		$checko['cid'] = $o['cid'];
		$checko['buyer_uid'] = $_G['uid'];
		$checko['ispayed'] = '0';
		$checko['isclosed'] = '0';
		$checkorder = $video->get_one_order_byfield($checko);
	}
	

	if (empty($checkorder) && $input['oid']) {
		showmessage(lang('plugin/wxz_live', 'order_error'));
	}else if ($checkorder['ispayed'] == '1') {
		showmessage(lang('plugin/wxz_live', 'order_is_payed'));
	}


	if ($checkorder['oid']) {
		$o['oid'] = $checkorder['oid'];
	}

	$return_url = $input['return_url'] ? urldecode($input['return_url']) : dreferer();

	$data  = array(
			'out_trade_no'=>$out_trade_no,
			'total_fee'=>strval($o['course_price'] / 100),
			'intro'=>remove_nopromissword($o['course_intro']),
			'name'=>remove_nopromissword($o['course_name']),
			'bank_type'=>$input['bank_type'],
			'num'=>'1',
			'price'=>strval($o['course_price'] / 100),
			'return_url'=>$return_url,
			);
	C::t("#wxz_live#wxz_live_order")->insert($o,false,true);




	//if pay not is  no't support wxz_wepay
	$config = $video->config;
	$wxzPayExists =  $video->ZmsIsWepayExists();

	if (($config['paytype'] == '1' || !$wxzPayExists) && $config['paytype_extcredits']) {
		$moneyNum = $config['moneyper'] * $o['course_price'] / 100;
		if ($_GET['act'] == 'do') {
			if ($_GET['total_fee'] !== $o['course_price']) {
				showmessage(lang('plugin/wxz_live','price_is_updated'));
			}
			//检测是否已经支付
			if ($o['ispayed'] == '1') {
				showmessage(lang('plugin/wxz_live','you_have_payed'));
			}

			if ($video->checklowerlimit('-'.$moneyNum) !== true) {
				showmessage(lang('plugin/wxz_live','cr_isnot_enough_please_pay_times'));
				exit;
			}else{

				$c = array();
				$c['oid']=$o['oid'];
				$c['cid']=$o['cid'];
				$c['course_name']=$o['course_name'];
				$c['time']=date('Y-m-d H:i:s',$o['dateline']);

				$video->updatemembercount($_G['uid'],'-'.$moneyNum,lang('plugin/wxz_live','paytype_extcredits'),lang('plugin/wxz_live','paytype_extcredits_intro',$c));

			}

			$video->setsuccess($o['oid']);
			$js = '<script type="text/javascript">showDialog(\''.lang('plugin/wxz_live','pay_success').'\',\'confirm\',\'\',function(){top.location.href="'.$return_url.'";},0,function(){location.reload();});</script>';
	
			if (defined('IN_MOBILE')) {
				wxz_go_header($return_url);
			}
			showmessage(lang('plugin/wxz_live','buytips'), '', array(), array('msgtype' => 3,'showmsg'=>false,'extrajs'=>$js));
			
		}else{
			$url = $_SERVER["REQUEST_URI"].'&act=do&total_fee='.$o['course_price'].'&formhash='.FORMHASH.'&return_url='.urlencode($return_url);
			$js = '<script type="text/javascript">showDialog(\''.lang('plugin/wxz_live','buy_course_extcredits',array('moneyNum'=>$moneyNum)).'\',\'confirm\',\'\',function(){showWindow("buyvideoact", "'.$url.'")},0,function(){location.reload();});</script>';
			showmessage(lang('plugin/wxz_live','buytips'), '', array(), array('msgtype' => 3,'showmsg'=>false,'extrajs'=>$js));		
		}
		exit;
	}
	$url=$_G['siteurl'].'plugin.php?id=wxz_wepay:pay&mod=pay&'.http_build_query($data);

	if (defined('IN_MOBILE')) {
		wxz_go_header($url);
		exit;
	}
	$js = '<script type="text/javascript">top.location.href="'.$url.'";</script>';
	showmessage(lang('plugin/wxz_live','buytips'), '', array(), array('msgtype' => 3,'showmsg'=>false,'extrajs'=>$js));

}
?>