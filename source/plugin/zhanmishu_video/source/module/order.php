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

if (!$_G['uid']) {
	showmessage('to_login', '', array(), array('showmsg' => true, 'login' => 1));
}
$input = daddslashes($_GET);

$act = $input['act'] ? $input['act'] : 'list';
$order = new zhanmishu_video();
$orderconfig = $order->config;
if ($input['oid']) {
	$o = $order->get_order_byoid($input['oid'] + 0);
	if (empty($o) || $o['buyer_uid'] !== $_G['uid']) {
		showmessage(lang('plugin/zhanmishu_video', 'data_error'));
	}
}

if ($o['ispayed'] =='0' && $o['out_trade_no'] && $o['checknum'] == '0') {
	$order->check_ispay_byout_trade_no($o['out_trade_no']);
}

 $o['status'] = $order->update_order_status_byoid($o['oid']);


if ($act =='list') {
	$field = array('buyer_uid'=>$_G['uid']);
	if ($input['status']) {
		$field['status'] = $input['status'] + 0;
	}
	$orders = $order->get_type_order(0,0,'','',$field); 
}else if ($act=='contract') {
	if ($input['oid']) {

		if (submitcheck('ordersignsubmit')) {
			$input = daddslashes($input);

			$images = zms_uploadimg('zhanmishu_video/sign/');
			if (!empty($images)) {
				if ($images['verifyimg1']) {
					$orderk= '1';
					$images['verifyimg'] = $images['verifyimg1'];
				}else if ($images['verifyimg2']) {
					$orderk= '2';
					$images['verifyimg'] = $images['verifyimg2'];
				}

				$noorderk = $orderk == '1' ? '2' : '1';
				include template('zhanmishu_video:order/order_contract_ajax');
				exit;
			}
			if (!$input['sign_img1'] || !$input['sign_img2']) {
				showmessage(lang('plugin/zhanmishu_video', 'data_error'));
			}

			if ($o['issign'] =='0') {
				$img = array(
				'sign_img2'=>$input['sign_img2'],
				'sign_img1'=>$input['sign_img1'],
				'issign'=>'1',
				'sign_time'=>TIMESTAMP
				);
				C::t("#zhanmishu_video#zhanmishu_video_order")->update($o['oid'],$img);
				$msg = lang('plugin/zhanmishu_video','sign_success');
			}else{
				$msg = lang('plugin/zhanmishu_video','have_signed');
			}

			$js = '<script type="text/javascript">showDialog("'.$msg.'","notice","",function(){top.location.href="'.dreferer().'";})</script>';
			showmessage(lang('plugin/zhanmishu_video',''), '', array(), array('msgtype' => 3,'showdialog'=>true,'showmsg'=>false,'extrajs'=>$js));
		}


	}else{
		$field = array('buyer_uid'=>$_G['uid']);
		if ($input['contractstatus']) {
			$field['order_type'] ='1';
			switch ($input['contractstatus']) {
				case '1'://finish
					$field['ispayed'] ='1';
					$field['issign'] ='1';
					$field['isconfirm'] ='1';
					$field['ismail'] ='1';
					$field['issuccess'] ='1';
					$field['isclosed'] ='0';
					break;

				case '2': // no sign
					$field['ispayed'] ='1';
					$field['isconfirm'] ='0';
					$field['issign'] ='0';
					$field['ismail'] ='0';
					$field['issuccess'] ='0';
					$field['isclosed'] ='0';
					break;

				case '3':// wait confirm
					$field['ispayed'] ='1';
					$field['isconfirm'] ='0';
					$field['issign'] ='1';
					$field['ismail'] ='0';
					$field['issuccess'] ='0';
					$field['isclosed'] ='0';
					break;
				case '4':// wait mail
					$field['ispayed'] ='1';
					$field['isconfirm'] ='1';
					$field['issign'] ='1';
					$field['ismail'] ='1';
					$field['issuccess'] ='0';
					$field['isclosed'] ='0';
					break;
				
				case '5':// wait success
					$field['ispayed'] ='1';
					$field['isconfirm'] ='1';
					$field['issign'] ='1';
					$field['ismail'] ='1';
					$field['issuccess'] ='0';
					$field['isclosed'] ='0';
					break;
				case '6':// closed
					$field['isclosed'] ='1';
					break;
				
				default:
					# code...
					break;
			}
		}
		$orders = $order->get_type_order(0,0,'','',$field); 
	}
}else if ($act=='mail') {

	if ($input['formhash'] == formhash()) {
		C::t("#zhanmishu_video#zhanmishu_video_order")->update($o['oid'],array('ismail'=>'1'));
		$msg = lang('plugin/zhanmishu_video','email_success');
		$js = '<script type="text/javascript">showDialog("'.$msg.'","notice","",function(){top.location.href="'.dreferer().'";})</script>';
		showmessage(lang('plugin/zhanmishu_video',''), '', array(), array('msgtype' => 3,'showdialog'=>true,'showmsg'=>false,'extrajs'=>$js));
	}
}else if ($act=='closed') {
	if ($o['ispayed'] =='1') {
		$js = '<script type="text/javascript">showDialog("'.lang('plugin/zhanmishu_video','cannot_closed_tips').'","notice","",function(){top.location.href="'.dreferer().'";})</script>';
		showmessage(lang('plugin/zhanmishu_video','cannot_closed_tips'), '', array(), array('msgtype' => 3,'showdialog'=>true,'showmsg'=>false,'extrajs'=>$js));
	}
	if ($input['formhash'] == formhash() && $o['oid']) {
		C::t("#zhanmishu_video#zhanmishu_video_order")->update($o['oid'],array('isclosed'=>'1'));
		$js = '<script type="text/javascript">showDialog("'.lang('plugin/zhanmishu_video','closed_tips').'","notice","",function(){top.location.href="'.dreferer().'";})</script>';
		showmessage(lang('plugin/zhanmishu_video','closed_tips'), '', array(), array('msgtype' => 3,'showdialog'=>true,'showmsg'=>false,'extrajs'=>$js));
	}
}else if ($act=='down') {
	if ($o['status'] =='1') {
		$c = $order->get_course_bycid($o['cid']);
		$o = array_merge($c,$o);
	}
}



if ($_GET['dtype']) {
	$outapi = array(
		'msg'=>'success',
		'code'=>'0',
		'data'=>array(),
	);
	$outapi['data']['orders'] = $orders;

	$outapi = zms_diconv($outapi,CHARSET,'utf-8');
	echo json_encode($outapi);
	exit;
}


//print_r($orders);

include template('zhanmishu_video:'.$mod);

?>