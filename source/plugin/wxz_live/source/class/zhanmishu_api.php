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

class zhanmishu_api{
	public function getuserbyuid($uid, $fetch_archive = 0,$isprofile=false){
		global $_G;
		$uid = $uid ? $uid : $_G['uid'];
		$member = getuserbyuid($uid, $fetch_archive);

		if ($isprofile && $_G['uid']) {
			$profile = C::t("common_member_profile")->fetch($uid);
			$member = array_merge($profile,$member);
		}

		unset($member['password']);

		return $member;
	}


}

/*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
class zhanmishu_video_api extends zhanmishu_api
{
	
	
}