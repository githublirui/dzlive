<?php
/*
 *��˼����www.hfwxz.com
 *��������www.hfwxz.com
 *���ྫƷ��Դ�������˼���˹ٷ���վ��ѻ�ȡ
 *����Դ��Դ�������ռ�,��������ѧϰ����������������ҵ��;����������24Сʱ��ɾ��!
 *����ַ�������Ȩ��,�뼰ʱ��֪����,���Ǽ���ɾ��!
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
} 

// C::import('wxz_course','plugin/wxz_live/source/class');

class wxz_live extends wxz_course{

	public function get_type_video($start = 0, $limit = 0, $sort = '',$type = '',$field=array()){
		return C::t("#wxz_live#wxz_live")->get_type_video($start, $limit, $sort,$type,$field);
	}

	public function get_type_video_num($field){
		return C::t("#wxz_live#wxz_live")->get_type_video_num($field);
	}

	public function checkuser_ispay_course($cid,$uid){
		$o = array();
		$o['cid'] = $cid;
		$o['buyer_uid'] = $uid;
		$o['ispayed'] = '1';
		
		$payorder = $this->get_one_order_byfield($o);
		if (empty($payorder)) {
			return false;
		}
		return $payorder['oid'];
	}
	public function checkuser_isfinish_course($cid,$uid,$isconfirm=true,$issuccess=true){
		$o = array();
		$o['cid'] = $cid;
		$o['buyer_uid'] = $uid;
		$o['ispayed'] = '1';
		if ($isconfirm) {
			$o['isconfirm'] = '1';
		}
		if ($issuccess) {
			$o['issuccess'] = '1';
		}
		
		$payorder = $this->get_one_order_byfield($o);

		if (empty($payorder)) {
			return false;
		}
		return $payorder['oid'];
	}

	public function get_one_order_byfield($field=array()){
		return C::t("#wxz_live#wxz_live_order")->get_one_order_byfield($field);
	}

	public function issueorderbyuid($uid){
		global $_G;
		$uid = $uid ? $uid : $_G['uid'];

		$o = C::t("#wxz_live#wxz_live_order")->get_type_order(0,0,'','',array('buyer_uid'=>$uid,'ispayed'=>'0'));
		foreach ($o as $key => $value) {
			$this->check_ispay_byout_trade_no($value['out_trade_no']);
		}
	}

	public function check_ispay_byout_trade_no($out_trade_no){
		if (!$this->ZmsIsWepayExists()) {
			return false;
		}
		if (!function_exists('wxz_wepay_check_api')) {
			include_once DISCUZ_ROOT.'./source/plugin/wxz_wepay/source/function/api_function.php';
		}

		$o = $this->get_order_by_out_trade_no($out_trade_no);
		$rs = wxz_wepay_check_api($out_trade_no,$o['total_fee']);
		if ($rs['code'] == '1' && $o['oid']) {
			$this->selledsuccess($o['oid']);
		}
	}

	public function get_orders_num($filed=array()){
		return C::t("#wxz_live#wxz_live_order")->get_orders_num($field);
	}

	public function get_video_bycid($cid){
		return $this->get_type_video(0, 0,'asc','',$field=array('cid'=>$cid));
	}

	public function check_mobile($str){
		$config = $this->config;
		$preg = $config['mobile_rules'] ? $config['mobile_rules'] : "/^(13[0-9]|15[0-9]|17[0678]|18[0-9]|14[57])[0-9]{8}$/";
		if (!preg_match($preg, $str)) {
			return false;
		}
		return true;
	}

	public function check_email($str){
		$config = $this->config;
		$preg = $config['email_rules'] ? $config['email_rules'] : "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
		if (!preg_match($preg, $str)) {
			return false;
		}
		return true;		
	}


	public function delete_video($vid){
		return C::t("#wxz_live#wxz_live")->delete($vid);
	}

	public function get_order_byoid($oid){
		if (!$oid) {
			return false;
		}

		return C::t("#wxz_live#wxz_live_order")->fetch($oid);
	}

	public function order_video($oid,$buyer_uid){
		if (!$oid) {
			return array('code'=>'-5','msg'=>'oid_isnot_exixts');
		}
	  $o = C::t("#wxz_live#wxz_live_order")->fetch($oid);
	  if (empty($o)) {
	  	return array('code'=>'-5','msg'=>'cid_isnot_exixts');
	  }


	  return C::t("#wxz_live#wxz_live")->order_video($oid,$o['cid'],$buyer_uid);

	}

	public function get_order_by_out_trade_no($out_trade_no){
		return C::t("#wxz_live#wxz_live_order")->get_order_by_out_trade_no($out_trade_no);
	}

	public function selledsuccess($oid){
		 $o = $this->get_order_byoid($oid);
		
		 $oupdate = array(
			'ispayed'=>'1',
			'isselled'=>'1',
			'checknum'=>'1',
			'out_trade_no'=>$o['out_trade_no'],
		 	);

		 C::t("#wxz_live#wxz_live_order")->update($o['oid'],$oupdate);
	}

	public function  setsuccess($oid){
		if (!$oid) {
			return false;
		}

		 $oupdate = array(
			'ispayed'=>'1',
			'isselled'=>'1',
			'checknum'=>'1',
		 	);

		return  C::t("#wxz_live#wxz_live_order")->update($oid,$oupdate);
	}

	public function get_orders($start = 0, $limit = 0, $sort = '',$type = '',$field=array()){
		return C::t("#wxz_live#wxz_live_order")->get_type_order($start, $limit, $sort,$type,$field);
	}
	public function get_orders_fmt($start = 0, $limit = 0, $sort = '',$type = '',$field=array()){
		global $wxz_liveconf,$url;
		$url = ADMINSCRIPT.'?action='.$url;
		$orders = $this->get_orders($start, $limit, $sort,$type,$field);
		$return = array();

		foreach ($orders as $key => $value) { 
			$return[$key]['oid'] = $value['oid'];
			$return[$key]['cid'] = $value['cid'] > 0 ? $value['cid'] : '';
			$return[$key]['cname'] = '<a href="plugin.php?id=wxz_live:video&mod=video&cid='.$value['cid'].'" target="_blank">'.$value['course_name'].'</a>';
			$return[$key]['vid'] = $value['vid'] > 0 ? $value['vid'] : '';
			$return[$key]['ispayed'] = $value['ispayed']? lang('plugin/wxz_live', 'payed_success') : lang('plugin/wxz_live', 'payed_unsuccess');
			$return[$key]['course_price'] = intval($value['course_price']) / 100;
			$return[$key]['buyer_uid'] =  '<a href="home.php?mod=space&uid='.$value['buyer_uid'].'" target="_blank">'.$this->get_usernamebyuid($value['buyer_uid']).'</a>';
			$return[$key]['out_trade_no'] = $value['out_trade_no'];
			$return[$key]['dateline'] = date('Y-m-d',$value['dateline']);
			$return[$key]['pay_time'] = $value['pay_time'] ? date('Y-m-d H:i:s',$value['pay_time']) : $value['pay_time'];
			$return[$key]['orderstatus'] = $wxz_liveconf['orderstatus'][$value['status']];
			$acturl = '';
			if ($value['ispayed'] =='0') {
				$acturl .= '<a style="color:green;" href="'.$url.'&act=order&m=setpay&paystatus=1&oid='.$value['oid'].'&formhash='.FORMHASH.'">'.lang('plugin/wxz_live','pay_confirm').'</a>&nbsp;&nbsp;';
			}else{
				$acturl .= '<a style="color:red;" href="'.$url.'&act=order&m=setpay&paystatus=0&oid='.$value['oid'].'&formhash='.FORMHASH.'">'.lang('plugin/wxz_live','pay_cancel').'</a>&nbsp;&nbsp;';
			}
			if ($value['issign'] =='1' && $value['isconfirm'] =='0') {
				$acturl .= '<a style="color:green;" href="'.$url.'&act=order&m=setcontract&contractstatus=1&oid='.$value['oid'].'&formhash='.FORMHASH.'">'.lang('plugin/wxz_live','contract_confirm').'</a>&nbsp;&nbsp;';
			}else{
				$acturl .= '<a style="color:#cdcdcd;" >'.lang('plugin/wxz_live','contract_confirm').'</a>&nbsp;&nbsp;';
			}

			if ($value['issign'] =='1') {
				$acturl .= '<a style="color:red;" href="'.$url.'&act=order&m=setcontract&contractstatus=0&oid='.$value['oid'].'&formhash='.FORMHASH.'">'.lang('plugin/wxz_live','contract_cancel').'</a>&nbsp;&nbsp;';
			}else{
				$acturl .= '<a style="color:#cdcdcd;" >'.lang('plugin/wxz_live','contract_cancel').'</a>&nbsp;&nbsp;';
			}

			if ($value['issuccess'] =='0' && $value['isconfirm'] =='1') {
				$acturl .= '<a style="color:green;" href="'.$url.'&act=order&m=setmail&mailstatus=1&oid='.$value['oid'].'&formhash='.FORMHASH.'">'.lang('plugin/wxz_live','mail_confirm').'</a>&nbsp;&nbsp;';

			}else{
				$acturl .= '<a style="color:#cdcdcd;" >'.lang('plugin/wxz_live','mail_confirm').'</a>&nbsp;&nbsp;';
			}
			if ($value['ismail'] =='1') {
				$acturl .= '<a style="color:red;" href="'.$url.'&act=order&m=setmail&mailstatus=0&oid='.$value['oid'].'&formhash='.FORMHASH.'">'.lang('plugin/wxz_live','mail_cancel').'</a>&nbsp;&nbsp;';
			}else{
				$acturl .= '<a style="color:#cdcdcd;" >'.lang('plugin/wxz_live','mail_cancel').'</a>&nbsp;&nbsp;';
			}
			if ($value['order_type'] =='1') {
				$acturl .= '<a style="color:red;" href="'.$url.'&act=order&m=cleanplaycount&oid='.$value['oid'].'&formhash='.FORMHASH.'">'.lang('plugin/wxz_live','cleanplaycount').'</a>&nbsp;&nbsp;';
			}else{
				$acturl .= '<a style="color:#cdcdcd;" >'.lang('plugin/wxz_live','cleanplaycount').'</a>&nbsp;&nbsp;';
			}

			$acturl .= '<a href="'.$url.'&act=order&m=checkorder&oid='.$value['oid'].'&formhash='.FORMHASH.'">'.lang('plugin/wxz_live','checkorder_info').'</a>&nbsp;&nbsp;';

			$return[$key]['act'] = $acturl;
		}
		return $return;
	}

	public function cleanplaycount($oid){
		if (!$oid) {
			return false;
		}
		$o = $this->get_order_byoid($oid);
		if (empty($o)) {
			return false;
		}
		C::t("#wxz_live#wxz_live_order")->update($o['oid'],array('playcount'=>'0'));
		return array('code'=>'1','msg'=>'succeed');
	}

	public function order_setmail($oid,$status='1'){
		if (!$oid) {
			return false;
		}
		$o = $this->get_order_byoid($oid);
		if (empty($o)) {
			return false;
		}		
		if ($status =='1') {
			if ($o['ispayed'] =='0') {
				return array('code'=>'-2','msg'=>'this_order_is_not_payed_yet');
			}else if ($o['isclosed'] =='1') {
				return array('code'=>'-3','msg'=>'this_order_is_closed_before');
			}else if ($o['isconfirm'] =='0') {
				return array('code'=>'-3','msg'=>'this_order_isnot_isconfirm_yet');
			}else if ($o['issuccess'] =='1') {
				return array('code'=>'-3','msg'=>'this_order_is_issuccess_before');
			}

			C::t("#wxz_live#wxz_live_order")->update($oid,array('isconfirm'=>'1','ismail'=>$status,'issuccess'=>$status,'success_time'=>TIMESTAMP));
			$this->update_order_status_byoid($oid);
			return array('code'=>'1','msg'=>'succeed');
		}else if ($status =='0') {
			if ($o['ispayed'] =='0') {
				return array('code'=>'-2','msg'=>'this_order_is_not_payed_yet');
			}else if ($o['issign'] =='0') {
				return array('code'=>'-3','msg'=>'this_order_isnot_issign_yet');
			}else if ($o['isconfirm'] =='0') {
				return array('code'=>'-3','msg'=>'this_order_isnot_isconfirm_yet');
			}
			C::t("#wxz_live#wxz_live_order")->update($oid,array('ismail'=>$status,'issuccess'=>$status,'success_time'=>TIMESTAMP));
			$this->update_order_status_byoid($oid);
			return array('code'=>'1','msg'=>'succeed');
		}

		return array('code'=>'-1','msg'=>'none');
	}
	public function order_setcontract($oid,$status='1'){
		$config = $this->config;
		if (!$oid) {
			return false;
		}
		$o = $this->get_order_byoid($oid);
		if (empty($o)) {
			return false;
		}		
		if ($status =='1') {
			if ($o['ispayed'] =='0') {
				return array('code'=>'-2','msg'=>'this_order_is_not_payed_yet');
			}else if ($o['isclosed'] =='1') {
				return array('code'=>'-3','msg'=>'this_order_is_closed_before');
			}else if ($o['issign'] =='0') {
				return array('code'=>'-3','msg'=>'this_order_isnot_issign_yet');
			}else if ($o['isconfirm'] =='1') {
				return array('code'=>'-3','msg'=>'this_order_is_isconfirm_before');
			}

			//����Ƿ������ʵ����֤
			if (!$this->check_user_isverify($o['buyer_uid']) && $config['isverify']) {
				return array('code'=>'-3','msg'=>'this_user_isnot_finish_verify');
			}

			C::t("#wxz_live#wxz_live_order")->update($oid,array('issign'=>$status,'isconfirm'=>$status,'confirm_time'=>TIMESTAMP));
			$this->update_order_status_byoid($oid);
			return array('code'=>'1','msg'=>'succeed');
		}else if ($status =='0') {
			if ($o['ispayed'] =='0') {
				return array('code'=>'-2','msg'=>'this_order_is_not_payed_yet');
			}
			C::t("#wxz_live#wxz_live_order")->update($oid,array('ismail'=>'0','issuccess'=>'0','issign'=>$status,'isconfirm'=>$status,'confirm_time'=>TIMESTAMP));
			$this->update_order_status_byoid($oid);
			return array('code'=>'1','msg'=>'succeed');
		}

		return array('code'=>'-1','msg'=>'none');
	}
	public function order_setpay($oid,$status='1'){
		if (!$oid) {
			return false;
		}
		$o = $this->get_order_byoid($oid);
		if (empty($o)) {
			return false;
		}
		if ($status =='1') {
			if ($o['ispayed'] =='1') {
				return array('code'=>'-2','msg'=>'this_order_is_payed_before');
			}
			if ($o['isclosed'] =='1') {
				return array('code'=>'-3','msg'=>'this_order_is_closed_before');
			}

			C::t("#wxz_live#wxz_live_order")->update($oid,array('ispayed'=>$status,'pay_time'=>TIMESTAMP));
			$this->update_order_status_byoid($oid);
			return array('code'=>'1','msg'=>'succeed');
		}else if ($status =='0') {
			if ($o['ispayed'] =='0') {
				return array('code'=>'-2','msg'=>'this_order_is_not_payed_yet');
			}
			C::t("#wxz_live#wxz_live_order")->update($oid,array('ispayed'=>$status,'checknum'=>'1','pay_time'=>TIMESTAMP));
			$this->update_order_status_byoid($oid);
			return array('code'=>'1','msg'=>'succeed');
		}

		return array('code'=>'-1','msg'=>'none');
	}

	public function get_video_by_vid($vid){
		return C::t("#wxz_live#wxz_live")->fetch($vid);
	}

	public function set_selled($vid,$data=array()){
		if (!$vid) {
			return false;
		}
		$data = array(
		 	'isselled'=>'1',
		 	'buyer_uid'=>$data['buyer_uid'],
		 	'buyer_mobile'=>$data['buyer_mobile'],
		 	'buyer_email'=>$data['buyer_email'],
		 	);
		$rs = C::t("#wxz_live#wxz_live")->update($vid,$data);

		if ($rs) {
			$video = $this->get_video_by_vid($vid);
			
			$this->update_store_num($video['cid']);
			$this->add_kaselltimes($video['cid']);
		}
		
		return true;
	}



	public function sendemail($oid){
		include libfile('function/mail');
		$o = $this->get_order_byoid($oid);
		if ($o['buyer_uid']) {
			$username = $this->get_usernamebyuid($o['buyer_uid']);
		}
		$content = $this->config['emailnoticetemplate'];
		$content = str_replace('{email}', $o['buyer_email'], $content);
		$content = str_replace('{mobile}', $o['buyer_mobile'], $content);
		$content = str_replace('{video_sec}', '[[['.$o['video_sec'].']]]', $content);
		$content = str_replace('{uid}', $o['buyer_uid'], $content);
		if ($username) {
			$content = str_replace('{username}', $username, $content);
		}

		$succeed = sendmail($o['buyer_email'], $content , $_G['setting']['bbname']."\n\n\n$message");
		if ($succeed) {
			C::t("#wxz_live#wxz_live_order")->update($oid,array('issend'=>'1'));
		}
	}


	public function get_type_video_fmt($start = 0, $limit = 0, $sort = '',$type = '',$field){
		global $url;
		$url = ADMINSCRIPT.'?action='.$url;
		$videos = $this->get_type_video($start, $limit, $sort,$type,$field);
		$videos_fmt = array();
		foreach ($videos as $key => $value) {
			$videos_fmt[$key]['vid'] = $value['vid'];
			$videos_fmt[$key]['video_name'] = $value['video_name'];
			$videos_fmt[$key]['video_price'] = $value['video_price'] / 100;
			$videos_fmt[$key]['isfree'] = $value['isfree'];
			$videos_fmt[$key]['video_url'] = $value['video_url'];
			$videos_fmt[$key]['video_urltype'] = $value['video_urltype'];
			$videos_fmt[$key]['video_length'] = $value['video_length'];
			$videos_fmt[$key]['selltimes'] = $value['selltimes'];
			$videos_fmt[$key]['video_img'] = '<a href="'.$value['video_img'].'" target="_blank"><img src="'.$value['video_img'].'" width="40px" height="40px"></a>';
			$videos_fmt[$key]['dateline'] = date('Y-m-d H:i:s',$value['dateline']);
			$videos_fmt[$key]['act'] = '<a href="'.$url.'&act=adminvideo&m=edit&vid='.$value['vid'].'&set_selled=yes&formhash='.FORMHASH.'">'.lang('plugin/wxz_live', 'edit').'</a>&nbsp;&nbsp;<a href="'.$url.'&act=adminvideo&m=delete&vid='.$value['vid'].'&delete=yes&formhash='.FORMHASH.'">'.lang('plugin/wxz_live', 'delete').'</a>';
		}

		return $videos_fmt;
	}

	public function check_user_isverify($uid){
		global $_G;

		$verify_id = $this->get_verify_id();
		$uid = $uid ? $uid : $_G['uid'];
		if ($uid) {
			$verify = C::t('common_member_verify')->fetch($uid);
			if (!empty($verify)) {
				return intval($verify['verify'.$verify_id]);
			}
		}
		return false;
	}

	public function check_verify_issubmit($uid=''){
		$info = $this->get_member_verify_info($uid);
		if (is_array($info) && !empty($info) && $info['flag'] !=='-1'){
			return true;
		}
		return false;
	}

	public function get_member_verify_info($uid='',$verifytype='',$issuccess=false){
		global $_G;
		if ($issuccess !== false) {
			$flag = ' and flag = '.$issuccess.' ';
		}else{
			$flag = '';
		}
		$uid = $uid ? $uid : $_G['uid'];
		$verifytype = $verifytype ? $verifytype : $this->get_verify_id();

		$info = DB::fetch_first('select * from %t where verifytype = '.$verifytype.$flag.' and uid ='.$uid,array('common_member_verify_info'));
		$info['field'] = unserialize($info['field']);
		return $info;
	}

	public function get_realname_idcardinfo($uid){
		global $_G;
		$uid = $uid ? $uid : $_G['uid'];
		$config = $this->config;
		if (!$uid) {
			return false;
		}
		$info = $this->get_member_verify_info($uid);

		if (!$info['field'][$config['realname']] || !$info['field'][$config['idcard']]) {
			$userprofile = C::t("common_member_profile")->fetch($uid);
		}

		$realname = $userprofile[$config['realname']] ? $userprofile[$config['realname']] : $userprofile[$config['realname']];
		$idcard = $userprofile[$config['idcard']] ? $userprofile[$config['idcard']] : $userprofile[$config['idcard']];

		return $realname.' '.$idcard;
	}

	public function get_verify_id(){
		$verifyid = $this->config['verify'];
		return $verifyid ? $verifyid : '6';
	}

	public function get_vid_byverifyid($uid,$verifytype){
		global $_G;
		$uid = $uid ? $uid : $_G['uid'];
		$verifytype = $verifytype ? $verifytype : $this->get_verify_id();
		$v = $this->get_member_verify_info($uid,$verifytype);
		if (empty($v)) {
			return false;
		}
		return $v['vid'];
	}

	public function get_type_order($start = 0, $limit = 0, $sort = '',$type = '',$field=array()){
		return C::t("#wxz_live#wxz_live_order")->get_type_order($start, $limi, $sort,$type,$field);
	}

	public function update_order_status_byoid($oid){
		if (!$oid) {
			return false;
		}
		$o = $this->get_order_byoid($oid);
		if (empty($o)) {
			return '-1';
		}


		if ($o['isclosed'] =='1') {
			$status='7';
		}else if ($o['ispayed'] == '1' && $o['order_type'] == '0') {
			$status='1';
		}else if ($o['ispayed'] == '1' && $o['issign'] == '1' && $o['isconfirm'] == '1' && $o['ismail'] == '1' && $o['issuccess'] == '1' && $o['isclosed'] == '0') {
			$status='1';
		}else if ($o['ispayed'] == '1' && $o['issign'] == '1' && $o['isconfirm'] == '1' && $o['ismail'] == '1' && $o['issuccess'] == '0' && $o['isclosed'] == '0' && $o['order_type'] == '1') {
			$status='6';
		}else if ($o['ispayed'] == '1' && $o['issign'] == '1' && $o['isconfirm'] == '1' && $o['ismail'] == '0' && $o['issuccess'] == '0' && $o['isclosed'] == '0' && $o['order_type'] == '1') {
			$status='5';
		}else if ($o['ispayed'] == '1' && $o['issign'] == '1' && $o['isconfirm'] == '0' && $o['ismail'] == '0' && $o['issuccess'] == '0' && $o['isclosed'] == '0' && $o['order_type'] == '1') {
			$status='4';
		}else if ($o['ispayed'] == '1' && $o['issign'] == '0' && $o['isconfirm'] == '0' && $o['ismail'] == '0' && $o['issuccess'] == '0' && $o['isclosed'] == '0' && $o['order_type'] == '1') {
			$status='3';
		}else if ($o['ispayed'] == '0' && $o['issign'] == '0' && $o['isconfirm'] == '0' && $o['ismail'] == '0' && $o['issuccess'] == '0' && $o['isclosed'] == '0') {
			$status='2';
		}
		
		if ($status) {
			C::t("#wxz_live#wxz_live_order")->update($o['oid'],array('status'=>$status));
			$config = $this->config;
			if (!$config['issign'] && $status >= 3 && $status <=5) {
				$status = '1';
			}else if (!$config['isemail'] && $status > '5') {
				$status = '1';
			}

			return $status;
		}
		return '-2';
	}

	public function get_order_byoid_fmt($oid){
		global $wxz_liveconf;
		$o = $this->get_order_byoid($oid);
		if (empty($o)) {
			return false;
		}
		if ($o['buyer_uid']) {
			$o['buyer_username'] = '<a href="home.php?mod=space&uid='.$o['buyer_uid'].'" target="_blank">'.$this->get_usernamebyuid($o['buyer_uid']).'</a>';

		}

		$o['course_name'] = '<a href="plugin.php?id=wxz_live:video&mod=video&cid='.$o['cid'].'" target="_blank">'.$o['course_name'].'</a>';
		$o['course_img'] = '<a href="'.$o['course_img'].'" target="_blank">'.'<img src="'.$o['course_img'].'" width="40px" height="40px"></a>';
		$o['sign_img1'] = '<a href="'.$o['sign_img1'].'" target="_blank">'.'<img src="'.$o['sign_img1'].'" width="40px" height="40px"></a>';
		$o['sign_img2'] = '<a href="'.$o['sign_img2'].'" target="_blank">'.'<img src="'.$o['sign_img2'].'" width="40px" height="40px"></a>';
		$o['sign_img3'] = '<a href="'.$o['sign_img3'].'" target="_blank">'.'<img src="'.$o['sign_img3'].'" width="40px" height="40px"></a>';
		$o['vid'] = $o['vid'] ? $o['vid'] : '';
		$o['cid'] = $o['cid'] ? $o['cid'] : '';
		$o['video_price'] = lang('plugin/wxz_live','moneyunit_code').($o['video_price'] / 100 ).lang('plugin/wxz_live','moneyunit');
		$o['course_price'] = lang('plugin/wxz_live','moneyunit_code').($o['course_price'] / 100 ).lang('plugin/wxz_live','moneyunit');
		$o['total_fee'] = lang('plugin/wxz_live','moneyunit_code').($o['total_fee'] / 100 ).lang('plugin/wxz_live','moneyunit');
		$o['dateline'] = $o['dateline'] ? date("Y-m-d H:i:s",$o['dateline']) : '' ;
		$o['sign_time'] = $o['sign_time'] ? date("Y-m-d H:i:s",$o['sign_time']) : '' ;
		$o['pay_time'] = $o['pay_time'] ? date("Y-m-d H:i:s",$o['pay_time']) : '' ;
		$o['confirm_time'] = $o['confirm_time'] ? date("Y-m-d H:i:s",$o['confirm_time']) : '' ;
		$o['success_time'] = $o['success_time'] ? date("Y-m-d H:i:s",$o['success_time']) : '' ;
		$o['ispayed'] = $o['ispayed'] ? lang('plugin/wxz_live','payed_success') : lang('plugin/wxz_live','payed_unsuccess');
		$o['isselled'] = $o['isselled'] ? lang('plugin/wxz_live','learn_success') : lang('plugin/wxz_live','learn_unsuccess');
		$o['checknum'] = $o['checknum'] ? lang('plugin/wxz_live','checknum_success') : lang('plugin/wxz_live','checknum_unsuccess');
		$o['issign'] = $o['issign'] ? lang('plugin/wxz_live','issign_success') : lang('plugin/wxz_live','issign_unsuccess');
		$o['isconfirm'] = $o['isconfirm'] ? lang('plugin/wxz_live','isconfirm_success') : lang('plugin/wxz_live','isconfirm_unsuccess');
		$o['ismail'] = $o['ismail'] ? lang('plugin/wxz_live','ismail_success') : lang('plugin/wxz_live','ismail_unsuccess');
		$o['isclosed'] = $o['isclosed'] ? lang('plugin/wxz_live','isclosed_success') : lang('plugin/wxz_live','isclosed_unsuccess');
		$o['issuccess'] = $o['issuccess'] ? lang('plugin/wxz_live','issuccess_success') : lang('plugin/wxz_live','issuccess_unsuccess');
		$o['status'] = $wxz_liveconf['orderstatus'][$o['status']];
		return $o;
	}

	public function update_video_length($vid){
		$config = $this->config;
		if (!$vid) {
			return false;
		}
		$v = $this->get_video_by_vid($vid);
		if (empty($v)) {
			return false;
		}
		if ($v['video_urltype'] !== '1') {
			return;
		}
		//��Ҫ��д��� Access Key �� Secret Key
	    $accessKey = $config['qiniuaccessKey'];
	    $secretKey = $config['qiniusecretKey'];

	    // ������Ȩ����
	    $auth = new Qiniu_Auth($accessKey, $secretKey);
	    $f = $this->getfileexten(trim($v['video_url']));
	    //baseUrl�����˽�пռ������/key����ʽ
	    $baseUrl = $config['qiniuencdoeurl'].rawurlencode($this->auto_to_utf8($f['0'])).$f['1'].'?avinfo';
	    $authUrl = $auth->privateDownloadUrl($baseUrl);
	    $length = $this->get_duration_video($authUrl);
	    if ($length) {
	    	C::t("#wxz_live#wxz_live")->update($v['vid'],array('video_length'=>ceil($length)));
	    }
	}

	public function get_device_type(){
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$type = 'other';
		if(strpos($agent, 'iphone') || strpos($agent, 'ipad')){
			$type = 'ios';
		}else if(strpos($agent, 'android')){
			$type = 'android';
		}else if(strpos($agent, 'chrome')){
			$type = 'chrome';
		}else if(strpos($agent, 'safari')){
			$type = 'safari';
		}
		return $type;
	}

	public function get_private_videourl($vid){
		$config = $this->config;

		if (!$vid) {
			return false;
		}
		$v = $this->get_video_by_vid($vid);
		if (empty($v)) {
			return false;
		}
		if ($v['video_urltype'] == '9') {
			$v['video_url'] = wxz_livestrtoarray_tomobile($v['video_url']);

			if ($this->get_device_type() == 'ios' ||$this->get_device_type() == 'android' || $this->get_device_type() == 'safari') {
				return $v['video_url']['1'];
			}else{
				return $v['video_url']['0'];
			}
		}

		if ($v['video_urltype'] !== '1') {
			return $v['video_url'];
		}

		//��Ҫ��д��� Access Key �� Secret Key
	    $accessKey = $config['qiniuaccessKey'];
	    $secretKey = $config['qiniusecretKey'];

	    // ������Ȩ����
	    $auth = new Qiniu_Auth($accessKey, $secretKey);
	    $f = $this->getfileexten(trim($v['video_url']));


	    //baseUrl�����˽�пռ������/key����ʽ
	    $baseUrl = $config['qiniuencdoeurl'].rawurlencode($this->auto_to_utf8($f['0'])).$f['1'];
	    $authUrl = $auth->privateDownloadUrl($baseUrl);

	    if ($config['qiniuencdoeurl'] != $config['qiniuurl']) {
	    	$authUrl = str_replace($config['qiniuencdoeurl'], $config['qiniuurl'], $authUrl);
	    }

	    return $authUrl;
	}

	public function getfileexten($file){
		$tmp = explode('.',$file);
		if (empty($tmp) && count($tmp) < 2) {
			return false;
		}
		$ext = '.'.end($tmp);
		array_pop($tmp);
		$filename = implode('.', $tmp);
		return array($filename,$ext);
	}

	public function get_duration_video($url){
		$vinfo = $this->Curl($url);
		return $vinfo['streams']['0']['duration'];
	}

	public function Curl($url){
		$_G;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //����֤֤��
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //����֤֤��
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt ($ch, CURLOPT_REFERER, $_G['siteurl']);
		$resultj = curl_exec($ch);
		curl_close($ch); 

		return json_decode($resultj,true);
	}

	public function get_cat_by_cat_id($cat_id){
		if (!$cat_id) {
			return false;
		}
		return C::t("#wxz_live#wxz_live_cat")->fetch($cat_id);
	}

	public function get_cat_by_level($level='0',$order){
		return C::t("#wxz_live#wxz_live_cat")->get_cat_by_level($level,$order);
	}

	public function get_touch_swiper($updateCache=false){

		return $this->ZmsGetFromCache('touch_swiper');
		die;

	}

	public function get_touch_best(){
		return $this->ZmsGetFromCache('touch_best');
	}

	public function update_touch_bestcache_init($touch_best=array()){
		if (!empty($touch_best)) {
			foreach ($touch_best as $key => $value) {
				$c = $this->get_course_bycid($value['cid']);
				if ($c['cat_id']) {
					$cat = $this->get_cat_by_cat_id($c['cat_id']);
				}
				$user = getuserbyuid($c['uid']);
				unset($user['password']);

				if (!empty($cat)) {
					$touch_best[$key] = array_merge($user,$value,$cat,$c);
				}else{
					$touch_best[$key] = array_merge($user,$value,$c);
				}
			}


		}
		$this->ZmswriteToCache('touch_best',$touch_best);
	}

	public function update_touch_swipercache_init($swiper){
		global $wxz_liveconf;
		if (is_array($swiper) && !empty($swiper)) {
			$touch_swiper = $swiper;
		}else{
			$touch_swiper = $wxz_liveconf['touch']['swiper'];
		}
		
		$this->ZmswriteToCache('touch_swiper',$touch_swiper);

	}
}

?>