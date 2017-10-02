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
// C::import('wxz_live','plugin/wxz_live/source/class');

class plugin_wxz_live {

}

class plugin_wxz_live_home extends plugin_wxz_live{
	function spacecp_profile_extra(){
		if ($_GET['return_url'] && $_GET['return_url']) {
			return '<input type="hidden" name="referer" value="'.dreferer().'"><input type="hidden" name="return_url" value="'.urlencode($_GET['return_url']).'">';
		}
		return;
			// $video = new wxz_live();
			// if ($video->check_verify_issubmit() && $_GET['referer'] && $_GET['return_url']) {
			// 	$rurl = $_GET['referer'] && $_GET['return_url'] ? $_GET['referer'].'&isverifysubmit=yes&return_url='.$_GET['return_url'] : '';
			// 	$this->_wxz_showsuccess(lang('plugin/wxz_live','verifytips_submitsuccess'),$rurl);
			// }
	}

}


?>