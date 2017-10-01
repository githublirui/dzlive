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
// C::import('zhanmishu_video','plugin/zhanmishu_video/source/class');

class plugin_zhanmishu_video {

}

class plugin_zhanmishu_video_home extends plugin_zhanmishu_video{
	function spacecp_profile_extra(){
		if ($_GET['return_url'] && $_GET['return_url']) {
			return '<input type="hidden" name="referer" value="'.dreferer().'"><input type="hidden" name="return_url" value="'.urlencode($_GET['return_url']).'">';
		}
		return;
			// $video = new zhanmishu_video();
			// if ($video->check_verify_issubmit() && $_GET['referer'] && $_GET['return_url']) {
			// 	$rurl = $_GET['referer'] && $_GET['return_url'] ? $_GET['referer'].'&isverifysubmit=yes&return_url='.$_GET['return_url'] : '';
			// 	$this->_zms_showsuccess(lang('plugin/zhanmishu_video','verifytips_submitsuccess'),$rurl);
			// }
	}

}


?>