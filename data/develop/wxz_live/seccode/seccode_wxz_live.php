<?php
/**
 *	[微小智千聊直播(wxz_live.seccode_wxz_live)] (C)2017-2099 Powered by wxz-合肥微小智.
 *	Version: 1.0.0
 *	Date: 2017-10-2 18:56
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class seccode_wxz_live {

	public $version = '1.0.0';
	public $name = 'wxz_live';
	public $description = '';
	public $copyright = '<a href="http://www.comsenz.com" target="_blank">Comsenz Inc.</a>';
	public $customname = '';

	/**
	 * 检查输入的验证码，返回 true 表示通过
	 */
	public function check($value, $idhash) {
		//TODO - Insert your code here
	}

	/**
	 * 输出验证码，echo 输出内容将显示在页面中
	 */
	public function make() {
		//TODO - Insert your code here
	}
}
?>