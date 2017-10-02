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
$input = daddslashes($_GET);

$video = new zhanmishu_video();
$config = $video->config;

if ($config['drmapi'] =='2') {
	
  
	$name=$input['uid']; //获得用户名
	$pwd=$_GET['pwd']; //获得密码

	$pwd = $pwd ? $pwd : $_GET['_php?pwd'];
	 
	$fileid=$_GET['pid']; //获得文件编号
	$code=$_GET['code']; // 获得机器码

	$randstr=$_GET['str'];// 获得每次认证的随机数
 
 // 然后你判断用户名和密码是否正确，判断用户有无换电脑，判断用户有无权限播放等等业务处理代码


	if ($name) { 
		//检测用户名密码是否有误

		if (substr($name, 0,3) == '|||') {
			$namestr = substr($name, 3);
			$namearr = explode('|||', $namestr);

			$nametemp = '';
			foreach ($namearr as $key => $value) {
				if ($value <= 256 && $value > 0) {
					$nametemp .= chr($value);
				}else if ($value > 256) {
						$nametemp .=diconv(u2utf8($value),'UTF-8',CHARSET);
				}
			}

			$name = $nametemp;

		}

		loaducenter();
		$result = uc_user_login($name,$pwd,0);
		if ($result[0] < 1) {
			$result = uc_user_login($name,$pwd,1);
		}


		if ($result[0] < 1 && !$_G['uid']) {
			echo lang('plugin/zhanmishu_video','drm_user_or_password_error');exit;
		}

		$member = getuserbyuid($result[0], 1);
		require_once libfile('function/member');
		$cookietime = 1296000;
		setloginstatus($member, $cookietime);
		dsetcookie('lip', $_G['member']['lastip'].','.$_G['member']['lastvisit']);
		C::t('common_member_status')->update($_G['uid'], array('lastip' => $_G['clientip'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));


		//check yourproductid
		$c = $video->get_one_course_byfield(array('cid'=>$fileid));
		if (empty($c)) {
			echo lang('plugin/zhanmishu_video','drm_filepid_error');exit;
		}

		$ispay = $video->checkuser_isfinish_course($fileid,$_G['uid'],false,false);
		$o = $video->get_order_byoid($ispay);


		$isvip = check_isvip($c);

		if (!$ispay && !$isvip) {
			echo lang('plugin/zhanmishu_video','drm_have_not_brought');exit;
		}

		$time = date('Y-m-d',TIMESTAMP + 180*24*3600);
		
		$fileid = array();
		$field['uid'] = $_G['uid'];
		//$fileid['device'] = $input['code'];
		$field['cid'] = $c['cid'];
		$field['isautho'] = '1';
		$authonum = C::t("#zhanmishu_video#zhanmishu_video_autho")->get_autho_num_byuidcid($c['cid'],$_G['uid'],$input['code'],'1');
		if ($authonum['num'] >=3) {
			echo lang('plugin/zhanmishu_video', 'drm_have_not_getlicstore_times').$authonum['num'];exit;
		}

		echo 'AAAAAA274E052AD619232D026E602F16B92318|'.'20|'.'2028-11-11'.'||'.md5($_GET['code']);
		$ip_array = explode('.', $_G['clientip']);
		$autho=array(
			'cid'=>$c['cid'],
			'uid'=>$_G['uid'],
			'isautho'=>'1',
			'dateline'=>TIMESTAMP,
			'device'=>$input['code'],
			'type'=>$isvip ? '2' : '1',
		);
		array_merge($ip_array,$autho);

		C::t("#zhanmishu_video#zhanmishu_video_autho")->insert($autho,false,false);

		exit;
	}else{
		echo 'data_error';
	}
	exit;
}else if ($config['drmapi'] == '1') {
		
	require_once libfile('function/member');
	$act = $input['act'] ? $input['act'] : 'login';
	if ($act == 'login') {
		if (submitcheck('drmloginsubmit')) {
			$name = $input["username"];
			$pwd = $input["password"]; 

			//$name =diconv($_GET['username'],'UTF-8',CHARSET);

			//检测用户名密码是否有误
			loaducenter();
			$result = uc_user_login($name,$pwd,0);

			if ($result[0] < 1 && !$_G['uid']) {
				echo '<br />'.lang('plugin/zhanmishu_video', 'drm_user_or_password_error');
				echo '<script>alert("'.lang('plugin/zhanmishu_video', 'drm_user_or_password_error').'");window.location="plugin.php?id=zhanmishu_video:video&mod=drm";</script>';exit;
			}

			$member = getuserbyuid($result[0], 1);
			require_once libfile('function/member');
			$cookietime = 1296000;
			setloginstatus($member, $cookietime);
			dsetcookie('lip', $_G['member']['lastip'].','.$_G['member']['lastvisit']);
			C::t('common_member_status')->update($_G['uid'], array('lastip' => $_G['clientip'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));
		
			echo '<br />'.lang('plugin/zhanmishu_video', 'drm_licstore_is_coming');
			echo "<script>window.location=\"licstoreing.php\";</script>"; 
			
			exit;
		}
		//clearcookies();
		dsetcookie('ProfileID',$input["profileid"],3600);
		dsetcookie('ClientInfo',$input["clientinfo"],3600);
		dsetcookie('Platform',$input["platform"],3600);
		dsetcookie('ContentType',$input["contenttype"],3600);
		dsetcookie('Version',$input["version"],3600);
		dsetcookie('yourproductid',$input["yourproductid"],3600);


		include template('zhanmishu_video:drm/login');
		exit;
	}


	$adminemail = $config['haihaiusername'];
	$adminpwd = $config['haihaisec'];

	$ProfileID 		= 	daddslashes(getcookie('ProfileID'));
	$ClientInfo 	= 	daddslashes(getcookie('ClientInfo'));
	$Platform 		= 	daddslashes(getcookie('Platform'));
	$contenttype 	= 	daddslashes(getcookie('ContentType'));
	$Version 		= 	daddslashes(getcookie('Version'));
	$yourproductid 	= 	daddslashes(getcookie('yourproductid'));
	$name 			= 	$_G['username'];

	if (!$_G['uid']) {
		echo '<script>alert("'.lang('plugin/zhanmishu_video', 'drm_please_login').'");</script>';exit;
	}


	//check yourproductid

	$c = $video->get_one_course_byfield(array('ProfileID'=>$ProfileID));
	if (empty($c)) {

		echo '<br />'.lang('plugin/zhanmishu_video', 'drm_licstore_error');
		echo '<script>alert("'.lang('plugin/zhanmishu_video', 'drm_licstore_error').'");</script>';exit;

	}

	$yourproductid = $yourproductid ? $yourproductid : $c['cid'];

	$ispay = $video->checkuser_isfinish_course($yourproductid,$_G['uid']);
	$o = $video->get_order_byoid($ispay);

	$isvip = check_isvip($c);
	$wotarname = $video->get_realname_idcardinfo();
	$wotarname = $wotarname ? $wotarname : $name;

	if ($act =='licstoreing' ) {
		if (!$ispay && !$isvip) {
			echo "<br />".lang('plugin/zhanmishu_video', 'drm_have_not_brought');
			echo '<script>alert("'.lang('plugin/zhanmishu_video', 'drm_have_not_brought').'");</script>';exit;
		}

		//检测是否已经完成认证
		if (!$video->check_user_isverify() && $config['isverify']) {
			echo "<br />".lang('plugin/zhanmishu_video', 'drm_have_not_verify');
			echo '<script>alert("'.lang('plugin/zhanmishu_video', 'drm_have_not_verify').'");</script>';exit;
		}

		if ($ispay && ($o['playcount'] >= $config['maxplaycount'] || empty($o))) {
			echo "<br />".lang('plugin/zhanmishu_video', 'drm_have_not_getlicstore_times');
			echo '<script>alert("'.lang('plugin/zhanmishu_video', 'drm_have_not_getlicstore_times').'");</script>';exit;
		}

		$num = C::t("#zhanmishu_video#zhanmishu_video_autho")->get_authos_num(array('uid'=>$_G['uid'],'cid'=>$c['cid']));




		if (strlen($wotarname) < 3 && false) {
			echo "<br />".lang('plugin/zhanmishu_video', 'drm_watermark_error');
			echo '<script>alert("'.lang('plugin/zhanmishu_video', 'drm_watermark_error').'");</script>';exit;
		}
		 echo "<SCRIPT language=JavaScript>location='licstore.php';</SCRIPT>";
		 die;

	}else if ($act =='licstore') {

		if (!$ispay && !$isvip) {
			exit;
		}

		//检测是否已经完成认证
		if (!$video->check_user_isverify() && $config['isverify']) {
			exit;
		}

		if ($ispay && ($o['playcount'] >= $config['maxplaycount'] || empty($o))) {
			exit;
		}

		$num = C::t("#zhanmishu_video#zhanmishu_video_autho")->get_authos_num(array('uid'=>$_G['uid'],'cid'=>$c['cid']));




		if (strlen($wotarname) < 3 && false) {
			exit;
		}


		$Description = 'storfx';
		$PlayCount = '-1';
		$rightsID = '23106';
		$groupid = $config['haihaigroupid'];

		$orderDate ='2016/1/16';
		$ExpirationDate ='2028/1/16';
		$ExpirationAfterFirstUse ='7200';

		$wsdl="http://3.drm-x.com/haihaisoftlicenseservice.asmx?wsdl";
		$paramupdateright = array(
			'AdminEmail' 		=> $adminemail,
			'WebServiceAuthStr' => $adminpwd,
			'RightsID' 			=> $rightsID,
			'Description'		=> $Description,
			'PlayCount' 		=> $PlayCount,
			'BeginDate' 		=> $orderDate,
			'ExpirationDate' 	=> $ExpirationDate,
			'ExpirationAfterFirstUse'=> $ExpirationAfterFirstUse,
			'RightsPrice' 		=> $c['course_price'] ? $c['course_price'] : '1',
			'AllowPrint' 		=> "False",
			'AllowClipBoard' 	=> "False",
			'AllowDoc' 			=> "True",
			'EnableWatermark' 	=> "True",
			'WatermarkText' 	=> $wotarname.' '.$wotarname, //水印文字
			'WatermarkArea' 	=> "1,2,3,4,5,",  //水印区域
			'RandomChangeArea' 	=> "True",//随机水印
			'RandomFrquency' 	=> 3, 
			'EnableBlacklist' 	=> "True",
			'EnableWhitelist' 	=> "False",
			'DisableVM' 	=> "True",
			'FontSize' 	=>200,
			'CheckServerTime' 	=> "True"
		);
		$client=new soapclient2($wsdl, 'wsdl');		// PHP version 5.3
		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8 = false;

		$paramcheckuser = array(
			'UserName' => $name,
			'AdminEmail' 		=> $adminemail,
			'WebServiceAuthStr' 		=> $adminpwd
			
		);	
		//CheckUserExists
		$resultcheck = $client->call('CheckUserExists', array('parameters' => $video->auto_to_utf8($paramcheckuser)), '', '', true, true);

		if ($resultcheck['CheckUserExistsResult'] !== 'True') {
			$email = getuserprofile('email');
			$mobile = getuserprofile('mobile');
			$paramAddNewUser = array(
				'AdminEmail' 		=> $adminemail,
				'WebServiceAuthStr' 		=> $adminpwd,
				'GroupID' => $groupid,
				'UserLoginName' => $name,
				'UserPassword' => 'N/A',
				'UserEmail' =>$email,
				'UserFullName' => $name,
				'Title' => 'user',
				'Company' => 'N/A',
				'Address' => 'N/A',
				'City' => 'N/A',
				'Province' => 'N/A',
				'ZipCode' => '000000',
				'Phone' => $mobile,
				'CompanyURL' => 'N/A',
				'SecurityQuestion' => '0',
				'SecurityAnswer' => 'N/A',
				'IP' => $_G['clientip'],
				'Money' => '0',
				'BindNumber' => '1',
				'IsApproved' => 'True',
				'IsLockedOut' =>'False'
			);
			//AddNewUser
			$resultadd = $client->call('AddNewUser', array('parameters' => $video->auto_to_utf8($paramAddNewUser)), '', '', true, true);
		}

		$result1 = $client->call('UpdateRightMore', array('parameters' => $video->auto_to_utf8($paramupdateright)), '', '', true, true);

		$param = array(
			'AdminEmail' =>  $adminemail,
			'WebServiceAuthStr' => $adminpwd,
			'ProfileID' =>$ProfileID,
			'ClientInfo' =>$ClientInfo,
			'RightsID' =>$rightsID,
			'UserLoginName' => $name,
			'UserFullName' => 'N/A',
			'GroupID'=>$groupid,
			'Message' => 'N/A',
			'IP' => 'N/A',
			'Platform' =>$Platform,
			'ContentType' =>$ContentType,
			'Version' =>$Version,//如果您的DRM帐号是增强安全模式，请去掉前面的注释
		);

		//DRM-X 3.0标准模式请使用getLicenseRemoteToTable方法
		// $result = $client->__call('getLicenseRemoteToTable', array('parameters' => $param));
		// $license = $result->getLicenseRemoteToTableResult;


		//DRM-X 3.0增强安全模式 请使用getLicenseRemoteToTableWithVersion方法
		// $client = new SoapClient($wsdl, array('trace' => false,'soap_version'   => SOAP_1_1));
		$result = $client->call('getLicenseRemoteToTableWithVersion', array('parameters' =>  $video->auto_to_utf8($param)));


		//$license = $result->getLicenseRemoteToTableWithVersionResult;
		$license= $result['getLicenseRemoteToTableWithVersionResult'];
		clearcookies();
		$message = $result['Message'];


		if($license == "ERROR:EXCEED_BIND"){
		 echo "<SCRIPT language=JavaScript>location='LicErrorExceedBind.html';</SCRIPT>";
		}

		if (!empty($o)) {
			C::t("#zhanmishu_video#zhanmishu_video_order")->update($o['oid'],array('playcount'=>$o['playcount'] + 1));
		}

		$ip_array = explode('.', $_G['clientip']);
		$autho=array(
			'cid'=>$c['cid'],
			'uid'=>$_G['uid'],
			'isautho'=>'1',
			'dateline'=>TIMESTAMP,
			'type'=>$isvip ? '2' : '1',
			'oid'=>isset($o['oid']) ? $o['oid'] : ''
		);
		array_merge($ip_array,$autho);

		C::t("#zhanmishu_video#zhanmishu_video_autho")->insert($autho,false,false);


		print_r('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head><body>'. $license.'</body></html> ');
	}

}

function check_isvip($course){
	global $_G,$config;
	$extgroups = explode("\t", $_G['member']['extgroupids']);
	$course_group = array_filter(explode('#', trim($course['course_group'])));
	$extgroups[] = $_G['groupid'];
	$vgroup = array_intersect($extgroups, array_keys($config['vipgroup']));

	$course_groupin = array_intersect($extgroups, $course_group);

	if (!empty($course_groupin)) {
		$course['ispay'] = '1';
	}else{
		foreach ($vgroup as $value) {
			if (($course['course_price'] / 100) <= $config['vipgroup'][$value]) {
				$course['ispay'] = '1';
				break;
			}
		}

	}
	return $course['ispay'];
}

function u2utf8($c) {    
    $str="";    
    if ($c < 0x80) $str.=$c;    
    else if ($c < 0x800) {    
        $str.=chr(0xC0 | $c>>6);    
        $str.=chr(0x80 | $c & 0x3F);    
    } else if ($c < 0x10000) {    
        $str.=chr(0xE0 | $c>>12);    
        $str.=chr(0x80 | $c>>6 & 0x3F);    
        $str.=chr(0x80 | $c & 0x3F);    
    } else if ($c < 0x200000) {    
        $str.=chr(0xF0 | $c>>18);    
        $str.=chr(0x80 | $c>>12 & 0x3F);    
        $str.=chr(0x80 | $c>>6 & 0x3F);    
        $str.=chr(0x80 | $c & 0x3F);    
    }  
    return $str;    
} 

?>