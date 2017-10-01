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

include_once DISCUZ_ROOT.'./source/plugin/zhanmishu_wepay/source/function/api_function.php';


if (!$_G['uid']) {
	showmessage('to_login', '', array(), array('showmsg' => true, 'login' => 1));
}

$video = new wxz_live();
$out_trade_no = $video->get_rand_trade_no();
 $data  = array(
	        'out_trade_no'=>$out_trade_no,
	        'total_fee'=>99,
	        'intro'=>'99元技法VIP一年，可免费学习技法VIP所有课程',
	        'name'=>'99元技法VIP一年',
	        'bank_type'=>$_GET['bank_type'],
	        'num'=>'1',
	        'price'=>99,
	        'return_url'=>'http://xxx.com/success.html',
	);
	$url=$_G['siteurl'].'plugin.php?id=zhanmishu_wepay:pay&mod=pay&'.http_build_query($data);

	showmessage('', $url, array(),array('showid' => '','extrajs' => '<script type="text/javascript">'.'top.location.href="'.$url.'";</script>'.$ucsynlogin,'showdialog' => false));

?>